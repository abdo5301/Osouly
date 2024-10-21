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

                {!! Form::open(['route' => isset($result) ? ['system.area.update',$result->id]:'system.area.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
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
                        @if(isset($area_type))
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="controls">
                                    <label>{{__('Area Type')}}</label>
                                    <input disabled="disabled" value="{{implode(' -> ',\App\Libs\AreasData::getAreaTypesUp($area_type->id))}}" class="form-control">
                                </div>
                            </div>
                        </div>
                            {!! Form::hidden('area_type_id',$area_type->id) !!}
                        @endif

                        @if(isset($area))
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="controls">
                                    <label>{{__('Area Route')}}</label>
                                    <input disabled="disabled" value="{{implode(' -> ',\App\Libs\AreasData::getAreasUp($area->id,true))}}" class="form-control">
                                </div>
                            </div>
                        </div>
                            {!! Form::hidden('area_id',$area->id) !!}
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
                            <div class="col-md-6">
                                <label>{{__('Latitude')}}</label>
                                {!! Form::text('latitude',isset($result) ? $result->latitude : null,['class'=>'form-control','id'=>'latitude-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="latitude-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label>{{__('Longitude')}}</label>
                                {!! Form::text('longitude',isset($result) ? $result->longitude : null,['class'=>'form-control','id'=>'longitude-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="longitude-form-error"></div>
                            </div>

                            {{--<div class="col-md-4">--}}
                                {{--<label>{{__('has_property_model')}}<span class="red-star">*</span></label>--}}
                                {{--{!! Form::select('has_property_model',['0'=>__('No'),'1'=>__('Yes')],isset($result) ? $result->has_property_model : null,['class'=>'form-control','id'=>'has_property_model-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="has_property_model-form-error"></div>--}}
                            {{--</div>--}}
                        </div>

                        {{--<div class="form-group row">--}}
                            {{--<div class="col-md-4">--}}
                                {{--<label>{{__('OLX ID')}}<span class="red-star">*</span></label>--}}
                                {{--{!! Form::text('olx_id',isset($result) ? $result->olx_id : null,['class'=>'form-control','id'=>'olx_id-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="olx_id-form-error"></div>--}}
                            {{--</div>--}}

                            {{--<div class="col-md-4">--}}
                                {{--<label>{{__('Aqarmap ID')}}<span class="red-star">*</span></label>--}}
                                {{--{!! Form::text('aqarmap_id',isset($result) ? $result->aqarmap_id : null,['class'=>'form-control','id'=>'aqarmap_id-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="aqarmap_id-form-error"></div>--}}
                            {{--</div>--}}

                            {{--<div class="col-md-4">--}}
                                {{--<label>{{__('Property Finder ID')}}<span class="red-star">*</span></label>--}}
                                {{--{!! Form::text('propertyfinder_id',isset($result) ? $result->propertyfinder_id : null,['class'=>'form-control','id'=>'propertyfinder_id-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="propertyfinder_id-form-error"></div>--}}
                            {{--</div>--}}

                        {{--</div>--}}


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
                '{{isset($result) ? route('system.area.update',$result->id):route('system.area.store')}}',
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