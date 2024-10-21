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
            <div class="k-portlet__body">

                {!! Form::open(['route' => isset($result) ? ['system.parameter.update',$result->id]:'system.parameter.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

                        {!! Form::hidden('property_type_id',$property_type->id) !!}

                        <div id="form-alert-message"></div>

                        {{--@if($errors->any())
                            <div class="alert alert-danger fade show" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">{{__('Some fields are invalid please fix them')}}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @elseif(Session::has('status'))
                            <div class="alert alert-{{Session::get('status')}} fade show" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">{{ Session::get('msg') }}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @endif--}}

                        @if(!isset($result))
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Column Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('column_name',null,['class'=>'form-control','id'=>'column_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="column_name-form-error"></div>
                            </div>
                        </div>
                        @endif
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Name (Arabic)')}}<span class="red-star">*</span></label>
                                    {!! Form::text('name_ar',isset($result) ? $result->name_ar : null,['class'=>'form-control','id'=>'name_ar-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="name_ar-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Name (English)')}}<span class="red-star">*</span></label>
                                    {!! Form::text('name_en',isset($result) ? $result->name_en : null,['class'=>'form-control','id'=>'name_en-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="name_en-form-error"></div>
                                </div>

                            </div>


                        <div class="form-group row">
                            <div class="col-md-3">
                                <label>{{__('Type')}}<span class="red-star">*</span></label>
                                {!! Form::select('type',['text'=> __('Text'),'textarea'=> __('Textarea'),'number'=> __('Number'),'select'=> __('Select'),'multi_select'=> __('Multi Select'),'radio'=> __('Radio Buttons'),'checkbox'=>__('Checkbox')],isset($result) ? $result->type : null,['class'=>'form-control','id'=>'type-form-input','onchange'=>'changeType($(this).val())']) !!}
                                <div class="invalid-feedback" id="type-form-error"></div>
                            </div>

                            <div class="col-md-3">
                                <label>{{__('Required')}}<span class="red-star">*</span></label>
                                {!! Form::select('required',['yes'=> __('Yes'),'no'=> __('No')],isset($result) ? $result->required : null,['class'=>'form-control','id'=>'required-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="required-form-error"></div>
                            </div>

                            <div class="col-md-3">
                                <label>{{__('Display on request')}}<span class="red-star">*</span></label>
                                {!! Form::select('show_in_request',['yes'=> __('Yes'),'no'=> __('No')],isset($result) ? $result->show_in_request : null,['class'=>'form-control','id'=>'show_in_request-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="show_in_request-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Display on property')}}<span class="red-star">*</span></label>
                                {!! Form::select('show_in_property',['yes'=> __('Yes'),'no'=> __('No')],isset($result) ? $result->show_in_property : null,['class'=>'form-control','id'=>'show_in_property-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="show_in_property-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row number-div" style="display:none;">

                            <div class="col-md-12">
                                <label>{{__('Between request')}}<span class="red-star">*</span></label>
                                {!! Form::select('between_request',['yes'=> __('Yes'),'no'=> __('No')],isset($result) ? $result->between_request : null,['class'=>'form-control','id'=>'between_request-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="between_request-form-error"></div>
                            </div>

                        </div>

                        <div class="multi-div" style="display:none;">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Multi request')}}<span class="red-star">*</span></label>
                                    {!! Form::select('multi_request',['yes'=> __('Yes'),'no'=> __('No')],isset($result) ? $result->multi_request : null,['class'=>'form-control','id'=>'multi_request-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="multi_request-form-error"></div>
                                </div>
                            </div>

                            <div class="multi-row-data">
                                @if(isset($result) && is_array($result->options))
                                    @foreach($result->options as $key => $value)
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label>{{__('Value')}}<span class="red-star">*</span></label>
                                            {!! Form::text('options[value][]',$value['value'],['class'=>'form-control','id'=>'options-value-0-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="options-value-0-form-error"></div>
                                        </div>

                                        <div class="col-md-4">
                                            <label>{{__('Name (Arabic)')}}<span class="red-star">*</span></label>
                                            {!! Form::text('options[name_ar][]',$value['name_ar'],['class'=>'form-control','id'=>'options-name_ar-0-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="options-name_ar-0-form-error"></div>
                                        </div>

                                        <div class="col-md-4">
                                            <label>{{__('Name (English)')}}<span class="red-star">*</span></label>
                                            {!! Form::text('options[name_en][]',$value['name_en'],['class'=>'form-control','id'=>'options-name_en-0-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="options-name_en-0-form-error"></div>
                                        </div>
                                        <div class="col-md-1">
                                            <label style="color: #FFF;">-</label>
                                            <a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">
                                                <i class="flaticon-delete form-control" style="color: red;"></i>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <label>{{__('Value')}}<span class="red-star">*</span></label>
                                            {!! Form::text('options[value][]',null,['class'=>'form-control','id'=>'options-value-0-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="options-value-0-form-error"></div>
                                        </div>

                                        <div class="col-md-4">
                                            <label>{{__('Name (Arabic)')}}<span class="red-star">*</span></label>
                                            {!! Form::text('options[name_ar][]',null,['class'=>'form-control','id'=>'options-name_ar-0-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="options-name_ar-0-form-error"></div>
                                        </div>

                                        <div class="col-md-4">
                                            <label>{{__('Name (English)')}}<span class="red-star">*</span></label>
                                            {!! Form::text('options[name_en][]',null,['class'=>'form-control','id'=>'options-name_en-0-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="options-name_en-0-form-error"></div>
                                        </div>
                                        <div class="col-md-1">
                                            <label style="color: #FFF;">-</label>
                                            <a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">
                                                <i class="flaticon-delete form-control" style="color: red;"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12" style="text-align: right;">
                                    <a href="javascript:void(0);" onclick="addMultiRowParameter();">
                                        <i class="flaticon2-add"></i>
                                        {{__('Add Row')}}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Position')}}<span class="red-star">*</span></label>
                                {!! Form::number('position',isset($result) ? $result->position : null,['class'=>'form-control','id'=>'position-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="position-form-error"></div>
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
                '{{isset($result) ? route('system.parameter.update',$result->id):route('system.parameter.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    window.location = $data.data.url;
                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

        function changeType($value){
            $('.number-div').hide();
            $('.multi-div').hide();

            if($value == 'number'){
                $('.number-div').show();
            }else if($value == 'select' || $value == 'multi_select' || $value == 'radio' || $value == 'checkbox'){
                $('.multi-div').show();
            }
        }


        $(document).ready(function(){
            changeType($('#type-form-input').val());
        });

    </script>
@endsection