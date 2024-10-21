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
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__body">

                    {!! Form::open(['route' => isset($result) ? ['system.payment-methods.update',$result->id]:'system.payment-methods.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

                        <div id="form-alert-message"></div>

                        <div  class="form-group row">
                            <div class="col-md-11">
                                <label>{{__('Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('name',isset($result) ? $result->name: null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>
                        </div>


                        <div class="parameters-data-container">
                            <h3 class="k-portlet__head-title" id="field_name-form-input" style="color: #00A79D;margin-bottom: 25px">{{__('Payment Method Parameters')}}</h3>
                            <div class="invalid-feedback" id="field_name-form-error" style="display: block;"></div>
                            <div class="parameters-data">
                                @if(isset($result) && !empty($parameters))
                                    @foreach($parameters as $key => $value)
                                        <div class="form-group row">
                                            <div class="col-md-5">
                                                <label>{{__('Field Name')}}<span class="red-star">*</span></label>
                                                {!! Form::text("field_name[]",$key,['class'=>'form-control field_name-form-input','autocomplete'=>'off']) !!}
                                                <div class="invalid-feedback" id="field_name-form-error"></div>
                                            </div>

                                            <div class="col-md-6">
                                                <label>{{__('Field Value')}}<span class="red-star">*</span></label>
                                                {!! Form::textarea("field_value[]",$value,['class'=>'form-control field_value-form-input','rows'=>'1','autocomplete'=>'off']) !!}
                                                <div class="invalid-feedback" id="field_value-form-error"></div>
                                            </div>

                                            <div class="col-md-1">
                                                <label style="color: #FFF;">-</label>
                                                <a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">
                                                    <i class="flaticon2-delete form-control" style="color: red;border: 0;"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label>{{__('Field Name')}}<span class="red-star">*</span></label>
                                            {!! Form::text("field_name[]",null,['class'=>'form-control field_name-form-input','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="field_name-form-error"></div>
                                        </div>

                                        <div class="col-md-6">
                                            <label>{{__('Field Value')}}<span class="red-star">*</span></label>
                                            {!! Form::textarea("field_value[]",null,['class'=>'form-control field_value-form-input','rows'=>'1','autocomplete'=>'off']) !!}
                                            <div class="invalid-feedback" id="field_value-form-error"></div>
                                        </div>

                                        <div class="col-md-1">
                                            <label style="color: #FFF;">-</label>
                                            <a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">
                                                <i class="flaticon2-delete form-control" style="color: red;border: 0;"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12" style="text-align: right;">
                                    <a href="javascript:void(0);" onclick="addMultiRowField();">
                                        <i class="flaticon2-add"></i>
                                        {{__('Add Field')}}
                                    </a>
                                </div>
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
        </div>
        <!-- end:: Content -->
        @endsection
        @section('footer')
            <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
            <script type="text/javascript">

                function submitMainForm(){
                    $(".field_name-form-input").each(function() {
                            $(this).css('border-color','#4d65dc94');
                            $(this).parent().find('.invalid-feedback').remove();
                            var value = $(this).val();
                            if(empty(value)){
                              $(this).css('border-color','#fd397a');
                              $(this).after('<div class="invalid-feedback" style="display:block;">{{__('Field name is required')}}</div>');
                            }
                        });

                    $(".field_value-form-input").each(function() {
                        $(this).css('border-color','#4d65dc94');
                        $(this).parent().find('.invalid-feedback').remove();
                        var value = $(this).val();
                        if(empty(value)){
                            $(this).css('border-color','#fd397a');
                            $(this).after('<div class="invalid-feedback" style="display:block;">{{__('Field value is required')}}</div>');
                        }
                    });
                    formSubmit(
                        '{{isset($result) ? route('system.payment-methods.update',$result->id):route('system.payment-methods.store')}}',
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

                function addMultiRowField(){
                    $data = "<div class=\"form-group row\">\n" +
                        "                        <div class=\"col-md-5\">\n" +
                        "                            <label>{{__('Field Name')}}<span class=\"red-star\">*</span></label>\n" +
                        "                            <input class=\"form-control field_name-form-input\" autocomplete=\"off\" name=\"field_name[]\" type=\"text\">\n" +
                        "                            <div class=\"invalid-feedback\" id=\"field_name-form-error\"></div>\n" +
                        "                        </div>\n" +
                        "\n" +
                        "                        <div class=\"col-md-6\">\n" +
                        "                            <label>{{__('Field Value')}}<span class=\"red-star\">*</span></label>\n" +
                        "                            <textarea class=\"form-control field_value-form-input\" id=\"field_value-form-input\" rows=\"1\"  autocomplete=\"off\" name=\"field_value[]\" type=\"text\"></textarea>\n" +
                        "                            <div class=\"invalid-feedback\"  id=\"field_value-form-error\"></div>\n" +
                        "                        </div>\n" +
                        "\n" +
                        "                        <div class=\"col-md-1\">\n" +
                        "                            <label style=\"color: #FFF;\">-</label>\n" +
                        "                            <a href=\"javascript:void(0);\" onclick=\"removeMultiRowParameter($(this));\">\n" +
                        "                                <i class=\"flaticon2-delete form-control\" style=\"color: red;border: 0;\"></i>\n" +
                        "                            </a>\n" +
                        "                        </div>\n" +
                        "                    </div>";


                    $('.parameters-data').append($data);
                }

            </script>
@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />

    <style>
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

        .select2-container--default .select2-selection--single .select2-selection__clear,.select2-selection__choice__remove{
            font-size: large;
            color: red !important;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }
    </style>
@endsection