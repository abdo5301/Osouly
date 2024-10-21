<?php

namespace App\Modules\Web;

use App\Http\Controllers\Controller;
use App\Models\ClientPackages;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Pay;
use App\Models\Property;

use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionInvoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use RuntimeException;

class WebController extends Controller{

    protected $viewData = [];


    public function index(Request $request){

        if($request->bug) {
            Bugsnag::notifyException(new RuntimeException("Test error"));
        }
        if($request->mail){
            send_invoice_email('amr.bdreldin@gmail.com','Test','',['invoice'=>Invoice::find(1)]);
        }
        return $this->view('home');
        return redirect('/system');
    }


//    function init_pay(Request $request){
//
//        if(empty($request->type) || empty($request->id)){
//            abort(401,'Type and id is required');
//        }
//
//        if($request->type == 'invoice'){
//            $invoice = Invoice::where('id',$request->id)->where('client_id',Auth::id())->first();
//            if(!$invoice){dd('dd');
//                abort(401,'Invalid Invoice ID');
//            }
//            $transaction = Transaction::create([
//                'payment_method_id'=>1,
//                'client_id'=>Auth::id(),
//                'invoice_id'=>$invoice->id,
//                'status'=>'pending',
//                'amount'=> $invoice->amount,
//                'total_amount'=> $invoice->amount
//            ]);
//            $amount = $invoice->amount;
//            $description = $invoice->property_due->name.' '.$invoice->date;
//        }else{
//            $service = Service::find($request->id);
//
//            if(!$service){
//                abort(401,'Invalid Service ID');
//            }
//            $amount = $service->price;
//            $description = $service->title_ar;
//            $transaction = Transaction::create([
//                'payment_method_id'=>1,
//                'client_id'=>Auth::id(),
//                'service_id'=>$service->id,
//                'status'=>'pending',
//                'amount'=> $service->price,
//                'total_amount'=> $service->price
//            ]);
//        }
//
//        $result = $this->startSession('osouly'.$transaction->id,$amount);
//        $result = (object)$result;
//        $transaction->update(['session_id'=>$result->id]);
//
//        $result->amount = $amount;
//        $result->description = $description;
//        $result->order_id = 'osouly'.$transaction->id;
//
//        return $this->view('pay',['result'=>$result]);
//    }


public function init_pay(Request $request){
        if(empty($request->order_id)){
            abort(401);
        }
    $ex = explode('y',$request->order_id);
    if (isset($ex[1])){
        $order_id = (int)$ex[1];
    }else{
        abort(401);
    }

    $transaction = Transaction::where('status','pending')->find($order_id);
    if(!$transaction){
        abort(401);
    }

    $result['id']= $transaction->session_id;
    $result['amount']= $transaction->total_amount;
    $result['order_id']= $request->order_id;
    $result['description']= $transaction->notes;
    $result['version']= $transaction->version;

    return $this->view('pay',['result'=>(object)$result]);


}

    public function complete_pay(Request $request){


        $input = $request->only('order_id','status','response');

        $ex = explode('y',$input['order_id']);
        if (isset($ex[1])){
            $input['order_id'] = (int)$ex[1];
        }
        $validator = Validator::make($input, [
//            'order_id' =>  'int|required|exists:transactions,id',
            'status' =>  'string|required',
            'response' =>  'string|nullable',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $transaction = Transaction::where('status','pending')->where('id',$input['order_id'])->first();
        if(!$transaction){
            return $this->fail('Transaction Not Found');
        }

        if($input['status'] == 'paid'){

            $transaction->update(['response'=>$request->response,'status'=>'paid']);

            if($transaction->type == 'invoice') {
                $invoices =TransactionInvoices::where('transaction_id',$transaction->id)->with('invoice')->get();
                foreach ($invoices as $one) {
                    $invoice = $one->invoice;
                    $invoice->update(['status' => 'paid']);


                    //تحويل الفلوس للارصده

                    // أخر باقة للعميل
                    $client_package = ClientPackages::where(['client_id' => $invoice->property->owner_id, 'status' => 'active', 'service_type' => 'manage'])
                        ->orderby('id', 'desc')->first();
                    if(!$client_package){
                      continue;
                        //  return 'ليس لدى المالك اى خدمة تحصيل مفعلة';
                    }



                    $app_amount = ($client_package->service_count / 100) * $invoice->amount;
                    $client_amount = $invoice->amount - $app_amount;


                    $invoice->update(['commission'=>$app_amount,'transaction_id'=>$transaction->id]);

                    // زيادة رصيد العميل
                    $client_package->client->credit_transactions()->create([
                        'type' => 'in',
                        'amount' => $client_amount,
                        'credit_before' => $client_package->client->credit,
                        'credit_after' => $client_package->client->credit + $client_amount,
                        'staff_id' => 0,
                        'invoice_id' => $invoice->id,
                        'transaction_id' => $transaction->id
                    ]);
                    $client_package->client->update(['credit' => $client_package->client->credit + $client_amount]);

                    //زيادة رصيد الموقع
                    Pay::create([
                        'sign_type' => 'App\Models\IncomeReason',
                        'sign_id' => 1,
                        'invoice_id' => $invoice->id,
                        'client_id' => $client_package->client_id,
                        'locker_id' => 1,
                        'price' => $app_amount,
                        'note' => 'Added By System T-ID '. $transaction->id,
                        'date' => date('Y-m-d H:i:s'),
                        'staff_id' => 0,
                        'payment_method_id' => 1
                    ]);

                }
                if (!empty($transaction->client->email)) {
                    $send_invoice = $invoice;
                   // @send_invoice_email($transaction->client->email, __('Osouly'), 'تم سداد الفاتورة رقم ' . $transaction->invoice_id  . ' بمبلغ قدرة ' . amount($transaction->amount).' رقم عملية الدفع '.$transaction->id,['invoice'=>$send_invoice,'transaction'=>$transaction]);
                }
            }else{

                $service = $transaction->service;
                ClientPackages::create([
                    'service_id'=>$service->id,
                    'client_id'=>$transaction->client_id,
                    'transaction_id'=>$transaction->id,
                    'service_type'=>$service->type,
                    'service_count'=>$service->properties_count,
                    'status'=>'active',
                    'rest_count'=>$service->properties_count,
                    'service_details'=>json_encode($service),
                    'date_from'=>date('Y-m-d'),
                    'date_to'=>date('Y-m-d',strtotime(date('Y-m-d'). ' + '.$service->duration.' days')),
                    'count_per_day'=>$service->type_count,
                ]);

                //زيادة رصيد الموقع
                   Pay::create([
                    'sign_type'=>'App\Models\IncomeReason',
                    'sign_id'=>2,
                    'client_id'=>$transaction->client_id,
                    'locker_id'=>1,
                    'price'=>$transaction->amount,
                    'note'=>'Added By System T-ID '. $transaction->id,
                    'date'=>date('Y-m-d H:i:s'),
                    'staff_id'=>0,
                    'payment_method_id'=>1
                ]);
                if(!empty($transaction->client->email)) {
                 //   @send_email($transaction->client->email,__('Osouly'),'تم الاشتراك بنجاح فى خدمة '.$transaction->service->title_ar.' مقابل ملغ وقدرة '.amount($transaction->amount).' رقم عملية الدفع '.$transaction->id);
                }

            }
        }else{
            $transaction->update(['response'=>$request->cause.' - '.$request->explanation,'status'=>'fail']);

          return  redirect('https://www.osouly.com/ar/checkout/error/');
        }
       return redirect('https://www.osouly.com/ar/checkout/success/');
     //   return $this->success('Done');


    }

public function check_payment($id){
        return true;
}


    public function startSession($id,$amount){


        $client = new \GuzzleHttp\Client();
        $result = $client->request('POST', 'https://test-nbe.gateway.mastercard.com/api/rest/version/57/merchant/EGPTEST1/session',[
            'auth'=> [
                'merchant.EGPTEST1',
                '61422445f6c0f954e24c7bd8216ceedf'
            ],
            'json'=> [
                'apiOperation'  => 'CREATE_CHECKOUT_SESSION',
                'interaction'   => [
                    'operation'=> 'PURCHASE'
                ],
                'order'         => [
                    'id'=> $id,
                    'amount'=> $amount,
                    'currency'=> 'EGP'
                ]
            ]
        ]);


        if($result->getBody()){
            $response = json_decode($result->getBody());
            if($response->result == 'SUCCESS'){
                return [
                    'status'=> true,
                    'id'=> $response->session->id,
                    'version'=> $response->session->version
                ];
            }
        }

        return [
            'status'=> false
        ];




    }


    public function propertyImages($id){
        $property = Property::where('id',$id)->firstOrFail();
        $this->viewData['result'] = $property;
        return $this->view('property-images',$this->viewData);
    }

    protected function view($file,array $data = []){
        return view('web.'.$file,$data);
    }

    protected function response($status,$code = '200',$message = 'Done',$data = []): array {
        return [
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ];
    }



    protected function success($msg='Done',$data = []){
        return $this->response(true,$msg,$data);
    }

    protected function fail($msg='fail',$data = []){
        return $this->response(false,$msg,$data);
    }




}