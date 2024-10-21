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

                    {!! Form::open(['route' => isset($result) ? ['system.slider.update',$result->id]:'system.slider.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>


                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Title Ar')}}</label>
                                {!! Form::text('title_ar',isset($result) ? $result->title_ar: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="title_ar-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Title En')}}</label>
                                {!! Form::text('title_en',isset($result) ? $result->title_en: null,['class'=>'form-control','id'=>'title_en-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="title_en-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Description Ar')}}</label>
                                {!! Form::textarea('description_ar',isset($result) ? $result->description_ar: null,['class'=>'form-control','id'=>'description_ar-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="description_ar-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Description En')}}</label>
                                {!! Form::textarea('description_en',isset($result) ? $result->description_en: null,['class'=>'form-control','id'=>'description_en-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="description_en-form-error"></div>
                            </div>
                        </div>


                    </div>

                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                        <div class="form-group row">
                            <div class="col-md-2 col-image-upload">
                                <h6 class="image-upload-title"> {{__('Image')}} <span class="red-star">*</span></h6>
                                <label class="image-upload">
                                    <i class="flaticon2-plus image-upload-icon"></i>
                                    <img class="image-upload-src" @if(isset($result) && !empty($result->image) && is_file($result->image)) style="display:block;" src="{{asset($result->image)}}" @endif >
                                    {!! Form::file('image',['class'=>'form-control','style'=>'display:none;','id'=>'image-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="image-form-error"></div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Video URL')}}</label>
                                {!! Form::text('video_url',isset($result) ? $result->video_url: null,['class'=>'form-control','id'=>'video_url-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="video_url-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('URL')}}</label>
                                {!! Form::text('url',isset($result) ? $result->url: null,['class'=>'form-control','id'=>'url-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="url-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Status')}}</label>
                                {!! Form::select('status',['active'=> __('Active'),'in-active'=> __('In-Active')],isset($result) ? $result->status : null,['class'=>'form-control','id'=>'status-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="status-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Type')}}</label>
                                {!! Form::select('type',['main_web'=> __('Main web slider'),'main_mob'=> __('Main mob slider'),'board'=> __('Board slider'),'renter'=> __('Renter slider'),'owner'=> __('Owner slider')],isset($result) ? $result->type : null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="type-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Sort')}}</label>
                                {!! Form::text('sort',isset($result) ? $result->sort: 0,['class'=>'form-control','id'=>'sort-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="sort-form-error"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
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
                '{{isset($result) ? route('system.slider.update',$result->id):route('system.slider.store')}}',
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
