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

                {!! Form::open(['route' => isset($result) ? ['system.importer.update',$result->id]:'system.importer.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

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


                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label>{{__('Connector')}}<span class="red-star">*</span></label>
                                    {!! Form::select('connector',['OLX'=> __('OLX'),'Aqarmap'=> __('Aqarmap')],isset($result) ? $result->connector : null,['class'=>'form-control','id'=>'connector-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="connector-form-error"></div>
                                </div>

                                <div class="col-md-4">
                                    <label>{{__('Type')}}<span class="red-star">*</span></label>
                                    @php
                                        $typesData = [''=>__('Select Type')];
                                        foreach ($property_types as $key => $value){
                                            $typesData[$value->id] = $value->name;
                                        }
                                    @endphp
                                    {!! Form::select('property_type_id',$typesData,isset($result) ? $result->property_type_id: null,['class'=>'form-control','id'=>'property_type_id-form-input','onchange'=>'propertyType();','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="property_type_id-form-error"></div>
                                </div>

                                <div class="col-md-4">
                                    <label>{{__('Purpose')}}<span class="red-star">*</span></label>
                                    @php
                                        $purposesData = [''=>__('Select Purpose')];
                                     foreach ($purposes as $key => $value){
                                            $purposesData[$value->id] = $value->name;
                                        }
                                    @endphp
                                    {!! Form::select('purpose_id',$purposesData,isset($result) ? $result->purpose_id: null,['class'=>'form-control','id'=>'purpose_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="purpose_id-form-error"></div>
                                </div>

                            </div>


                        <div class="form-group row">

                            <div class="col-md-12">
                                <label>{{__('Query')}}</label>
                                {!! Form::text('query_name',null,['class'=>'form-control','id'=>'query_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="query_name-form-error"></div>
                            </div>

                        </div>


                        <div class="form-group row">

                            <div class="col-md-12">
                                <label>{{__('Area')}}<span class="red-star">*</span></label>
                                @php
                                    $areaViewSelect = [''=> __('Select Area')];
                                @endphp
                                {!! Form::select('area_id',$areaViewSelect,isset($result) ? $result->area_id: null,['class'=>'form-control area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="area_id-form-error"></div>
                            </div>

                        </div>


                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Space From')}}</label>
                                {!! Form::number('space_from',isset($result) ? $result->space_from : null,['class'=>'form-control','id'=>'space_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="space_from-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Space To')}}</label>
                                {!! Form::number('space_to',isset($result) ? $result->space_to : null,['class'=>'form-control','id'=>'space_to-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="space_to-form-error"></div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Price From')}}</label>
                                {!! Form::number('price_from',isset($result) ? $result->price_from: null,['class'=>'form-control','id'=>'price_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price_from-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Price To')}}</label>
                                {!! Form::number('price_to',isset($result) ? $result->price_to: null,['class'=>'form-control','id'=>'price_to-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price_to-form-error"></div>
                            </div>
                        </div>



                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Page Start')}}<span class="red-star">*</span></label>
                                {!! Form::number('page_start',isset($result) ? $result->page_start: null,['class'=>'form-control','id'=>'page_start-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="page_start-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Page End')}}<span class="red-star">*</span></label>
                                {!! Form::number('page_end',isset($result) ? $result->page_end: null,['class'=>'form-control','id'=>'page_end-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="page_end-form-error"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

    <script type="text/javascript">

        ajaxSelect2('.area-select','area',3);

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.importer.update',$result->id):route('system.importer.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    if($data.status == true){
                        window.location = $data.data.url;
                    }else{
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',$data.message);
                    }
                },
                function ($data){
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

    </script>
@endsection

@section('header')
        <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
@endsection