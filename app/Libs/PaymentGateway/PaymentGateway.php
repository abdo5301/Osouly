<?php

namespace App\Libs\PaymentGateway;

use App\Models\Gateway;

use App\Models\Transaction;
use Exception;
use Requests;
use Requests_Hooks;

class PaymentGateway{

    /*
     * Mastercard APIs
     */
    private static $merchantID  = 'EGPTEST1',
                   $APIPassword = '61422445f6c0f954e24c7bd8216ceedf',
                   $APIVersion  = '57';

    /*
     * Config
     * $sessionTimeOut in minutes
     */
    private static $sessionTimeOut = 5;

    /*
     * Lib Data
     */
    private static $internalMerchants = [];

    public static function makeRequest($type,...$data){

        $mainURL = 'https://test-nbe.gateway.mastercard.com/api/rest/version/'.self::$APIVersion.'/merchant/'.self::$merchantID;

        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_request', function($handle){
            curl_setopt($handle, CURLOPT_FAILONERROR, true);
            curl_setopt($handle, CURLOPT_VERBOSE, true);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HEADER , false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_ENCODING,  '');
            curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($handle, CURLOPT_USERPWD, 'merchant.'.self::$merchantID.':'.self::$APIPassword);
        });


        try{

            switch ($type){
                case 'start_sesstion':
                    $response = Requests::post(
                        $mainURL.'/session',
                        ['Content-Type' => 'application/json'],
                        [],
                        ['hooks'=> $hooks,'timeout'=>10000,'connect_timeout'=>10000]
                    );

                    $result = json_decode($response->body);

                    if($result->result != 'SUCCESS'){
                        return [
                            'status'=> false,
                            'reason'=> $result->result
                        ];
                    }

                    return [
                        'status'=> true,
                        'reason'=> $result->result,
                        'session_id'=> $result->session->id,
                        'aes256Key'=> $result->session->aes256Key
                    ];

                    break;


                case 'check_3Ds_enrollment':
                    // 0 Transaction ID
                    // 1 Session ID
                    // 2 URL Response
                    // 3 Amount
                    // 4 Currency

                    $response = Requests::put(
                        $mainURL.'/3DSecureId/'.$data[0],
                        ['Content-Type' => 'application/json'],
                        json_encode([
                            'apiOperation'=> 'CHECK_3DS_ENROLLMENT',
                            'session'=> [
                                'id'=> $data[1]
                            ],
                            '3DSecure'=> [
                                'authenticationRedirect'=> [
                                    'responseUrl'=> $data[2]
                                ]
                            ],
                            'order'=> [
                                'amount'=> $data[3],
                                'currency'=> $data[4]
                            ]
                        ]),
                        ['hooks'=> $hooks,'timeout'=>10000,'connect_timeout'=>10000]
                    );

                    $result = json_decode($response->body);

                    return [
                        'status'=> true,
                        'html_response'=> $result->{'3DSecure'}->authenticationRedirect->simple->htmlBodyContent,
                    ];

                    break;


                case 'process_acs_result':
                    // 0 Transaction ID
                    // 1 Session ID
                    // 2 URL Response
                    // 3 Amount
                    // 4 Currency


                    $response = Requests::post(
                        $mainURL.'/3DSecureId/'.$data[0],
                        ['Content-Type' => 'application/json'],
                        json_encode([
                            'apiOperation'=> 'PROCESS_ACS_RESULT',
                            '3DSecure'=> [
                                'paRes'=> $data[1]
                            ]
                        ]),
                        ['hooks'=> $hooks,'timeout'=>10000,'connect_timeout'=>10000]
                    );

                    $result = json_decode($response->body);

                    return [
                        'status'=> true,
                        'veResEnrolled'=> @$result->{'3DSecure'}->veResEnrolled,
                        'xid'=> @$result->{'3DSecure'}->xid,
                        'gatewayRecommendation'=> $result->response->gatewayRecommendation,
                    ];

                    break;


                case 'pay':

                    $response = Requests::put(
                        $mainURL.'/order/'.$data[0].'/transaction/'.$data[0],
                        ['Content-Type' => 'application/json'],
                        json_encode([
                            'apiOperation'=> 'PAY',
                            'order'=> [
                                'amount'=> $data[2],
                                'currency'=> $data[3]
                            ],
                            'session'=> [
                                'id'=> $data[1]
                            ],
                            '3DSecureId'=> $data[0]
                        ]),
                        ['hooks'=> $hooks,'timeout'=>10000,'connect_timeout'=>10000]
                    );



                    $result = json_decode($response->body);

                    if($result->result == 'SUCCESS'){
                        return ['status'=> true,'response'=> $result];
                    }else{
                        return ['status'=> false,'response'=> $result];
                    }


                    break;
            }

        }catch (Exception $exception){
            echo $exception->getMessage().'-'.$exception->getFile().'-'.$exception->getLine().'-'.$exception->getCode();

            return ['status'=> false,'timeout'=> true];
        }
    }

    public static function startSession($User,$amount,$currency,$ip,$userAgent,$locale,$description = null){
        $session = self::makeRequest('start_sesstion');
        if(!$session['status']){
            $session = self::makeRequest('start_sesstion');
            if(!$session['status']){
                return [
                    'status'=> false,
                    'error_code'=> 1
                ];
            }
        }


        $totalAmount = self::calculateTotalAmount($amount);

        $transaction = Transaction::create([
            'session_id'=> $session['session_id'],
            'token'=> str_random(19).time().str_random(25),
            'amount'=> $amount,
            'total_amount'=> $totalAmount,
            'browser_user_agent'=> $userAgent,
            'ip'=> $ip,
            'locale'=> $locale,
            'description'=> $description,
            'aes256Key'=> $session['aes256Key']
        ]);


        if(!$transaction){
            return [
                'status'=> false,
                'error_code'=> 2
            ];
        }


        return [
            'status'            => true,
            'token'             => $transaction->token,
            'transactions_id'   => $transaction->id,
            'session_id'        => $transaction->session_id,
        ];

    }

    public static function check3DS($transactionID,$url){

        $transaction = Transaction::where([
            ['id',$transactionID],
//            ['status','new']
        ])->first();

        if(!$transaction){
            return [
                'status'=> false,
                'error_code'=> 1
            ];
        }

        $transaction->update([
            'status'=> 'processing'
        ]);

        $request = self::makeRequest('check_3Ds_enrollment',$transaction->id,$transaction->session_id,$url,$transaction->total_amount,'EGP');
//dd('ddd');
        if($request['status']){
            return $request;
        }else{
            return [
                'status'=> false,
                'error_code'=> 2
            ];
        }

    }

    public static function pay($transactionID,$PaRes){

        $transaction = Transaction::where([
            ['id',$transactionID],
            ['status','processing']
        ])->first();

        if(!$transaction){
            return [
                'status'=> false,
                'error_code'=> 1
            ];
        }

        $process3DS = self::makeRequest('process_acs_result',$transaction->id,$PaRes);
        if(!$process3DS['status']){
            return [
                'status'=> false,
                'error_code'=> 2
            ];
        }


        if($process3DS['gatewayRecommendation'] != 'PROCEED'){
            return [
                'status'=> false,
                'error_code'=> 3
            ];
        }

        $pay = self::makeRequest('pay',$transaction->id,$transaction->session_id,$transaction->total_amount,'EGP');

        if(!$pay['status']){
            if(isset($pay['timeout']) && $pay['timeout'] == true){
                $transaction->update([
                    'status'=> 'pending'
                ]);

                return [
                    'status'=> false,
                    'error_code'=> 4
                ];

            }else{
                $transaction->update([
                    'status'=> 'rejected',
                    'full_response'=> serialize($pay)
                ]);

                return [
                    'status'=> false,
                    'error_code'=> 5
                ];
            }
        }else{

            return [
                'status'=> true,
                'error_code'=> 1
            ];

        }

    }

    public static function calculateTotalAmount($amount){
        if(!$amount){
            return false;
        }

        $additionalAmount = (($amount*2)/100)+2.5;

        if($additionalAmount < 3){
            $additionalAmount = 3;
        }
        
        return $amount+$additionalAmount;
    }



/*
    private static function getMerchant($id){
        if(isset(self::$internalMerchants[$id])){
            return self::$internalMerchants[$id];
        }

        $data = Merchant::where([
            ['id',$id],
            ['status','active']
        ])->first();

        if($data){
            self::$internalMerchants[$id] = $data;
            return self::$internalMerchants[$id];
        }

        return false;
    }


    public static function startSessiondddd($merchantID,$merchantTransactionID,$amount,$currency,$userAgent){
        $request = self::makeRequest('start_sesstion');
        if(!$request['status']){
            return [
                'status'=> false,
                'msg'   => __('Unable to create session')
            ];
        }

        $merchant = self::getMerchant($merchantID);
        if(!$merchant){
            return [
                'status'=> false,
                'msg'   => __('Unable to get merchant information')
            ];
        }

        $checkTransaction = GatewayTransaction::where([
            ['merchant_id', $merchant->id],
            ['merchant_transaction_id', $merchantTransactionID]
        ])->first();

        if($checkTransaction){
            return [
                'status'=> false,
                'msg'=> __('Duplicate transaction')
            ];
        }

        if($request['response']->result == 'SUCCESS'){
            $transaction = GatewayTransaction::create([
                'session_id'                => $request['response']->session->id,
                'merchant_transaction_id'   => $merchantTransactionID,
                'merchant_id'               => $merchantID,
                'amount'                    => $amount,
                'currency'                  => $currency,
                'browser_user_agent'        => $userAgent,
                'ip'                        => getRealIP()
            ]);
            return [
                'status'=> true,
                'transaction'=> $transaction
            ];
        }

        return ['status'=> false];

    }

    public static function makePayment($transactionID = null){
        if(!is_null($transactionID)){
            $transaction = GatewayTransaction::find($transactionID);
            if(!$transaction){
                return [
                    'status'=> false,
                    'msg'=> __('Sorry We can\'t get transaction information')
                ];
            }elseif($transaction->status != 'new'){
                return [
                    'status'=> false,
                    'msg'=> __('Unknown Error')
                ];
            }elseif($transaction->created_at->diffInMinutes(Carbon::now()) > self::$sessionTimeOut){
                $transaction->update([
                    'status'=> 'timeout'
                ]);

                return [
                    'status'=> false,
                    'msg'=> __('Session timeout')
                ];
            }

            $request = self::makeRequest('pay',$transaction->id,$transaction->session_id,$transaction->amount,$transaction->currency);

            if(!$request['status']){
                $transaction->update([
                    'status'=> 'pending'
                ]);

                return [
                    'status'=> false,
                    'msg'=> __('Payment is pending')
                ];
            }


            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE
            // --- Success NOT DONE

        }else{

        }

    }


    public static function makeTransaction($User,$gatewayID,$amount,$callbackUrl,$currency = 'EGP'){
        $gateway = Gateway::where([
            ['id',$gatewayID],
            ['status','active']
        ])
            ->first();

        if(!$gateway){
            return [
                'status'=> false,
                'message'=> __('Unable to get payment gateway'),
                'url'=> null,
                'transaction_id'=> 0
            ];
        }

        $gatewayObject = $gateway->driver_path;

        $transaction = GatewayTransaction::create([
            'gateway_id'=> $gateway->id,
            'creatable_id'=> $User->id,
            'creatable_type'=> $User->modelPath,
            'amount'=> $amount
        ]);

        $url = $gatewayObject::getURL($amount,$callbackUrl,$currency,$transaction->id);

        if($url['status']){
            return [
                'status'=> true,
                'message'=> $url['message'],
                'url'=> $url['url'],
                'transaction_id'=> $transaction->id
            ];
        }else{
            $transaction->update(['status'=> 'canceled']);

            return [
                'status'=> true,
                'message'=> $url['message'],
                'url'=> null,
                'transaction_id'=> $transaction->id
            ];
        }

    }
    public static function callback($walletType,$transactionID,$data){
        $transaction = GatewayTransaction::where([
            ['id',$transactionID],
            ['status','pending']
        ])->first();

        if(!$transaction){
            return [
                'status'=> false,
                'message'=> __('Unable to get transaction data')
            ];
        }

        $User = $transaction->creatable;

        $transaction->update(['status'=> 'processing']);

        $gateway        = Gateway::find($transaction->gateway_id);
        $walletObject   = WalletData::getWalletByUserData($User->modelPath,$User->id,$walletType);

        if(!$gateway || !$walletObject){
            $transaction->update(['status'=> 'error']);
            return [
                'status'=> false,
                'message'=> __('Unable to get payment gateway')
            ];
        }

        $gatewayObject = $gateway->driver_path;
        $checkCallback = $gatewayObject::callback($data,$transaction->amount,$transaction->callback_url,$transaction->callback_url,'EGP',$transaction->id);

        if($checkCallback){
            $transactionData = WalletData::makeTransaction(
                $transaction->amount,
                'wallet',
                setting('online_payment_wallet_id'),
                $walletObject,
                'online_payment_gateway',
                $transaction->id,
                $User->modelPath,
                $User->id
            );

            if(!$transactionData){
                $transaction->update(['status'=> 'error']);
                return [
                    'status'=> false,
                    'message'=> __('Unable to get payment gateway')
                ];
            }

            $transaction->update([
                'status'                => 'paid',
                'wallet_transaction_id' => $transactionData->id,
                'card_type'             => (isset($data['card_type'])) ? $data['card_type'] : null,
                'locale'                => (isset($data['locale'])) ? $data['locale'] : null,
                'response_map'          => (isset($data['response_map'])) ? $data['response_map'] : null
            ]);

            return [
                'status'=> true,
                'message'=> __('Paid successfully')
            ];

        }else{
            $transaction->update([
                'status'        => 'rejected',
                'card_type'     => (isset($data['card_type'])) ? $data['card_type'] : null,
                'locale'        => (isset($data['locale'])) ? $data['locale'] : null,
                'response_map'  => (isset($data['response_map'])) ? $data['response_map'] : null
            ]);

            return [
                'status'=> false,
                'message'=> __('transaction rejected')
            ];

        }


    }*/


}