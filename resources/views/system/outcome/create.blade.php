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

                    {!! Form::open(['route' => isset($result) ? ['system.outcome.update',$result->id]:'system.outcome.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

                        <div id="form-alert-message"></div>

                        <div  class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Outcome Price')}}<span class="red-star">*</span></label>
                                {!! Form::text('price',isset($result) ? $result->price: null,['class'=>'form-control','id'=>'price-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label>{{__('Date')}}<span class="red-star">*</span></label>
                                {!! Form::text('date',(isset($result) && !empty($result->date)) ? date('Y-m-d',strtotime($result->date)) : null,['class'=>'form-control k_datepicker_1','id'=>'date-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="date-form-error"></div>
                            </div>
                        </div>


                        <div  class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Outcome Reason')}}<span class="red-star">*</span></label>
                                @php
                                    $reasonsData = [''=>__('Outcome Reasons')];
                                    foreach ($reasons as $key => $value){
                                        $reasonsData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('reason_id',$reasonsData,isset($result) ? $result->sign_id: null,['style'=>'width: 100%','class'=>'form-control reasons-select','id'=>'reason_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="reason_id-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Locker')}}<span class="red-star">*</span></label>
                                @php
                                    $lockersData = [''=>__('Locker')];
                                    foreach ($lockers as $key => $value){
                                        $lockersData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('locker_id',$lockersData,isset($result) ? $result->locker_id: null,['style'=>'width: 100%','class'=>'form-control locker-select','id'=>'locker_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="locker_id-form-error"></div>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Payment Method')}}</label>
                                @php
                                    $methodsData = [''=>__('Payment Methods')];
                                    foreach ($payment_methods as $key => $value){
                                        $methodsData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('payment_method_id',$methodsData,isset($result) ? $result->payment_method_id: null,['style'=>'width: 100%','class'=>'form-control payment-method-select','id'=>'payment_method_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="payment_method_id-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Client')}}</label>
                                @php
                                    $clientsData = array();
                                    if(isset($result) && isset($result->client_id)){
                                       $clientsData[$result->client_id] = $result->client->fullname;
                                    }
                                @endphp
                                {!! Form::select('client_id',array(),isset($result) ? $result->client_id: null,['style'=>'width: 100%','class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_id-form-error"></div>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <div  class="col-md-12 param-div">
                                <label>{{__('Select Staff')}}</label>
                                @php
                                    $staffData = array();
                                    if(isset($result) && isset($result->staff_id)){
                                       $staffData[$result->staff_id] = $result->staff->fullname;
                                    }
                                @endphp
                                {!! Form::select('staff_id',$staffData,isset($result) ? $result->staff_id: null,['style'=>'width: 100%','class'=>'form-control staff-select','id'=>'staff_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="staff_id-form-error"></div>
                            </div>
                        </div>




                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Notes')}}</label>
                                {!! Form::textarea('note',isset($result) ? $result->note: null,['class'=>'form-control','id'=>'note-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="note-form-error"></div>
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
                simpleAjaxSelect2('.client-select','clients',2,'{{__('Clients')}}');
                simpleAjaxSelect2('.staff-select','staff',2,'{{__('Staff')}}');

                noAjaxSelect2('.payment-method-select','{{__('Payment Methods')}}','{{App::getLocale()}}');
                noAjaxSelect2('.reasons-select','{{__('Outcome Reasons')}}','{{App::getLocale()}}');
                noAjaxSelect2('.locker-select','{{__('Locker')}}','{{App::getLocale()}}');


                function submitMainForm(){
                    formSubmit(
                        '{{isset($result) ? route('system.outcome.update',$result->id):route('system.outcome.store')}}',
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