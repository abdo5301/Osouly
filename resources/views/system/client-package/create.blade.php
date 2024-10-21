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

                    {!! Form::open(['route' => isset($result) ? ['system.client-package.update',$result->id]:'system.client-package.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Service')}}<span class="red-star">*</span></label>
                                @php
                                    $servicesData = [''=>__('Services')];
                                    foreach ($services as $key => $value){
                                        $servicesData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('service_id',$servicesData,isset($result) ? $result->service_id: null,['style'=>'width: 100%','class'=>'form-control services-select','id'=>'service_id-form-input','autocomplete'=>'off','onChange'=>'getPackages()']) !!}
                                <div class="invalid-feedback" id="service_id-form-error"></div>
                            </div>
                        </div>
                        <div class="form-group row" id="packages-row" style="display: none;">
                        </div>
                        <div class="form-group row">
                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Service Count')}}</label>--}}
                                {{--{!! Form::number('service_count',isset($result) && !empty($result->service_count) ? $result->service_count: null,['class'=>'form-control','id'=>'service_count-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="service_count-form-error"></div>--}}
                            {{--</div>--}}

                            <div class="col-md-12">
                                <label>{{__('Select Client')}}<span class="red-star">*</span></label>
                                @php
                                $clients = [''=> __('Select Client')];
                                if(isset($result)){
                                $clients[$result->client_id] = $result->client->fullname .' ( ' .__(ucwords($result->client->type)).' )';
                                }
                                @endphp
                                {!! Form::select('client_id',$clients,isset($result) ? $result->client_id: null,['class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_id-form-error"></div>
                                {{--<input name="client_id" value="2" type="number">--}}
                            </div>
                            </div>


                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Transaction ID')}}</label>
                                {!! Form::number('transaction_id',isset($result) && !empty($result->transaction_id) ? $result->transaction_id: null,['class'=>'form-control','id'=>'transaction_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="transaction_id-form-error"></div>
                            </div>
                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Count Per Day')}}</label>--}}
                                {{--{!! Form::number('count_per_day',isset($result) && !empty($result->count_per_day) ? $result->count_per_day: null,['class'=>'form-control','id'=>'count_per_day-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="count_per_day-form-error"></div>--}}
                            {{--</div>--}}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Date From')}}<span class="red-star">*</span></label>
                                {!! Form::text('date_from',(isset($result) && !empty($result->date_from)) ? date('Y-m-d',strtotime($result->date_from)) : null,['class'=>'form-control k_datepicker_1','id'=>'date_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="date_from-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Date To')}}<span class="red-star">*</span></label>
                                {!! Form::text('date_to',(isset($result) && !empty($result->date_to)) ? date('Y-m-d',strtotime($result->date_to)) : null,['class'=>'form-control k_datepicker_1','id'=>'date_to-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="date_to-form-error"></div>
                            </div>
                         </div>

                        <div  class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Select Status')}}<span class="red-star">*</span></label>
                                {!! Form::select('status',['active'=>__('Active'),'pendding'=>__('Pending'),'in-active'=>__('In-Active'),'cancel'=>__('Cancel'),'expired'=>__('Expired')],isset($result) ? $result->status: null,['class'=>'form-control status-select','id'=>'status-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="status-form-error"></div>
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

        noAjaxSelect2('.status-select','{{__('Status')}}','{{App::getLocale()}}');
        simpleAjaxSelect2('.client-select','clients',2,'{{__('Client')}}');
        noAjaxSelect2('.services-select','{{__('Services')}}','{{App::getLocale()}}');


        $(document).ready(function () {
            $('.notes_text_editor').summernote({
                height:200,
            });
        });


            function getPackages() {
                addLoading();
                $('#packages-row').css('display','none');
                $('#packages-row').html('');

                $.get(
                    '{{route('system.misc.ajax')}}',
                    {
                        'type':'select_service_packages',
                        'service_id': $('#service_id-form-input').val(),
                    },
                    function(data){
                        //console.log(data);
                        if(data.length){
                            $('#packages-row').html(data);
                            $('#packages-row').css('display','block');
                        }

                    }
                );
                removeLoading();
            }



        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.client-package.update',$result->id):route('system.client-package.store')}}',
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

        .form-control[readonly] {
            background-color: #1dc9b70a;
        }
    </style>
@endsection
