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

                    {!! Form::open(['route' => isset($result) ? ['system.special-property.update',$result->id]:'system.special-property.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Select Property')}}</label>
                                @php
                                    $properties = [''=> __('Select Property')];
                                    if(isset($result)){
                                    $properties[$result->property_id] = " شقة رقم ".$result->property->flat_number .' - الدور ' .$result->property->floor.' - رقم المبنى '.$result->property->building_number.' - الشارع '.$result->property->street_name;
                                    }
                                @endphp
                                {!! Form::select('property_id',$properties,isset($result) ? $result->property_id: null,['class'=>'form-control property-select','id'=>'property_id-form-input','autocomplete'=>'off']) !!}
                                {{--{!! Form::number('property_id',isset($result) && !empty($result->property_id) ? $result->property_id: null,['class'=>'form-control','id'=>'property_id-form-input','autocomplete'=>'off']) !!}--}}
                                <div class="invalid-feedback" id="property_id-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Client Package ID')}}</label>
                                {!! Form::text('client_package_id',isset($result) && !empty($result->client_package_id) ? $result->client_package_id: null,['class'=>'form-control','id'=>'client_package_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_package_id-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Created By')}}<span class="red-star">*</span></label>
                                @php
                                    $clients = [''=> __('Select Client')];
                                    if(isset($result)){
                                    $clients[$result->created_by] = $result->created_by_client->fullname .' ( ' .__(ucwords($result->created_by_client->type)).' )';
                                    }
                                @endphp
                                {!! Form::select('created_by',$clients,isset($result) ? $result->created_by: null,['class'=>'form-control client-select','id'=>'created_by-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="created_by-form-error"></div>
                                {{--<input name="client_id" value="2" type="number">--}}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Start Date')}}<span class="red-star">*</span></label>
                                {!! Form::text('start_date',(isset($result) && !empty($result->start_date)) ? date('Y-m-d',strtotime($result->start_date)) : null,['class'=>'form-control k_datepicker_1','id'=>'start_date-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="start_date-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('End Date')}}<span class="red-star">*</span></label>
                                {!! Form::text('end_date',(isset($result) && !empty($result->end_date)) ? date('Y-m-d',strtotime($result->end_date)) : null,['class'=>'form-control k_datepicker_1','id'=>'end_date-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="end_date-form-error"></div>
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

        simpleAjaxSelect2('.client-select','clients',2,'{{__('Client')}}');
        simpleAjaxSelect2('.property-select','property',1,'{{__('Properties')}}');


        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.special-property.update',$result->id):route('system.special-property.store')}}',
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
