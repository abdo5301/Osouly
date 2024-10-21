<!DOCTYPE html>
<html>
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>@yield('title')</title>
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
</head>
<body>
@php echo $contract->contract_content; @endphp
</body>
</html>

