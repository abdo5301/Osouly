<!doctype html>
<html style=" direction:rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>A simple, clean, and responsive HTML invoice template</title>
    <style>
        html,body {
            font-family: DejaVu Sans;
            width:100%;
            height: 100%;


            direction: rtl;
        }
    </style>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: left;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;

        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box ">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{asset(setting('company_logo'))}}" style="width:100%; max-width:300px;">
                        </td>


                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>


                        <td>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>
                الاسم
            </td>

            <td>
                البيان
            </td>
        </tr>

        <tr class="item">
            <td>المبلغ</td>
            <td>{{$invoice->amount}}</td>
        </tr>
        <?php $Arabic = new \App\Libs\Arabic('Numbers'); ?>
        <tr class="item">
            <td>مبلغ وقدرة</td>
            <td>{{  $Arabic->money2str( $invoice->amount, 'EGP', 'ar')}}</td>
        </tr>
        <tr class="item">

            <td>
                المبلغ المذكور وصلني من السيد
            </td>

            <td>
                {{$invoice->client->Fullname}}
            </td>
        </tr>

        <tr class="item">
            <td>
                طريقة الدفع
            </td>

            <td>
                {{(empty($transaction))?'نقدي':$transaction->payment_method_name }}
            </td>
        </tr>

        <tr class="item">
            <td>
                رقم الايصال
            </td>

            <td>
                {{$invoice->id}}
            </td>
        </tr>
        @if(!empty($transaction))
        <tr class="item">
            <td>
                رقم عملية الدفع
            </td>

            <td>
                {{$transaction->id}}
            </td>
        </tr>
        @endif

        <tr class="item">
            <td>
               تدفع عن
            </td>

            <td>
                {{@$invoice->property_due->dues->name}}
            </td>
        </tr>
        @if(!empty($invoice->property_id))


            <tr class="item">
                <td colspan="2">
                 وحدة رقم {{$invoice->property_id}} الكائن في مبني رقم {{$invoice->property->building_number}} - الشارع {{$invoice->property->street_name}}- المنطقة {{$invoice->property->local->name_ar  }} - المحافظة {{$invoice->property->government->name_ar}} -    مصر

                </td>

            </tr>

         @endif

        <tr class="item">
            <td>
                تاريخ الاستحقاق
            </td>

            <td>
                {{$invoice->date}}
            </td>
        </tr>


        <tr class="item">
            <td>
                تاريخ الدفع
            </td>

            <td>
                {{(empty($transaction))?date('Y-m-d',strtotime($invoice->updated_at)):date('Y-m-d',strtotime($transaction->created_at))}}
            </td>
        </tr>

        <tr class="item">
            <td>
                ملاحظات
            </td>

            <td>
                {{$invoice->notes}}
            </td>
        </tr>



    </table>
</div>
</body>
</html>