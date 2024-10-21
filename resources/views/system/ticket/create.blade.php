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

                    {!! Form::open(['route' =>  'system.ticket.store','files'=>true, 'method' =>  'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>

                        <div id="select-clients-div" class="form-group row">
                            <div  class="col-md-12 param-div">
                                {{--@php--}}
                                {{--$salesViewSelect = array();--}}
                                {{--$salesViewSelect = $salesViewSelect+array_column(getStaff()->toArray(),'name','id');--}}
                                {{--@endphp--}}
                                <label>{{__('Select Client')}}</label>
                                {!! Form::select('client_id',array(),null,['style'=>'width: 100%','class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_id-form-error"></div>
                                {{--<input name="client_id" value="2">--}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Title')}}</label>
                                {!! Form::text('ticket_title',null,['class'=>'form-control','id'=>'ticket_title-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="ticket_title-form-error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Content')}}</label>
                                {!! Form::textarea('ticket_content',null,['class'=>'form-control ticket_text_editor','rows'=>'4','id'=>'ticket_content-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="ticket_content-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2 col-image-upload">
                                <h6 class="image-upload-title"> {{__('Image')}} </h6>
                                <label class="image-upload">
                                    <i class="flaticon2-plus image-upload-icon"></i>
                                    <img class="image-upload-src" @if(isset($result) && !empty($result->ticket_image) && is_file($result->ticket_image)) style="display:block;" src="{{asset($result->ticket_image)}}" @endif >
                                    {!! Form::file('ticket_image',['class'=>'form-control','style'=>'display:none;','id'=>'ticket_image-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="ticket_image-form-error"></div>
                                </label>
                            </div>
                        </div>

                        <div class="k-portlet__foot">
                            <div class="k-form__actions">
                                <div class="row" style="float: right;">
                                    <button type="submit" class="btn btn-primary">{{__('Send')}}</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>

    <script type="text/javascript">
        simpleAjaxSelect2('.client-select','clients',2,'{{__('Clients')}}');
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
                '{{route('system.ticket.store')}}',
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

        $('.ticket_text_editor').summernote({
            height:200,
        });
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