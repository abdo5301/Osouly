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

                {!! Form::open(['route' => isset($result) ? ['system.request.update',$result->id]:'system.request.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                   <input type="hidden" name="lead_data" @if(!empty($_GET['lead_data'])) value="{{$_GET['lead_data']}}" @endif >
                    <div class="k-portlet__body" style="background: #FFF;">

                        <div id="form-alert-message"></div>

                    <div class="form-group row" id="client-select-information">

                            <div class="col-md-10">
                                <label>{{__('Renter')}}<span class="red-star">*</span></label>
                                @php
                                $clientViewSelect = [''=> __('Select Renter')];
                                if(isset($result)){
                                    $clientViewSelect[$result->renter_id] = $result->renter->Fullname;
                                }
                                @endphp
                                {!! Form::select('renter_id',$clientViewSelect,isset($result) ? $result->renter_id: null,['class'=>'form-control client-select','id'=>'renter_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="renter_id-form-error"></div>
                            </div>

                            <div class="col-md-2">
                                <label style="color: #FFF;">*</label>
                                <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.renter.create',['addClientFromProperty'=>'true','type'=>'renter'])}}');" class="form-control">
                                    <i class="la la-plus"></i>
                                </a>
                            </div>
                        </div>
                        </div>


                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
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
                            <label>{{__('Status')}}<span class="red-star">*</span></label>
                            @php
                               // $purposesData = [''=>__('Select Status')];
                               $statusData = array();
                                foreach ($request_status as $key => $value){
                                    $statusData[$value] = __(ucfirst($value));
                                }
                            @endphp
                            {!! Form::select('status',$statusData,isset($result) ? $result->status: null,['class'=>'form-control','id'=>'status-form-input','autocomplete'=>'off']) !!}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        simpleAjaxSelect2('.client-select','renter',2,'{{__('Renter')}}');
        simpleAjaxSelect2('.property-select','property',1,'{{__('Properties')}}');

        function submitMainForm(){
                formSubmit(
                    '{{isset($result) ? route('system.request.update',$result->id):route('system.request.store')}}',
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

        $(document).ready(function(){
            $('.multiple-select2').select2();

        });

        window.closeModal = function(){
            $('#modal-iframe').modal('hide');
        };

    </script>
@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
        <style>

            .select2-container--default .selection .select2-selection--multiple{
                width: 100%;
                display: inline-table;
            }

            .select2-container--default .select2-selection--single .select2-selection__clear{
                font-size: large;
                color: red;
                @if( App::getLocale() !== 'ar')
                /*margin-right: -12px;*/
                float: right !important;
                @else
                 margin-left: -12px;
                @endif
        }

            .select2-selection__arrow{
            @if( App::getLocale() !== 'ar')
               left: auto !important;
               right: 1px !important;
            @endif
            }

            .select2-search__field{
                @if( App::getLocale() !== 'ar')
                direction: ltr;
                @endif
             }

            /*.select2-selection--multiple .select2-search--inline .select2-search__field { width: auto !important; }*/

        </style>
@endsection
