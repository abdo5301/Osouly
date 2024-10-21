@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" type="text/css">--}}
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" type="text/css">--}}
{{--    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />--}}
    <style>
        /*#datatable-main_filter {*/
        /*    margin-left: 185px;*/
        /*}*/
        /*.dt-buttons button{*/
        /*    margin-left: 5px;*/
        /*}*/

        .select2-container--default .select2-selection--single .select2-selection__clear{
            font-size: large;
            color: red;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }

    </style>
@endsection
@section('content')



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
{{--            <div class="k-content__head-toolbar">--}}
{{--                <div class="k-content__head-wrapper">--}}
{{--                    <a href="#" data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">--}}
{{--                        <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Filter')}}</span>--}}
{{--                        <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">


                    <!--begin::Portlet-->
                    <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">
                                    {{__('Data')}}
                                </h3>
                            </div>
                        </div>
                        <div class="k-portlet__body table-responsive">
                            <div class="k-section">
                                <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main" >
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


        <!--end::Row-->

        </div>
        <!-- end:: Content -->

        @endsection
        @section('footer')
{{--            <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>--}}
{{--            <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>--}}
{{--            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>--}}
            <script type="text/javascript">
                $datatable = $('#datatable-main').DataTable({
                    "columnDefs": [
                        {  "orderable" : true, "targets": 0 },
                        {  "orderable" : true, "targets": 1 },
                        {  "orderable" : true, "targets": 2 },
                        {  "orderable" : true, "targets": 3 },
                        {  "orderable" : true, "targets": 4 },
                        {  "orderable" : true, "targets": 5 },
                        {  "orderable" : true, "targets": 6 },
                        {  "orderable" : true, "targets": 7 },
                    ],
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    orderCellsTop: true,  // abdo edit
                    fixedHeader: true,  // abdo edit
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
                    $('#property-modal').modal('hide');
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

                        if(!$data.property_id){
                            $('#property-modal-property_id').text('{{__('Approve')}}').attr('onclick','approveProperty('+$id+')').show();
                        }else{
                            $('#property-modal-property_id').text('{{__('View')}}').attr('onclick','viewRealProperty('+$data.property_id+')').show();
                        }


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