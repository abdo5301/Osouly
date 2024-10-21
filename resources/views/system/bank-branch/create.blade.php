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
            <div class="k-portlet__body" style="background: #f7f7fb;">

                {!! Form::open(['route' => isset($result) ? ['system.bank-branch.update',$result->id]:'system.bank-branch.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body" style="background: #FFF;">
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
                                <label>{{__('Bank Code')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_bank_code',isset($result) ? $result->columns_data_bank_code: null,['class'=>'form-control','id'=>'columns_data_bank_code-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_bank_code-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Branch Code')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_branch_code',isset($result) ? $result->columns_data_branch_code: null,['class'=>'form-control','id'=>'columns_data_branch_code-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_branch_code-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Name (Arabic)')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_name_ar',isset($result) ? $result->columns_data_name_ar: null,['class'=>'form-control','id'=>'columns_data_name_ar-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_name_ar-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Name (English)')}}<span class="red-star">*</span></label>
                                {!! Form::text('columns_data_name_en',isset($result) ? $result->columns_data_name_en: null,['class'=>'form-control','id'=>'columns_data_name_en-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="columns_data_name_en-form-error"></div>
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
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.bank-branch.update',$result->id):route('system.bank-branch.store')}}',
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

    </script>
@endsection