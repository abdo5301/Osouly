@extends('system.layout')
@section('content')
    <div class="modal fade" id="vars-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Variables')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" style="width: 100%">
                        <thead>
                        <tr>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Value')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vars as $key=>$var)
                        <tr>
                            <td>{{__(ucwords(str_replace('_',' ',$key)))}}</td>
                            <td>{{$var}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
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
                    <a href="#" data-toggle="modal" data-target="#vars-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Variables')}}" data-placement="left">
                        <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Variables')}}</span>
                        <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- end:: Content Head -->


        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__body" style="background: #f7f7fb;">

                    {!! Form::open(['route' => isset($result) ? ['system.contract.update',$result->id]:'system.contract.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Select Property')}}<span class="red-star">*</span></label>
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
                                <label>{{__('Renter')}}<span class="red-star">*</span></label>
                                @php
                                    $renterViewSelect = [''=> __('Select Renter')];
                                    if(isset($result)){
                                        $renterViewSelect[$result->renter_id] = $result->renter->fullname;
                                    }
                                @endphp
                                {!! Form::select('renter_id',$renterViewSelect,isset($result) ? $result->renter_id: null,['class'=>'form-control renter-select','id'=>'renter_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="renter_id-form-error"></div>
                            </div>
                            {{--<input type="hidden" value="2" name="renter_id">--}}
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Select Contract Type')}}<span class="red-star">*</span></label>
                                {!! Form::select('contract_type',['year'=>__('Year'),'month'=>__('Month'),'day'=>__('Day')],isset($result) ? $result->contract_type: null,['class'=>'form-control contract-type-select','id'=>'contract_type-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="contract_type-form-error"></div>
                            </div>
                            </div>

                        @if(!isset($result))
                        <div class="form-group row">
                        <div class="col-md-12">
                                <label>{{__('Select Contract Template')}}<span class="red-star">*</span></label>
                                @php
                                    $tempsData = [''=>__('Select Contract Template')];
                                    foreach ($contract_templates as $key => $value){
                                        $tempsData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('contract_template_id',$tempsData,isset($result) ? $result->contract_template_id: null,['class'=>'form-control contract-template-select','id'=>'contract_template_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="contract_template_id-form-error"></div>
                            </div>
                        </div>
                        @endif

                        <div  class="form-group row">
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

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Price')}}<span class="red-star">*</span></label>
                                {!! Form::text('price',isset($result) ? $result->price: null,['class'=>'form-control','id'=>'price-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Deposit Rent')}}<span class="red-star">*</span></label>
                                {!! Form::text('deposit_rent',isset($result) ? $result->deposit_rent: null,['class'=>'form-control','id'=>'deposit_rent-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="deposit_rent-form-error"></div>
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Insurance Price')}}<span class="red-star">*</span></label>
                                {!! Form::text('insurance_price',isset($result) ? $result->insurance_price: null,['class'=>'form-control','id'=>'insurance_price-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="insurance_price-form-error"></div>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Pay From')}}<span class="red-star">*</span></label>
                                {!! Form::text('pay_from',(isset($result) && !empty($result->pay_from)) ? date('Y-m-d',strtotime($result->pay_from)) : null,['class'=>'form-control k_datepicker_1','id'=>'pay_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="pay_from-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Pay To')}}<span class="red-star">*</span></label>
                                {!! Form::text('pay_to',(isset($result) && !empty($result->pay_to)) ? date('Y-m-d',strtotime($result->pay_to)) : null,['class'=>'form-control k_datepicker_1','id'=>'pay_to-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="pay_to-form-error"></div>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Pay Every').' ('.__('By Month').') '}}<span class="red-star">*</span></label>
                                {!! Form::text('pay_every',isset($result) ? $result->pay_every: null,['class'=>'form-control','id'=>'pay_every-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="pay_every-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Pay At')}}<span class="red-star">*</span></label>
                                {!! Form::select('pay_at',['start'=>__('Start'),'end'=>__('End')],isset($result) ? $result->pay_at: null,['class'=>'form-control pay-at-select','id'=>'pay_at-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="pay_at-form-error"></div>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Limit To Pay').' ('.__('By Month').') '}}<span class="red-star">*</span></label>
                                {!! Form::text('limit_to_pay',isset($result) ? $result->limit_to_pay: null,['class'=>'form-control','id'=>'limit_to_pay-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="limit_to_pay-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Calendar')}}<span class="red-star">*</span></label>
                                {!! Form::select('calendar',['m'=>__('Gregorian'),'h'=>__('Hijri')],isset($result) ? $result->calendar: null,['class'=>'form-control calendar-select','id'=>'calendar-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="calendar-form-error"></div>
                            </div>
                        </div>

                        <div  class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Increase Value')}}<span class="red-star">*</span></label>
                                {!! Form::text('increase_value',isset($result) ? $result->increase_value: null,['class'=>'form-control','id'=>'increase_value-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="increase_value-form-error"></div>
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Increase Percentage')}}<span class="red-star">*</span></label>
                                {!! Form::text('increase_percentage',isset($result) ? $result->increase_percentage: null,['class'=>'form-control','id'=>'increase_percentage-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="increase_percentage-form-error"></div>
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Increase From')}}<span class="red-star">*</span></label>
                                {!! Form::text('increase_from',(isset($result) && !empty($result->increase_from)) ? date('Y-m-d',strtotime($result->increase_from)) : null,['class'=>'form-control k_datepicker_1','id'=>'increase_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="increase_from-form-error"></div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Status')}}<span class="red-star">*</span></label>
                                {!! Form::select('status',['pendding'=> __('Pending'),'active'=> __('Active'),'cancel'=> __('Canceled')],isset($result) ? $result->status : null,['class'=>'form-control status-select','id'=>'status-form-input','autocomplete'=>'off']) !!}
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

        noAjaxSelect2('.contract-type-select','{{__('Contract Type')}}','{{App::getLocale()}}');
        noAjaxSelect2('.contract-template-select','{{__('Contract Template')}}','{{App::getLocale()}}');
        noAjaxSelect2('.status-select','{{__('Status')}}','{{App::getLocale()}}');
        simpleAjaxSelect2('.renter-select','renter',2,'{{__('Renter')}}');
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
                '{{isset($result) ? route('system.contract.update',$result->id):route('system.contract.store')}}',
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
