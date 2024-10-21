<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"
           ></script>

    <script src="https://test-nbe.gateway.mastercard.com/checkout/version/57/checkout.js"
            data-error="{{route('web.pay-success')}}?order_id={{$result->order_id}}&status=fail"
            data-cancel="{{route('web.pay-success')}}?order_id={{$result->order_id}}&status=fail"
            data-complete="{{route('web.pay-success')}}?order_id={{$result->order_id}}&status=paid">
    </script>

    <script type="text/javascript">
        function errorCallback(error) {
            console.log(error,'error');
            // pageAlert('#form-gateway-alert-message', 'error', 'Unknown Error');
            // $('#PayButton').hide();
        }
        function cancelCallback(data) {
            console.log(data,'cancel');
        }

        function completeCallback(resultIndicator, sessionVersion) {
            console.log(resultIndicator,sessionVersion);
            return;
            $('#PayButton').hide();
            addLoading();
            {{--$.post('{{route('merchant.gateway.pay',$result->uuid)}}',{--}}
            {{--    '_token': '{!! csrf_token() !!}',--}}
            {{--    '_method': 'PUT'--}}
            {{--},function($data){--}}
            {{--    removeLoading();--}}
            {{--    if ($data.status) {--}}
            {{--        pageAlert('#form-gateway-alert-message', 'success', $data.message);--}}
            {{--    } else {--}}
            {{--        pageAlert('#form-gateway-alert-message', 'error', $data.message);--}}
            {{--    }--}}
            {{--});--}}
        }

        Checkout.configure({
            merchant: 'EGPTEST1',  // Merchant ID
            session: {
                id: '{{$result->id}}',
                version: '{{$result->version}}'
            },
            order: {
                amount: '{{$result->amount}}',
                currency: 'EGP',
                description: '{{$result->description}}',
                id:  '{{$result->order_id}}' // Order ID*/
            },
            interaction: {
                operation: 'PURCHASE',
                merchant: {
                    name: 'NBE Test',
                    address: {
                        line1: '9 El Tayaran St. Nasr City',
                    }
                }
            }
        });

        $(document).ready(function(){
            Checkout.showLightbox();
        });

    </script>
</head>
<body>
{{--...--}}
{{--<input type="button" value="Pay with Lightbox" onclick="Checkout.showLightbox();" />--}}
{{--<input type="button" value="Pay with Payment Page" onclick="Checkout.showPaymentPage();" />--}}
{{--...--}}
{{--</body>--}}
</html>
