<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{setting('company_name')}} - #ID: {{$result->id}}</title>
    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.0/examples/grid/grid.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</head>
<body>
<div class="container">

    <h1>
        {{__('Request #ID: :id',['id'=> $result->id])}}
        <small style="font-size:40%;">( {{$result->created_at->diffForHumans()}} )</small>

    </h1>

    <p class="lead">
        {{$result->client->name}}
    </p>


    <div class="row">
        <div class="col-md-12">
            <h3 style="margin-top: 0px;">{{__('Request Information')}}</h3>
        </div>
    </div>

    <div class="row">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>{{__('Key')}}</th>
                <th>{{__('Value')}}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{__('ID')}}</td>
                <td>{{$result->id}}</td>
            </tr>
            <tr>
                <td>{{__('Type')}}</td>
                <td>{{$result->property_type->{'name_'.\App::getLocale()} }}</td>
            </tr>
            <tr>
                <td>{{__('Purpose')}}</td>
                <td>{{$result->purpose->{'name_'.\App::getLocale()} }}</td>
            </tr>

            <tr>
                <td>{{__('Areas')}}</td>
                <td>
                    @foreach(explode(',',$result->area_ids) as $key => $value)
                        {{implode(' -> ',\App\Libs\AreasData::getAreasUp($value,true))}} <hr />
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>{{__('Status')}}</td>
                <td>{{$result->request_status->{'name_'.\App::getLocale()} }}</td>
            </tr>
            <tr>
                <td>{{__('Description')}}</td>
                <td>{{$result->description}}</td>
            </tr>
            @if($result->payment_type == 'cash')
                <tr>
                    <td>{{__('Payment Type')}}</td>
                    <td>{{__('Cash')}}</td>
                </tr>
                <tr>
                    <td>{{__('Price')}}</td>
                    <td>{{amount($result->price_from,true)}} : {{amount($result->price_to,true)}}</td>
                </tr>
            @elseif($result->payment_type == 'installment')
                <tr>
                    <td>{{__('Payment Type')}}</td>
                    <td>{{__('Installment')}}</td>
                </tr>
                <tr>
                    <td>{{__('Deposit')}}</td>
                    <td>{{amount($result->deposit_from,true)}} : {{amount($result->deposit_to,true)}}</td>
                </tr>
                <tr>
                    <td>{{__('Price')}}</td>
                    <td>{{amount($result->price_from,true)}} : {{amount($result->price_to,true)}}</td>
                </tr>
            @elseif($result->payment_type == 'cash_installment')
                <tr>
                    <td>{{__('Payment Type')}}</td>
                    <td>{{__('Cash & Installment')}}</td>
                </tr>
                <tr>
                    <td>{{__('Deposit')}}</td>
                    <td>{{amount($result->deposit_from,true)}} : {{amount($result->deposit_to,true)}}</td>
                </tr>
                <tr>
                    <td>{{__('Price')}}</td>
                    <td>{{amount($result->price_from,true)}} : {{amount($result->price_to,true)}}</td>
                </tr>
            @endif
            <tr>
                <td>{{__('Currency')}}</td>
                <td>{{$result->currency}}</td>
            </tr>
            <tr>
                <td>{{__('Space')}}</td>
                <td>{{number_format($result->space_from)}} {{__(ucfirst($result->space_type))}} : {{number_format($result->space_to)}} {{__(ucfirst($result->space_type))}}</td>
            </tr>

            <tr>
                <td>{{__('Sales')}}</td>
                <td>
                    {{$result->sales->fullname}} <br/> <a href="tel:{{$result->sales->mobile}}">{{$result->sales->mobile}}</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 style="margin-top: 0px;">{{__('Properties')}}</h3>
        </div>
    </div>

    <div class="row">
        @php
        if($result->sharing_properties_ids){
            $property = $result->property()->whereIn('properties.id',explode(',',$result->sharing_properties_ids))->get();
        }else{
             $property = $result->property()->get();
        }


        @endphp
        @if($property->isNotEmpty())
            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                <thead>
                    <tr>
                        <th>{{__('ID')}}</th>
                        <th>{{__('Name')}}</th>
                        <th>{{__('Type')}}</th>
                        <th>{{__('Purpose')}}</th>
                        <th>{{__('Space')}}</th>
                        <th>{{__('Price')}}</th>
                        <th>{{__('Action')}}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($property as $key => $value)
                    <tr>
                        <th>{{$value->id}}</th>
                        <th>
                            @if($value->name)
                                {{$value->name}}
                            @else
                                --
                            @endif
                        </th>
                        <th>{{$value->property_type->{'name_'.App::getLocale()} }}</th>
                        <th>{{$value->purpose->{'name_'.App::getLocale()} }}</th>
                        <th>{{number_format($value->space)}} {{__(ucfirst($value->space_type))}}</th>
                        <th>{{number_format($value->price)}} {{__($value->currency)}}</th>

                        <th>
                            <a href="javascript:viewProperty({{$value->id}});">
                            {{__('View')}}
                            </a>
                        </th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>


</div> <!-- /container -->





<div class="modal fade property-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('View Property')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="property-modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>



</body>
<script type="text/javascript">
    function viewProperty($id){
        $.get(
            '{{route('web.request.property',$result->sharing_slug)}}/'+$id,
            function($data){
                $('#property-modal-body').html($data);
                $('.property-modal').modal('show');
            }
        );
    }
</script>
</html>