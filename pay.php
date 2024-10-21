<html>
<head>
    <script src="https://test-nbe.gateway.mastercard.com/checkout/version/57/checkout.js"
            data-error="errorCallback"
            data-cancel="cancelCallback">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">

        var body ={
            "apiOperation": "CREATE_CHECKOUT_SESSION",
            "interaction": {
                "operation": "AUTHORIZE"
            },
            "order"      : {
                "amount"     : "122.0",
                "currency"   : "USD",
                "description": "Ordered goods",
                "id": "232E32323ddd"
            }
        };

        $.ajax({
            url: "https://test-nbe.gateway.mastercard.com/api/rest/version/57/merchant/EGPTEST1/session",
            type: 'Post',
            headers: {
                'url':'https://test-nbe.gateway.mastercard.com/'
                'Authorization':'Basic merchant.EGPTEST1:61422445f6c0f954e24c7bd8216ceedf',
                'Content-Type':'application/json'
            },
            success: function(data) {
                console.log("Success!");
                console.log(data);
            }
        });



        function errorCallback(error) {
            console.log(JSON.stringify(error));
        }
        function cancelCallback() {
            console.log('Payment cancelled');
        }

        function checkout(){
        Checkout.configure({
            merchant: 'EGPTEST1',
            order: {
                amount: function () {
                    //Dynamic calculation of amount
                    return 1 + 0;
                },
                currency: 'EGP',
                description: 'Ordered goods',
                id: 'AAA1',
                session: {
                    id: 'CHECKOUT_SESSION_ID from the 1st step'
                },
                interaction: {
                    operation: 'PURCHASE',
                    merchant: {
                        name: 'NBE Test',
                        address: {
                            line1: '200 Sample St',
                            line2: '1234 Example Town'
                        }
                    }
                }
            }
        });
        }
    </script>
</head>
<body>

<input type="button" value="Pay with Lightbox" onclick="Checkout.showLightbox();" />
<input type="button" value="Pay with Payment Page" onclick="Checkout.showPaymentPage();" />

</body>
</html>
