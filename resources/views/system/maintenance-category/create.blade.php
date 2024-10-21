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

                {!! Form::open(['route' => isset($result) ? ['system.maintenance-category.update',$result->id]:'system.maintenance-category.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

                        <div id="form-alert-message"></div>
                        <div  class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Category')}}</label>
                                @php
                                    $categoryData = [''=>__('Maintenance Categories')];
                                    foreach ($categories as $key => $value){
                                        $categoryData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('parent_id',$categoryData,isset($result) ? $result->parent_id: null,['style'=>'width: 100%','class'=>'form-control parent_id-select','id'=>'parent_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="parent_id-form-error"></div>
                            </div>
                        </div>

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
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

        <script type="text/javascript">
        noAjaxSelect2('.parent_id-select','{{__('Maintenance Categories')}}','{{App::getLocale()}}');

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.maintenance-category.update',$result->id):route('system.maintenance-category.store')}}',
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