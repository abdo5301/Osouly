@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" type="text/css">
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
    <style>
        /* #datatable-main_filter {*/
        /*     margin-left: 185px;*/
        /* }*/
        /*.dt-buttons button{*/
        /*    margin-left: 5px;*/
        /*}*/
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

        .select2-container--default .select2-selection--single .select2-selection__clear{
            font-size: large;
            color: red;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }

    </style>
@endsection
@section('content')



    <div class="modal fade" id="create-call-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {!! Form::open(['id'=>'main-form','onsubmit'=>'submitMainForm();return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="create-call-modal-title">{{__('Create Call')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div id="form-alert-message"></div>


                    {!! Form::hidden('sign_id',null,['id'=>'sign_id_call_modal']) !!}
                    {!! Form::hidden('sign_type','leads') !!}


                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Client')}}<span class="red-star">*</span></label>
                            {!! Form::hidden('client_id',null,['id'=>'client_id_call_modal']) !!}
                            {!! Form::text('client_id_hidden',null,['class'=>'form-control','disabled'=>'disabled','id'=>'client_id_hidden_call_modal']) !!}
                        </div>
                    </div>





                    <div class="form-group row">
                        <div class="col-md-4">
                            <label>{{__('Type')}}<span class="red-star">*</span></label>
                            {!! Form::select('type',['in'=> __('IN'),'out'=> __('OUT')],null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="type-form-error"></div>
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

                        <div class="col-md-4">
                            <label>{{__('Action')}}<span class="red-star">*</span></label>
                            @php
                                $purposesData = [''=>__('Select Action')];
                                foreach ($lead_status as $key => $value){
                                    $purposesData[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('call_purpose_id',$purposesData,null,['class'=>'form-control','id'=>'call_purpose_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="call_purpose_id-form-error"></div>
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
                            <label>{{__('Time of Remind')}}<span class="red-star">*</span></label>
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
                <div class="modal-body  param-div">


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
                            <label>{{__('To Sales')}}</label>
                            @php
                                $salesViewSelect = [''=> __('Select Sales')];
                                $salesViewSelect = $salesViewSelect+array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('transfer_to_sales_id',$salesViewSelect, null,['class'=>'form-control sales-select','id'=>'sales_id-form-input','autocomplete'=>'off']) !!}
                        </div>


                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('E-mail')}}</label>
                            {!! Form::text('email',null,['class'=>'form-control']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Description')}}</label>
                            {!! Form::text('description',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Project Name')}}</label>
                            {!! Form::text('project_name',null,['class'=>'form-control']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Campaign Name')}}</label>
                            {!! Form::text('campaign_name',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Client')}}</label>
                            {!! Form::select('client_id',[''=> __('Select Client')],null,['class'=>'form-control client-select','autocomplete'=>'off']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Last Action')}}</label>
                            @php
                                $StatusData = [''=>__('Select Action'),'fresh_lead'=>__('Fresh Lead')];

                                foreach ($lead_status as $key => $value){
                                    $StatusData[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('lead_status_id',$StatusData, null,['class'=>'form-control lead-status-select','id'=>'lead_status_id-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Last Description')}}</label>
                            {!! Form::text('last_call_description',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Last Status')}}</label>
                            @php
                                $callStatusData = [''=>__('Select Status')];
                                foreach ($status as $key => $value){
                                    $callStatusData[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('last_call_status',$callStatusData, null,['class'=>'form-control lead-status-select','id'=>'last_call_status-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>



                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Transfer By')}}</label>
                            @php
                                $staffViewSelect = [''=> __('Transfer By')];
                                $staffViewSelect  = $staffViewSelect +array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('transfer_by_staff_id',$staffViewSelect, null,['class'=>'form-control created_by-select','id'=>'created-by-select-form-input','autocomplete'=>'off']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Data Source')}}</label>
                            @php
                                $DataSource = [''=>__('Select Data Source')];
                                foreach ($data_source as $key => $value){
                                    $DataSource[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('data_source_id',$DataSource, null,['class'=>'form-control','id'=>'data_source_id-form-input','autocomplete'=>'off']) !!}
                        </div>

                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Addition Type')}}</label>
                            {!! Form::select('leads_type',[''=>__('Select Type'),'leads_manuel'=>__('Manuel'),'leads_Excel'=>__('Excel')],null,['class'=>'form-control leads-type-select','autocomplete'=>'off']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Created By')}}</label>
                            @php
                                $staffViewSelect = [''=> __('Select Creator')];
                                $staffViewSelect  = $staffViewSelect +array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('created_by_staff_id',$staffViewSelect, null,['class'=>'form-control created_by-select','id'=>'created-by-select-form-input','autocomplete'=>'off']) !!}
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



    <div class="modal fade" id="sales-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'salesForm','onsubmit'=>'toSales($(this));return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Sales')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <select name="to_sales_id" class="form-control  sales-select" id="select_to_sales_id" style="width: 100%;">
                                <option value="">{{__('Select Sales')}}</option>
                                @foreach(getSales() as $key => $value)
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                            <span id="select_to_sales_id_error" style="color: red"></span>
                        </div>
                        {{--                        <input type="hidden" name="data_ids" id="data_ids">--}}
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Save')}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


    <div class="modal fade" id="call-history-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Call History')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body table-responsive" id="call_history_table">

                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                </div>
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
                </div>
            </div>
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div id="page-alert-message"></div>
            <!--end::Portlet-->
        {{--            <div class="tab-content">--}}
        {{--                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">--}}

            <!--begin::Portlet-->
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__head">
                    <div class="k-portlet__head-label">
                        <h3  class="k-portlet__head-title">
                            {{__('Lead Data')}}
                        </h3>
                        @if(staffCan('lead-manage-all'))
                        <a  href="#" data-toggle="modal" data-target="#sales-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Transfer Leads To Sales')}}"   style="margin-right: 10px;margin-left: 10px;">
                            <span class="k-font-bold" id="k_dashboard_daterangepicker_date">  {{  __('To Sales')  }}  </span>
                            <i class="fa fa-user  k-padding-l-5 k-padding-r-0"></i>
                        </a>
                        @endif
                        @if(staffCan('system.lead-data.destroy') && !\Request::get('archive'))
                            <a  href="javascript:void(0)" onclick="toArchive();" class="btn btn-sm btn-elevate btn-danger" data-toggle="k-tooltip" title="{{__('Move Leads To Archive')}}"  style="margin-right: 10px;margin-left: 10px;">
                                <span class="k-font-bold" id="k_dashboard_daterangepicker_date">  {{  __('To Archive')  }}  </span>
                                <i class="fa fa-trash  k-padding-l-5 k-padding-r-0"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="k-portlet__body table-responsive">
                    <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                        <thead>
                        <tr>
                            <th><input type="checkbox" onclick="select_leads();" class="select-all-leads"></th>
                            @foreach($tableColumns as $key => $value)
                                <th>{{$value}}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th></th>
                            @foreach($tableColumns as $key => $value)
                                <th>{{$value}}</th>
                            @endforeach
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!--end::Portlet-->

        </div>
    {{--            </div>--}}
    <!--end::Row-->
        {{--                </div>--}}
        {{--            </div>--}}
    </div>

    <!-- end:: Content -->

@endsection
@section('footer')
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        noAjaxSelect2('.sales-select','{{__('Select Sales')}}','{{App::getLocale()}}');
        simpleAjaxSelect2('.client-select','investor',2,'{{__('Client')}}');



        $datatable = $('#datatable-main').DataTable({
            "iDisplayLength": 100,
            columns: [
                { name: 'select' },
                { name: 'id' },
                { name: 'last_call_purpose_id' },
                { name: 'last_call_status_id' },
                { name: 'last_call_description'},
                { name: 'name' },
                { name: 'mobile' },
                { name: 'email' },
                { name: 'description' },
                { name: 'project_name' },
                { name: 'campaign_name' },
                { name: 'client_id' },
                { name: 'transfer_by_staff_id' },
                { name: 'transfer_to_sales_id' },
                { name: 'created_by_staff_id' },
                { name: 'requested' },
                { name: 'data_source_id' },
                { name: 'action' },

            ],
            "columnDefs": [ {
                "targets": 0,
                "orderable" : false,
                "className": 'select-checkbox'
            },
                {  "orderable" : false, "targets": 1 },
                { "orderable" : false,  "targets": 2 },
                {  "orderable" : false, "targets": 3 },
                {  "orderable" : false, "targets": 4 },
                {  "orderable" : false, "targets": 5 },
                {  "orderable" : false, "targets": 6 },
                {  "orderable" : false, "targets": 7 },
                {  "orderable" : false, "targets": 8 },
                {  "orderable" : false, "targets": 9 },
                {  "orderable" : false, "targets": 10 },
                {  "orderable" : false, "targets": 11},
                {  "orderable" : false, "targets": 12},
                {  "orderable" : false, "targets": 13},
                {  "orderable" : false, "targets": 14 },
                {  "orderable" : false, "targets": 15 },
                {  "orderable" : false, "targets": 16 },
                {  "orderable" : false, "targets": 17 },

            ],
            "select": {
                "style":   'multi', //'os',
                "selector": 'td:first-child'
            },
            processing: true,
            serverSide: true,
            "order": [[ 1, "desc" ]],
            "ajax": {
                "url": "{{url()->full()}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
        });



{{--        @if(!staffCan('lead-manage-all'))--}}
{{--        $datatable.column( 10 ).visible(false);--}}
{{--        $datatable.column( 11 ).visible(false);--}}
{{--        $datatable.column( 12 ).visible(false);--}}
{{--        @endif--}}


        $datatable.column( 11 ).visible(false); // client_id
        $datatable.column( 15 ).visible(false); //requested

        @if(setting('table_leads_id') === 'no')
        $datatable.column( 'id:name' ).visible(false);
        @endif
            @if(setting('table_leads_name') === 'no')
        $datatable.column( 'name:name' ).visible(false);
        @endif
            @if(setting('table_leads_mobile') === 'no')
        $datatable.column( 'mobile:name' ).visible(false);
        @endif
            @if(setting('table_leads_description') === 'no')
        $datatable.column( 'description:name' ).visible(false);
        @endif
        @if(setting('table_leads_email') === 'no')
        $datatable.column( 'email:name' ).visible(false);
        @endif
            @if(setting('table_leads_campaign_name') === 'no')
        $datatable.column( 'campaign_name:name' ).visible(false);
        @endif
        @if(setting('table_leads_project_name') === 'no')
        $datatable.column( 'project_name:name' ).visible(false);
        @endif
        @if(setting('table_leads_data_source') === 'no')
        $datatable.column( 'data_source_id:name' ).visible(false);
        @endif
            @if(setting('table_leads_last_call_purpose') === 'no')
        $datatable.column( 'last_call_purpose_id:name' ).visible(false);
        @endif
            @if(setting('table_leads_last_call_status') === 'no')
        $datatable.column( 'last_call_status_id:name' ).visible(false);
        @endif
            @if(setting('table_leads_last_call_description') === 'no')
        $datatable.column( 'last_call_description:name' ).visible(false);
        @endif
            @if(setting('table_leads_transfer_by_staff') === 'no')
        $datatable.column( 'transfer_by_staff_id:name' ).visible(false);
        @endif
            @if(setting('table_leads_transfer_to_sales') === 'no')
        $datatable.column( 'transfer_to_sales_id:name' ).visible(false);
        @endif
            @if(setting('table_leads_created_by') === 'no')
        $datatable.column( 'created_by_staff_id:name' ).visible(false);
        @endif






        var data_ids = []; //global var

        $datatable.on( 'select', function ( e, dt, type, indexes ) {
            var selectedData = $datatable.rows( {selected:true} ).data();
            data_ids = [];
            for( var i = 0; i < selectedData.length; i++){
                var data_id = selectedData[i][1];
                if ( data_ids.indexOf(data_id) != -1) {
                    continue;
                }
                data_ids.push(data_id);
            }
            //   console.log(data_ids);

        } )
            .on( 'deselect', function ( e, dt, type, indexes ) {

                var selectedData = $datatable.rows( {selected:true} ).data();
                data_ids = [];
                for( var i = 0; i < selectedData.length; i++){
                    var data_id = selectedData[i][1];
                    if ( data_ids.indexOf(data_id) != -1) {
                        continue;
                    }
                    data_ids.push(data_id);
                }
                //  console.log(data_ids);
            } );


        function toSales($this) {
            if(!$('#select_to_sales_id').val()){
                $('#select_to_sales_id').css('border-color','red');
                $('#select_to_sales_id_error').text('{{__('Select Sales')}}');
                return;
            }

            if ( data_ids.length < 1){
                $('#select_to_sales_id_error').text('{{__('No selected rows for transferring')}}');return;
            }

            $('#select_to_sales_id_error').text('');
            $('#sales-modal').modal('hide');
            $("body,html").animate({ scrollTop: 0 }, "fast");
            pageAlert('#page-alert-message','success','{{__('Leads has been transferred successfully')}}');
            $url = '{{url()->full()}}?to_sales=true&data_ids='+ data_ids +'&'+$this.serialize();
            $datatable.ajax.url($url).load();
            $('#select_to_sales_id').val('').change();
            data_ids = [];
            {{--$url = '{{url()->full()}}?is_total=true&isDataTable=true';--}}
            {{--location = $url;--}}
        }



        function toArchive() {
            if ( data_ids.length < 1){
                alert('{{__('No Selected Leads !')}}');
              return;
            }

            if(!confirm('{{__('Are you sure, you want to Archive Selected Leads')}}')){
                return false;
            }

            $("body,html").animate({ scrollTop: 0 }, "fast");
            pageAlert('#page-alert-message','success','{{__('Leads has been Archived successfully')}}');
            $url = '{{url()->full()}}?to_archive=true&data_ids='+ data_ids;
            $datatable.ajax.url($url).load();
            data_ids = [];
            {{--$url = '{{url()->full()}}?is_total=true&isDataTable=true';--}}
            {{--location = $url;--}}
        }

        function showCallsHistory($id) {
            $.get(
                '{{route('system.misc.ajax')}}',
                {
                    'type':'leadsCallHistory',
                    'call_history_lead_id': $id,
                },
                function($data){
                    if($data.status == false){
                        $('#call_history_table').html('<b class="text-danger">'+'{{__('No Calls Added To This Lead')}}'+'</b>');
                        $('#call-history-modal').modal('show');
                    }else{
                        $('#call_history_table').html($data.calls);
                        $('#call-history-modal').modal('show');
                    }
                }
            );
        }



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


        function select_leads(){
            //alert('dsd');
            $(".select-all-leads").toggleClass('all-leads-checked');
            if ($(".select-all-leads.all-leads-checked")[0]){
                $datatable.rows().select();
            } else {
                $datatable.rows().deselect();
            }


        }


        function showModalCall($id,$client_id,$client_name){
           // create-call-modal
            if($id && $client_id && $client_name){
                $('#sign_id_call_modal').val($id);
                $('#client_id_call_modal').val($client_id);
                $('#client_id_hidden_call_modal').val($client_name);
                $('#create-call-modal-title').text(' {{__('Create Leads Call')}}  ( ID: '+$id+' ) ');
                $('#create-call-modal').modal('show');
            }else{
                $("body,html").animate({ scrollTop: 0 }, "fast");
                pageAlert('#page-alert-message','error','{{__('Client Data Not Found')}}');
              //  toastr.error('{{__('Client Data Not Found')}}', '', {"closeButton": true});
                return false;
            }
        }


        $('#remind_me-form-input').change(function () {
            if($(this).val() == 'yes'){
                $('#remind_me_on_div').show();
            }else{
                $('#remind_me_on_div').hide();
            }
        });


        $('.k_datetimepicker_1').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss'

        });

        function submitMainForm(){
            formSubmit(
                '{{route('system.call.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    $('#create-call-modal').modal('hide');
                    $url = '{{url()->full()}}?is_total=true&isDataTable=true';
                    $datatable.ajax.url($url).load();
                    $('#main-form')[0].reset();
                    $("body,html").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#page-alert-message','success','{{__('Action Added Successfully')}}');
                },
                function ($data){
                    $("#create-call-modal").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }




    </script>
@endsection