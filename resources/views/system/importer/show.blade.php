@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')


    <div class="modal fade" id="property-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Property Data')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive" id="property-modal-data">

                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <button type="button" class="btn btn-outline-danger btn-md" id="property-modal-previous">{{__('Previous')}}</button>
                    <button type="button" class="btn btn-outline-primary btn-md" id="property-modal-next">{{__('Next')}}</button>
{{--                    <button type="button" class="btn btn-outline-success btn-md" id="property-modal-property_id">{{__('Approve')}}</button>--}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'propertyForm','onsubmit'=>'filterFunction($(this));return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Filter')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Owner')}}</label>
                            @php
                            $selectOwner = [''=>__('Select Owner')];
                            if($ownersData->isNotEmpty()){
                                foreach ($ownersData as $value){
                                    $selectOwner[$value->mobile] = $value->owner_name.' ('.$value->count.')';
                                }
                            }
                            @endphp
                            {!! Form::select('mobile',$selectOwner,null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Count From')}}</label>
                            {!! Form::number('count_from',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Count To')}}</label>
                            {!! Form::number('count_to',null,['class'=>'form-control']) !!}
                        </div>
                    </div>



                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Filter')}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


    <!-- begin:: Content -->
<div class="k-content	k-grid__item k-grid__item--fluid k-grid k-grid--hor" id="k_content">

    <!-- begin:: Content Head -->
    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{$pageTitle}}</h3>
            <div class="k-content__head-breadcrumbs">
                <a href="{{route('system.dashboard')}}" class="k-content__head-breadcrumb-home"><i class="flaticon2-shelter"></i></a>

                @foreach($breadcrumb as $key => $value)
                    <span class="k-content__head-breadcrumb-separator"></span>
                    @if(isset($value['url']))
                        <a href="{{$value['url']}}" class="k-content__head-breadcrumb-link">{{$value['text']}}</a>
                    @else
                        <span class="k-content__head-breadcrumb-link k-content__head-breadcrumb-link--active">{{$value['text']}}</span>
                    @endif
                @endforeach

            </div>
        </div>
        <div class="k-content__head-toolbar">
            <div class="k-content__head-wrapper">
                <a href="#" data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Filter')}}</span>
                    <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>
                </a>

                <a href="javascript:filterFunction($('#filterForm'),true)" class="btn btn-sm btn-elevate btn-brand">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Download Excel')}}</span>
                    <i class="flaticon-download k-padding-l-5 k-padding-r-0"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- end:: Content Head -->

    <!-- begin:: Content Body -->
    <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
        <!--end::Portlet-->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                <!--begin::Row-->
                <div class="row">
                    <div class="col-lg-12 order-lg-1 order-xl-1">

                        <!--begin::Portlet-->
                        <div class="k-portlet k-portlet--height-fluid">
                            <div class="k-portlet__head">
                                <div class="k-portlet__head-label">
                                    <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                </div>
                            </div>
                            <div class="k-portlet__body table-responsive">
                                <table class="table">
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
                                        <td>{{__('Connector')}}</td>
                                        <td>{{$result->connector}}</td>
                                    </tr>

                                    <tr>
                                        <td>{{__('Query')}}</td>
                                        <td>{{ $result->query_name }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{__('Area')}}</td>
                                        <td>{{implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true))}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Property Type')}}</td>
                                        <td>{{ $result->property_type->{'name_'.App::getLocale()} }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Purpose')}}</td>
                                        <td>{{ $result->purpose->{'name_'.App::getLocale()} }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Space')}}</td>
                                        <td>{{$result->space_from}} : {{$result->space_to}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Price')}}</td>
                                        <td>{{amount($result->price_from,true)}} : {{amount($result->price_to,true)}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Page')}}</td>
                                        <td>{{$result->page_start}} : {{$result->page_end}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Status')}}</td>
                                        <td>{{$result->status}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Success')}}</td>
                                        <td>{{$result->success}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Created By')}}</td>
                                        <td>
                                            <a href="{{route('system.staff.show',$result->created_by_staff_id)}}" target="_blank">
                                                {{$result->staff->fullname}}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Created At')}}</td>
                                        <td>{!! $result->created_at->format('Y-m-d h:iA') . '<br /> ('.$result->created_at->diffForHumans().')' !!}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Last Update')}}</td>
                                        <td>{!! $result->updated_at->format('Y-m-d h:iA') . '<br /> ('.$result->updated_at->diffForHumans().')' !!}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!--end::Portlet-->
                    </div>
                    <div class="col-lg-12 order-lg-1 order-xl-1">

                        <!--begin::Portlet-->
                        <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                            <div class="k-portlet__head">
                                <div class="k-portlet__head-label">
                                    <h3 class="k-portlet__head-title">
                                        {{__('Data')}}
                                    </h3>
                                </div>
                            </div>
                            <div class="k-portlet__body">
                                <div class="tab-content">
                                    <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                                        <thead>
                                        <tr>
                                            @foreach($tableColumns as $key => $value)
                                                <th>{{$value}}</th>
                                            @endforeach
                                        </tr>
                                        </thead>
                                        <tfoot>
                                        <tr>
                                            @foreach($tableColumns as $key => $value)
                                                <th>{{$value}}</th>
                                            @endforeach
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!--end::Portlet-->
                    </div>
                </div>

                <!--end::Row-->
            </div>
        </div>
    </div>
    </div>
<!-- end:: Content -->
@endsection
@section('footer')
        <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"></link>
        <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css"></link>
        <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>




    <script type="text/javascript">
            $datatable = $('#datatable-main').DataTable({
                dom: 'Bfrtip',
                buttons: [

                    'excelHtml5',
                    'csvHtml5'

                ],
                aoColumns: [
                    { mData: "id" },
                    { mData: "name" },
                    { mData: "price" },
                    { mData: "space" },
                    { mData: "bed_rooms" },
                    { mData: "bath_room" },
                    { mData: "owner_name" },
                    { mData: "mobile" },
                    { mData: "action" }
                ],
                "iDisplayLength": -1,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "true";
                    }
                }

            });

            function filterFunction($this,downloadExcel = false){

                if(downloadExcel == false) {
                    $url = '{{url()->full()}}?is_total=true&'+$this.serialize();
                    $datatable.ajax.url($url).load();
                    $('#filter-modal').modal('hide');
                }else{
                    $url = '{{url()->full()}}?is_total=true&isDataTable=true&'+$this.serialize()+'&downloadExcel='+downloadExcel;
                    location = $url;
                }

            }

            function viewProperty($id){
                //$('#property-modal').modal('hide');
                addLoading();
                $.get('{{route('system.importer.show',$result->id)}}',{
                    'propertyData': $id,
                    'mobile': $('[name="mobile"]').val(),
                    'count_from': $('[name="count_from"]').val(),
                    'count_to': $('[name=count_to]').val()
                },function($data){
                    removeLoading();
                    if(!$data.status){
                        return false;
                    }


                    if($data.next){
                        $('#property-modal-next').attr('onclick','viewProperty('+$data.next+')').show();
                    }else{
                        $('#property-modal-next').hide();
                    }

                    if($data.previous){
                        $('#property-modal-previous').attr('onclick','viewProperty('+$data.previous+')').show();
                    }else{
                        $('#property-modal-previous').hide();
                    }

                    {{--if(!$data.property_id){--}}
                    {{--    $('#property-modal-property_id').text('{{__('Approve')}}').attr('onclick','approveProperty('+$id+')').show();--}}
                    {{--}else{--}}
                    {{--    $('#property-modal-property_id').text('{{__('View')}}').attr('onclick','viewRealProperty('+$data.property_id+')').show();--}}
                    {{--}--}}


                    $('#property-modal-data').html($data.table);
                    $('#property-modal').modal('show');
                });
            }


            function approveProperty($id){
                window.open('{{route('system.property.create')}}?importer_data_id='+$id, '_blank').focus();
            }

            function viewRealProperty($id){
                window.open('{{route('system.property.index')}}/'+$id, '_blank').focus();
            }

    </script>
@endsection
