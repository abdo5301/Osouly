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
                <div class="k-portlet__body" style="background: #f7f7fb;">

                    {!! Form::open(['route' => isset($result) ? ['system.property-dues.update',$result->id]:'system.property-dues.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('name',isset($result) ? $result->name: null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Value')}}<span class="red-star">*</span></label>
                                {!! Form::text('value',isset($result) ? $result->value: null,['class'=>'form-control','id'=>'value-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="value-form-error"></div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Select Property')}}<span class="red-star">*</span></label>
                                @php
                                    $properties = [''=> __('Select Property')];
                                    if(isset($result) && !empty($result->property)){
                                    $properties[$result->property_id] = " شقة رقم ".$result->property->flat_number .' - الدور ' .$result->property->floor.' - رقم المبنى '.$result->property->building_number.' - الشارع '.$result->property->street_name;
                                    }
                                @endphp
                                {!! Form::select('property_id',$properties,isset($result) ? $result->property_id: null,['class'=>'form-control property-select','id'=>'property_id-form-input','autocomplete'=>'off']) !!}
                                {{--{!! Form::number('property_id',isset($result) && !empty($result->property_id) ? $result->property_id: null,['class'=>'form-control','id'=>'property_id-form-input','autocomplete'=>'off']) !!}--}}
                                <div class="invalid-feedback" id="property_id-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Select Due Name')}}<span class="red-star">*</span></label>
                                @php
                                     $duesData = [''=>__('Due Name')];
                                     foreach ($dues as $key => $value){
                                         $duesData[$value->id] = $value->name;
                                     }
                                @endphp
                                {!! Form::select('due_id',$duesData,isset($result) ? $result->due_id: null,['class'=>'form-control dues-select','id'=>'due_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="due_id-form-error"></div>
                            </div>
                            </div>

                        <div  class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Select Type')}}<span class="red-star">*</span></label>
                                {!! Form::select('type',['renter'=>__('Renter'),'owner'=>__('Owner')],isset($result) ? $result->type: null,['class'=>'form-control type-select','id'=>'type-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="type-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Select Duration')}}<span class="red-star">*</span></label>
                                {!! Form::select('duration',['year'=>__('Year'),'month'=>__('Month'),'day'=>__('Day'),'one_time'=>__('One Time')],isset($result) ? $result->duration: null,['class'=>'form-control duration-select','id'=>'duration-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="duration-form-error"></div>
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

        noAjaxSelect2('.type-select','{{__('Type')}}','{{App::getLocale()}}');
        noAjaxSelect2('.duration-select','{{__('Duration')}}','{{App::getLocale()}}');
        noAjaxSelect2('.dues-select','{{__('Dues')}}','{{App::getLocale()}}');
        simpleAjaxSelect2('.property-select','property',1,'{{__('Properties')}}');


        $(document).ready(function () {
            $('.temp_text_editor').summernote({
                height:200,
            });
        });


        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.property-dues.update',$result->id):route('system.property-dues.store')}}',
                formData,
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
