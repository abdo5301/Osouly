@extends('system.layout')
@section('content')

    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'filterForm','onsubmit'=>'filterFunction($(this));return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Filter')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <div class="form-group mb1">
                        <label>{{__('Created At')}}</label>
                        <div class="input-daterange input-group" id="k_datepicker_5">
                            {!! Form::text('created_at1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('created_at2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>

                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('ID')}}</label>
                            {!! Form::number('id',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Type')}}</label>
                            {!! Form::select('type',[''=>__('Select Type'),'in'=> __('IN'),'out'=> __('OUT')],null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Client')}}</label>
                            {!! Form::select('client_id',[''=> __('Select Client')],null,['class'=>'form-control client-select','autocomplete'=>'off']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Action')}}</label>
                            @php
                                $purposesData = [''=>__('Select Action')];
                                foreach ($purposes as $key => $value){
                                    $purposesData[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('call_purpose_id',$purposesData,null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Status')}}</label>
                            @php
                                $statusData = [''=>__('Select Status')];
                                foreach ($status as $key => $value){
                                    $statusData[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('call_status_id',$statusData,null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Description')}}</label>
                            {!! Form::text('description',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Sign Type')}}</label>
                            {!! Form::select('sign_type',[''=>__('Select Sign Type'),'App\Models\Property'=>__('Property'),'App\Models\Request'=>__('Request')],null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Sign ID')}}</label>
                            {!! Form::number('sign_id',null,['class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Parent Call ID')}}</label>
                            {!! Form::number('parent_id',null,['class'=>'form-control']) !!}
                        </div>
                    </div>



                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Created By')}}</label>
                            @php
                                $staffViewSelect = [''=> __('Select Creator')];
                                $staffViewSelect  = $staffViewSelect +array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('created_by_staff_id',$staffViewSelect, null,['class'=>'form-control','id'=>'created-by-select-form-input','autocomplete'=>'off']) !!}
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
                <a href="#" data-toggle="modal" data-target="#create-call-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Create Call')}}" data-placement="left">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Create Call')}}</span>
                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>
                </a>

                <a href="#" data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Filter')}}</span>
                    <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>
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
                <div class="k-section">
                    {{--<div class="k-section__content k-section__content--border" id="calls-data">
                    </div>--}}

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
                <!--end: Datatable -->
            </div>
        </div>
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->


<div class="modal fade" id="create-call-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['id'=>'main-form','onsubmit'=>'submitMainForm();return false;','class'=>'k-form']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{__('Create Call')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="form-alert-message"></div>

                @if($sign_id)
                    {!! Form::hidden('sign_id',$sign_id) !!}
                @endif

                @if($sign_type)
                    {!! Form::hidden('sign_type',$sign_type) !!}
                @endif

                @if(!$client_info)
                <div class="form-group row" id="client-select-information">
                    <div class="col-md-6">
                        <label>{{__('Client')}}<span class="red-star">*</span></label>
                        {!! Form::select('client_id',[''=> __('Select Client')],null,['class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="client_id-form-error"></div>
                    </div>
                    <div class="col-md-3">
                        <label style="color: #FFF;">*</label>
                        <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.renter.create',['addClientFromCall'=>'true'])}}');" class="form-control">
                            <i class="la la-plus"></i>
                            {{__('Add Renter')}}
                        </a>
                    </div>
                    <div class="col-md-3">
                        <label style="color: #FFF;">*</label>
                        <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.owner.create',['addClientFromCall'=>'true'])}}');" class="form-control">
                            <i class="la la-plus"></i>
                            {{__('Add Owner')}}
                        </a>
                    </div>
                </div>
                @else
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Client')}}<span class="red-star">*</span></label>
                            {!! Form::hidden('client_id',$client_info->id) !!}
                            {!! Form::text('client_id_hidden',$client_info->fullname,['class'=>'form-control','disabled'=>'disabled']) !!}
                        </div>
                    </div>

                @endif

                <div class="form-group row">
                    <div class="col-md-4">
                        <label>{{__('Type')}}<span class="red-star">*</span></label>
                        {!! Form::select('type',['in'=> __('IN'),'out'=> __('OUT')],null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="type-form-error"></div>
                    </div>
                    <div class="col-md-4">
                        <label>{{__('Action')}}<span class="red-star">*</span></label>
                        @php
                            $purposesData = [''=>__('Select Action')];
                            foreach ($purposes as $key => $value){
                                $purposesData[$value->id] = $value->{'name_'.App::getLocale()};
                            }
                        @endphp
                        {!! Form::select('call_purpose_id',$purposesData,null,['class'=>'form-control','id'=>'call_purpose_id-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="call_purpose_id-form-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label>{{__('Status')}}<span class="red-star">*</span></label>
                        @php
                            $statusData = [''=>__('Select Status')];
                            foreach ($status as $key => $value){
                                $statusData[$value->id] = $value->{'name_'.App::getLocale()};
                            }
                        @endphp
                        {!! Form::select('call_status_id',$statusData,setting('default_call_status'),['class'=>'form-control','id'=>'call_status_id-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="call_status_id-form-error"></div>
                    </div>

                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <label>{{__('Remind Me')}}</label>
                        {!! Form::select('remind_me',['no'=>__('No'),'yes'=>__('Yes')],null,['class'=>'form-control','id'=>'remind_me-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="remind_me-form-error"></div>
                    </div>
                </div>

                <div class="form-group row" id="remind_me_on_div" style="display: none;">
                    <div class="col-md-12">
                        <label>{{__('On')}}<span class="red-star">*</span></label>
                        {!! Form::text('remind_me_on',null,['class'=>'form-control k_datetimepicker_1','data-date-container=#create-call-modal','id'=>'remind_me_on-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="remind_me_on-form-error"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <label>{{__('Description')}}<span class="red-star">*</span></label>
                        {!! Form::textarea('description',null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="description-form-error"></div>
                    </div>
                </div>






            </div>




            <div class="modal-footer">
                <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Create')}}">
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<!-- Large Modal -->
<div class="modal fade" id="call-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="call-modal-title">{{__('Loading...')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="call-modal-body">
                {{__('Loading...')}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>


@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>

    <script type="text/javascript">

        @if($client_info)
           $(document).ready(function () {
            $('#create-call-modal').modal('show');
        });
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

        $('#create-call-modal').on('shown.bs.modal', function () {
           // ajaxSelect2('.client-select','clients');
            simpleAjaxSelect2('.client-select','clients',2,'{{__('Client')}}');
        });

        $(document).ready(function(){
            loadCalls();
        });


        function submitMainForm(){
            formSubmit(
                '{{route('system.call.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    loadCalls();
                    showCall($data.data.id);
                    $('#create-call-modal').modal('hide');
                    $('#main-form')[0].reset();
                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }


        function loadCalls($page){
            $pagePath = '';
            if($page){
                $('#load-mode-button').text('{{__('Loading...')}}').attr('disabled','disabled');
                $pagePath = '&page='+$page;
            }

            $.get('{{route('system.call.index',['ajax'=>'true'])}}'+$pagePath,function($data){
                if($page){
                    $('#load-mode-button').remove();
                }
                if($page){
                    $('#calls-data').append($data);
                }else{
                    $('#calls-data').html($data);
                }
            })
        }


        function showCall($id){

            $('#call-modal-title').text('{{__('#ID:')}} '+$id);
            $('#call-modal-body').text('{{__('Loading...')}}');
            $('#call-modal').modal('show');

            $.get('{{route('system.call.index')}}/'+$id,function($data){
                $('#call-modal-body').html($data);
            });
        }


        $('#remind_me-form-input').change(function () {
            if($(this).val() == 'yes'){
                $('#remind_me_on_div').show();
            }else{
                $('#remind_me_on_div').hide();
            }
        });



        $(document).ready(function(){
            @if(request('call_id'))
                showCall({{request('call_id')}});
            @endif
        });

        window.closeModal = function(){
            $('#modal-iframe').modal('hide');
        };

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


        $('.k_datetimepicker_1').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss'

        });

    </script>





@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
@endsection