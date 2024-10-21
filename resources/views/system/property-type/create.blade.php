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

                {!! Form::open(['route' => isset($result) ? ['system.property-type.update',$result->id]:'system.property-type.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
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
                                <label>{{__('OLX ID')}}</label>
                                {!! Form::text('olx_id',isset($result) ? $result->olx_id : null,['class'=>'form-control','id'=>'olx_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="olx_id-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label>{{__('Aqarmap ID')}}</label>
                                {!! Form::text('aqarmap_id',isset($result) ? $result->aqarmap_id : null,['class'=>'form-control','id'=>'aqarmap_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="aqarmap_id-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2 col-image-upload">
                                <h6 class="image-upload-title"> {{__('Image')}}</h6>
                                <label class="image-upload">
                                    <i class="flaticon2-plus image-upload-icon"></i>
                                    <img class="image-upload-src" @if(isset($result) && !empty($result->image) && is_file($result->image)) style="display:block;" src="{{asset($result->image)}}" @endif >
                                    {!! Form::file('image',['class'=>'form-control','style'=>'display:none;','id'=>'image-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="image-form-error"></div>
                                </label>
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
    <script type="text/javascript">

        $('body').on('change','input:file', function () {
            let input = this;
            input = $(this);
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    input.parent().find('.image-upload-src').css('display','block');
                    input.parent().find('.image-upload-src').attr('src',e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.property-type.update',$result->id):route('system.property-type.store')}}',
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
        <style>
            .image-upload{
                width: 90px;
                height: 90px;
                border: solid 1px #E8EBE8;
                border-radius: 5px;
                text-align: center;
                cursor: pointer;
                z-index: 9999;
            }

            .col-image-upload{
                @if( App::getLocale() !== 'ar')
margin-right: -40px;
                @else
margin-left: -40px;
            @endif
}

            .image-upload-icon{
                position: absolute;
                margin-top: 24px;
                opacity: 0.4;
                border: 1px solid #ddd;
                border-radius: 50%;
                padding: 10px;
                font-size: 12px;
                @if( App::getLocale() !== 'ar')
margin-left: -18px;
                @else
margin-right: -18px;
            @endif
}

            .image-upload-src{
                width: 100%;
                height: 100%;
                display: none;
                border-radius: 5px;
            }

            .image-upload-title{
                padding-right: 4px;
            }

        </style>
@endsection