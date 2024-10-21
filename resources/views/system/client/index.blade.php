@extends('system.layout')
@section('content')

    <div class="modal fade" id="filter-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'filterForm','onsubmit'=>'filterFunction($(this));return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Filter')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body param-div">


                    <div class="form-group mb1">
                        <label>{{__('Registered At')}}</label>
                        <div class="input-daterange input-group k_datepicker_5" >
                            {!! Form::text('created_at1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('created_at2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>
                    </div>

                    <div class="form-group mb1">
                        <label>{{__('Verified At')}}</label>
                        <div class="input-daterange input-group k_datepicker_5" >
                            {!! Form::text('verified_at1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('verified_at2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>
                    </div>

                    <div class="form-group mb1">
                        <label>{{__('Updated At')}}</label>
                        <div class="input-daterange input-group k_datepicker_5" >
                            {!! Form::text('updated_at1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('updated_at2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('ID')}}</label>
                            {!! Form::number('id',null,['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('NID')}}</label>
                            {!! Form::number('id_number',null,['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Bank Account Number')}}</label>
                            {!! Form::number('bank_account_number',null,['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Parent Account').' ( '.__('multiple related accounts').' ) '}}</label>
                            {!! Form::select('parent_id',[''=> __('Select Parent')],null,['class'=>'form-control client-select','autocomplete'=>'off']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Name')}}</label>
                            {!! Form::text('name',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Mobile')}}</label>
                            {!! Form::text('mobile',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('E-mail')}}</label>
                            {!! Form::email('email',null,['class'=>'form-control']) !!}
                        </div>
                            <div class="col-md-6">
                            <label>{{__('Phone')}}</label>
                            {!! Form::text('phone',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Created By')}}</label>
                            @php
                                $staffViewSelect = [''=> __('Select Staff')];
                                $staffViewSelect  = $staffViewSelect +array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('created_by_staff_id',$staffViewSelect, null,['class'=>'form-control','id'=>'created-by-select-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Address')}}</label>
                            {!! Form::textarea('address',null,['class'=>'form-control','rows'=>'2']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Description')}}</label>
                            {!! Form::textarea('description',null,['class'=>'form-control','rows'=>'2']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Area')}}</label>
                            {!! Form::select('area_id',[''=> __('Select Area')],null,['class'=>'form-control  area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">

                    </div>

                    <div class="form-group mb1">
                        <label>{{__('Birth Date')}}</label>
                        <div class="input-daterange input-group k_datepicker_5" >
                            {!! Form::text('birth_date1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('birth_date2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Credit From')}}</label>
                            {!! Form::number('credit1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Credit To')}}</label>
                            {!! Form::number('credit2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Gender')}}</label>
                            {!! Form::select('gender',[''=>__('All'),'male'=>__('Male'),'Female'=>__('Female')],null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Status')}}</label>
                            {!! Form::select('status',[''=>__('All'),'pending'=>__('Pending'),'active'=>__('Active'),'in-active'=>__('In-Active')],null,['class'=>'form-control']) !!}
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
       {{-- <div class="alert alert-light alert-elevate" role="alert">
            <div class="alert-icon"><i class="flaticon-warning k-font-brand"></i></div>
            <div class="alert-text">
                With server-side processing enabled, all paging, searching, ordering actions that DataTables performs are handed off to a server where an SQL engine (or similar) can perform these actions on the large data set.
                See official documentation <a class="k-link k-font-bold" href="https://datatables.net/examples/data_sources/server_side.html" target="_blank">here</a>.
            </div>
        </div>--}}
        <div class="k-portlet k-portlet--mobile">
            <div class="k-portlet__head">
                <div class="k-portlet__head-label">
                    <h3 class="k-portlet__head-title">
                        {{$pageTitle}}{{__("'s data")}}
                    </h3>
                </div>
            </div>
            <div class="k-portlet__body table-responsive">

                <!--begin: Datatable -->
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

                <!--end: Datatable -->
            </div>
        </div>
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script type="text/javascript">
        simpleAjaxSelect2('.area-select','area',1,'{{__('Select Area')}}');

        @if($type == 'owner')
        simpleAjaxSelect2('.client-select','owner',2,'{{__('Select Parent')}}');
        @else
        simpleAjaxSelect2('.client-select','renter',2,'{{__('Select Parent')}}');
        @endif

        $datatable = $('#datatable-main').DataTable({
            "iDisplayLength": 25,
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
            /*,
            "fnPreDrawCallback": function(oSettings) {
                for (var i = 0, iLen = oSettings.aoData.length; i < iLen; i++) {
                    if(oSettings.aoData[i]._aData[6] != ''){
                        oSettings.aoData[i].nTr.className = oSettings.aoData[i]._aData[6];
                    }
                }
            }*/
        });
{{--        @if(!staffCan('show-all-phones'))--}}
{{--        $datatable.column( 3 ).visible(false); // mobile--}}
{{--        @endif--}}

        $('#th_type,#th_status').change(function(){
            if(!empty($(this).val())){
                $('#'+$(this).attr('id')+"_a").css('color','red');
            }else{
                $('#'+$(this).attr('id')+"_a").css('color','');
            }

            $WHERE = [];

            if(!empty($('#th_type').val())){
                $WHERE.push("type="+$('#th_type').val());
            }

            if(!empty($('#th_status').val())){
                $WHERE.push("status="+$('#th_status').val());
            }

            var URL = $WHERE.join("&");

            if("{{url()->full()}}".includes('?')){
                $sign = '&';
            }else{
                $sign = '?';
            }

            $datatable.ajax.url("{{url()->full()}}"+$sign+URL).load();
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

        function blockClient($routeName,$reload = false){

            if(!confirm("{{__('Are you sure?')}}")){
                return false;
            }

            if($reload == undefined){
                $reload = 3000;
            }
            addLoading();

            $.post(
                $routeName,
                {
                    '_method':'POST',
                    '_token':$('meta[name="csrf-token"]').attr('content')
                },
                function(response){
                    removeLoading();
                    if(isJSON(response)){
                        $data = response;
                        if($data.status == true){
                            toastr.success($data.message, 'Success !', {"closeButton": true});
                            if($reload){
                                setTimeout(function(){location.reload();},$reload);
                            }
                            $url = '{{url()->full()}}?is_total=true&'+$('#filterForm').serialize();
                            $datatable.ajax.url($url).load();
                        }else{
                            toastr.error($data.message, 'Error !', {"closeButton": true});
                        }
                    }
                }
            )
        }

    </script>
@endsection

@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single .select2-selection__clear{
            font-size: large;
            color: red;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }

        .form-control-sm {
            min-width:100px;
        }

        .param-div .select2-container--default{
            width: 100% !important;
        }
        .param-div .select2-search__field{
            width: 100% !important;
        }

        .param-div .select2-container--default .select2-selection--multiple .select2-selection__clear{
            display: none;
        }

        .param-div .select2-selection {
            display: inline-table;
            width: 100%;;
        }

        .multi-table-div .select2-search__field{
            width: 100px !important;
        }

        /*.select2-container--default .select2-selection--multiple .select2-selection__clear{*/
        /*    display: none;*/
        /*}*/

        /*.select2-selection {*/
        /*    display: inline-table;*/
        /*    width: 100%;;*/
        /*}*/

    </style>
@endsection