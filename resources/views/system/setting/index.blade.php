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
            {!! Form::open(['route' => 'system.setting.update','method' => 'PATCH' ,'files' => true]) !!}

            @if($errors->any())
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
            @endif

            <div class="k-portlet k-portlet--tabs">
                <div class="k-portlet__head">
                    <div class="k-portlet__head-toolbar">
                        <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
                            @foreach($settingGroups as $key => $value)
                                <li class="nav-item">
                                    <a class="nav-link @if($key == 0) active @endif" data-toggle="tab" href="#k_portlet_base_demo_2_{{$key}}_tab_content" role="tab">
                                        {{__(title_case(str_replace('_',' ',$value->group_name)))}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="k-portlet__body">


                    <div class="tab-content">
                        @foreach($settingGroups as $key => $value)
                        <div class="tab-pane @if($key == 0) active @endif" id="k_portlet_base_demo_2_{{$key}}_tab_content" role="tabpanel">
                            @foreach($setting[$key] as $sKey => $sValue)
                                @if($sValue->input_type == 'text')
                                    <div class="form-group row">
                                        {!! Form::label($sValue->name,$sValue->{'shown_name_'.\App::getLocale()},['class'=>'col-3 col-form-label']) !!}
                                        <div class="col-9">
                                            {!! Form::text($sValue->name,$sValue->value,['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                @elseif($sValue->input_type == 'number')
                                    <div class="form-group row">
                                        {!! Form::label($sValue->name,$sValue->{'shown_name_'.\App::getLocale()},['class'=>'col-3 col-form-label']) !!}
                                        <div class="col-9">
                                            {!! Form::number($sValue->name,$sValue->value,['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                @elseif($sValue->input_type == 'textarea')
                                    <div class="form-group row">
                                        {!! Form::label($sValue->name,$sValue->{'shown_name_'.\App::getLocale()},['class'=>'col-3 col-form-label']) !!}
                                        <div class="col-9">
                                            {!! Form::textarea($sValue->name,$sValue->value,['class'=>'form-control','rows'=>3]) !!}
                                        </div>
                                    </div>
                                @elseif($sValue->input_type == 'image')
                                    <div class="form-group row">
                                        {!! Form::label($sValue->name,$sValue->{'shown_name_'.\App::getLocale()},['class'=>'col-3 col-form-label']) !!}
                                        <div  @if($sValue->value) class="col-7" @else class="col-9" @endif>
                                            {!! Form::file($sValue->name,['class'=>'form-control','rows'=>3]) !!}
                                        </div>
                                        @if($sValue->value)
                                            <div class="col-2">
                                                <a target="_blank" href="{{asset($sValue->value)}}">{{__('View')}}</a>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($sValue->input_type == 'select')
                                    <div class="form-group row">
                                        {!! Form::label($sValue->name,$sValue->{'shown_name_'.\App::getLocale()},['class'=>'col-3 col-form-label']) !!}
                                        <div class="col-9">
                                            @php
                                                $listValues = $sValue->option_list;
                                                $listSelect = [];
                                                foreach($listValues as $lKey => $lValue){
                                                $listSelect[$lKey] = __($lValue);
                                                }
                                            @endphp
                                            {!! Form::select($sValue->name,$listSelect,$sValue->value,['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endforeach

                            <div class="k-portlet__foot">
                                <div class="k-form__actions">
                                    <div class="row" style="float: right;">
                                        <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

            <!-- end:: Content Body -->
        </div>
        <!-- end:: Content -->
        @endsection
        @section('footer')

        @endsection