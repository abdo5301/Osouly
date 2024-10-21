@extends('system.layout')
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
            <div class="k-content__head-toolbar">
                <div class="k-content__head-wrapper">

                    {{--<a href="#" data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">--}}
                        {{--<span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Filter')}}</span>--}}
                        {{--<i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>--}}
                    {{--</a>--}}

                         <a href="javascript:downloadExcel('on_us')" class="btn btn-sm btn-elevate btn-brand">
                            <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Download ON US Excel')}}</span>
                            <i class="flaticon-download k-padding-l-5 k-padding-r-0"></i>
                        </a>

                        <a href="javascript:downloadExcel('off_us')" class="btn btn-sm btn-elevate btn-brand" style="margin: 0 10px;">
                            <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Download OFF US Excel')}}</span>
                            <i class="flaticon-download k-padding-l-5 k-padding-r-0"></i>
                        </a>
                </div>
            </div>

        </div>

        <!-- end:: Content Head -->


        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__body" style="background: #f7f7fb;">

                    {!! Form::open(['route' => 'system.report.upload-credit','files'=>true, 'method' => 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body" style="background: #FFF;">
                        <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 40px;">{{__('Upload New Credits File')}}</h3>
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Excel File')}}<span class="red-star">*</span></label>
                                {!! Form::file('file',['class'=>'form-control','id'=>'file-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="file-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Ignore First Row (Fields Titles)')}}<span class="red-star">*</span></label>
                                {!! Form::select('ignore_first_row',['yes'=>__('Yes'),'no'=>__('No')],null,['class'=>'form-control','id'=>'ignore_first_row-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="ignore_first_row-form-error"></div>
                            </div>
                        </div>
                    </div>


                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                        <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 40px;">{{__('File Header Fields Letters (A ~ Z)')}}</h3>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label>{{__('Client ID')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_client_id',isset($result) ? $result->columns_data_client_id: null,['class'=>'form-control','id'=>'columns_data_client_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_client_id-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Client Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_client_name',isset($result) ? $result->columns_data_client_name: null,['class'=>'form-control','id'=>'columns_data_client_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_client_name-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Transaction ID')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_transaction_id',isset($result) ? $result->columns_data_transaction_id: null,['class'=>'form-control','id'=>'columns_data_transaction_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_transaction_id-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Amount Value')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_amount',isset($result) ? $result->columns_data_amount: null,['class'=>'form-control','id'=>'columns_data_amount-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_amount-form-error"></div>
                            </div>
                        </div>

                        <div class="k-portlet__foot">
                            <div class="k-form__actions">
                                <div class="row" style="float: right;">
                                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>





                </div>
                {!! Form::close() !!}
            </div>

            @if(!empty($old_files))
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__head">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">
                            {{__('Files')}}
                        </h3>
                    </div>
                </div>
                <div class="k-portlet__body table-responsive">
                    <div class="k-portlet__body table-responsive">
                    <!--begin: Datatable -->
                    <table class="table table-striped table-bordered table-hover " id="datatable-main">
                        <tbody>
                        @php
                        $count_files = 1;
                        @endphp
                        @foreach($old_files as $key => $value)
                        <tr>
                            <td>
                               {{$count_files}}
                            </td>
                            <td>
                                <a href="{{$value}}" download>{{__('Download File')}}</a>
                            </td>
                        </tr>
                        @php  $count_files++ @endphp
                        @endforeach
                        </tbody>
                    </table>

                    <!--end: Datatable -->
                </div>
            </div>

            @endif
        </div>
        <!-- end:: Content Body -->

    </div>

    <!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

    <script type="text/javascript">

        function submitMainForm(){
            formSubmit(
                '{{route('system.report.upload-credit')}}',
                new FormData($('#main-form')[0]),
                function ($data) {
                    if($data.status === false){
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',$data.message);
                    }else{
                        //window.location = $data.data.url;
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','success',$data.message);
                        $('#main-form')[0].reset();
                    }

                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

        function downloadExcel($this){
                $url = '{{url()->full()}}?is_total=true&downloadExcel='+$this;
                location = $url;
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

        $('.multiple-select2').select2();


        $('input[type="text"]').focus(function() {
            $(this).addClass("focus");
        });

        $('input[type="text"]').blur(function() {
            $(this).removeClass("focus");
        });

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

    </style>
@endsection