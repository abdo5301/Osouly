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
            {!! Form::open(['route' => isset($result) ? ['system.service.update',$result->id]:'system.service.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
            {!! Form::hidden('key',$randKey) !!}
            <div id="form-alert-message"></div>
            <div class="k-portlet k-portlet--mobile k-portlet--tabs">
                <div class="k-portlet__head">
                    <div class="k-portlet__head-toolbar">
                        <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link  active" data-toggle="tab" href="#k_portlet_base_demo_2_1_tab_content" role="tab">
                                    {{__('Service Data')}}
                                </a>
                            </li>
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" data-toggle="tab" href="#k_portlet_base_demo_2_2_tab_content" role="tab">--}}
                                    {{--{{__('Pricing')}}--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" data-toggle="tab" href="#k_portlet_base_demo_2_3_tab_content" role="tab">--}}
                                    {{--{{__('Discounts')}}--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" data-toggle="tab" href="#k_portlet_base_demo_2_4_tab_content" role="tab">--}}
                                    {{--{{__('Subscriptions')}}--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        </ul>
                    </div>
                </div>

                <div class="k-portlet__body">
                    <div class="tab-content">

                        <div class="tab-pane active" id="k_portlet_base_demo_2_1_tab_content" role="tabpanel">

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
                                <label>{{__('Content Ar')}}</label>
                                {!! Form::textarea('content_ar',isset($result) ? $result->content_ar: null,['class'=>'form-control text_editor','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="content_ar-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Content En')}}</label>
                                {!! Form::textarea('content_en',isset($result) ? $result->content_en: null,['class'=>'form-control text_editor','id'=>'content_en-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="content_en-form-error"></div>
                            </div>
                        </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Meta Key Ar')}}</label>
                                    {!! Form::textarea('meta_key_ar',isset($result) ? $result->meta_key_ar: null,['class'=>'form-control','id'=>'meta_key_ar-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="meta_key_ar-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Meta Key En')}}</label>
                                    {!! Form::textarea('meta_key_en',isset($result) ? $result->meta_key_en: null,['class'=>'form-control','id'=>'meta_key_en-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="meta_key_en-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Meta Description Ar')}}</label>
                                    {!! Form::textarea('meta_description_ar',isset($result) ? $result->meta_description_ar: null,['class'=>'form-control','id'=>'meta_description_ar-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="meta_description_ar-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Meta Description En')}}</label>
                                    {!! Form::textarea('meta_description_en',isset($result) ? $result->meta_description_en: null,['class'=>'form-control','id'=>'meta_description_en-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="meta_description_en-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Type')}}</label>
                                    {!! Form::select('type',['manage'=> __('management'),'star'=> __('Special Properties'),'ads'=> __('Ads')],isset($result) ? $result->type : null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="type-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Type Count')}}</label>
                                    {!! Form::text('type_count',isset($result) ? $result->type_count: null,['class'=>'form-control','id'=>'type_count-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="type_count-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Status')}}</label>
                                    {!! Form::select('status',['active'=> __('Active'),'in-active'=> __('In-Active')],isset($result) ? $result->status : null,['class'=>'form-control','id'=>'status-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="status-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Images')}}</label>
                                    <input type="file" class="form-control" id="images-form-input" autocomplete="off" name="images" multiple />
                                    <div class="invalid-feedback" id="images-form-error"></div>
                                </div>
                            </div>




                    </div>

                        <div class="tab-pane" id="k_portlet_base_demo_2_2_tab_content" role="tabpanel">

                        <div class="form-group row">
                        <div class="col-md-4">
                            <label>{{__('Price')}}</label>
                            {!! Form::text('price',isset($result) ? $result->price: null,['class'=>'form-control','id'=>'price-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="price-form-error"></div>
                        </div>
                        <div class="col-md-4">
                            <label>{{__('Offer')}}</label>
                            {!! Form::text('offer',isset($result) ? $result->offer: null,['class'=>'form-control','id'=>'offer-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="title_en-form-error"></div>
                        </div>
                        <div class="col-md-4">
                            <label>{{__('Percentage')}}</label>
                            {!! Form::text('percentage',isset($result) ? $result->percentage: null,['class'=>'form-control','id'=>'percentage-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="percentage-form-error"></div>
                        </div>
                    </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Properties Count')}}</label>
                                {!! Form::text('properties_count',isset($result) ? $result->properties_count: null,['class'=>'form-control','id'=>'properties_count-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="properties_count-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Duration') .' '. __('Per Day')}}</label>
                                {!! Form::text('duration',isset($result) ? $result->duration: null,['class'=>'form-control','id'=>'duration-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="duration-form-error"></div>
                            </div>
                        </div>
                    </div>

                        <div class="tab-pane" id="k_portlet_base_demo_2_3_tab_content" role="tabpanel">

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Discount Type')}}</label>
                                {!! Form::select('discount_type',['fixed'=> __('Fixed'),'percentage'=> __('Percentage')],isset($result) ? $result->discount_type : null,['class'=>'form-control','id'=>'discount_type-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="discount_type-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Discount Value')}}</label>
                                {!! Form::text('discount_value',isset($result) ? $result->discount_value: null,['class'=>'form-control','id'=>'discount_value-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="discount_value-form-error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Discount From')}}</label>
                                {!! Form::text('discount_from',(isset($result) && !empty($result->discount_from)) ? date('Y-m-d',strtotime($result->discount_from)) : null,['class'=>'form-control k_datepicker_1','id'=>'discount_from-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="discount_from-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Discount To')}}</label>
                                {!! Form::text('discount_to',(isset($result) && !empty($result->discount_to)) ? date('Y-m-d',strtotime($result->discount_to)) : null,['class'=>'form-control k_datepicker_1','id'=>'discount_to-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="discount_to-form-error"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Discount Code')}}</label>
                                {!! Form::text('discount_code',isset($result) ? $result->discount_code: null,['class'=>'form-control','id'=>'discount_code-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="discount_code-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Discount Code Value')}}</label>
                                {!! Form::text('discount_code_value',isset($result) ? $result->discount_code_value: null,['class'=>'form-control','id'=>'discount_code_value-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="discount_code_value-form-error"></div>
                            </div>
                        </div>
                            <div class="form-group row">
                            <div class="col-md-6">
                            <label>{{__('Discount Code From')}}</label>
                            {!! Form::text('discount_code_from',(isset($result) && !empty($result->discount_code_from)) ? date('Y-m-d',strtotime($result->discount_code_from)) : null,['class'=>'form-control k_datepicker_1','id'=>'discount_code_from-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="discount_code_from-form-error"></div>
                            </div>
                            <div class="col-md-6">
                            <label>{{__('Discount Code To')}}</label>
                            {!! Form::text('discount_code_to',(isset($result) && !empty($result->discount_code_to)) ? date('Y-m-d',strtotime($result->discount_code_to)) : null,['class'=>'form-control k_datepicker_1','id'=>'discount_code_to-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="discount_code_to-form-error"></div>
                            </div>
                            </div>
                    </div>

                        {{--<div class="tab-pane" id="k_portlet_base_demo_2_4_tab_content" role="tabpanel">--}}

                        {{--<div class="form-group row">--}}
                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Subscribers Count')}}</label>--}}
                                {{--{!! Form::text('subscribers_count',isset($result) ? $result->subscribers_count: null,['class'=>'form-control','id'=>'subscribers_count-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="subscribers_count-form-error"></div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Subscribe Monthly Value')}}</label>--}}
                                {{--{!! Form::text('subscribe_monthly',isset($result) ? $result->subscribe_monthly: null,['class'=>'form-control','id'=>'subscribe_monthly-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="subscribe_monthly-form-error"></div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group row">--}}
                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Subscribe From')}}</label>--}}
                                {{--{!! Form::text('subscribe_from',(isset($result) && !empty($result->subscribe_from)) ? date('Y-m-d',strtotime($result->subscribe_from)) : null,['class'=>'form-control k_datepicker_1','id'=>'subscribe_from-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="subscribe_from-form-error"></div>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Subscribe To')}}</label>--}}
                                {{--{!! Form::text('subscribe_to',(isset($result) && !empty($result->subscribe_to)) ? date('Y-m-d',strtotime($result->subscribe_to)) : null,['class'=>'form-control k_datepicker_1','id'=>'subscribe_to-form-input','autocomplete'=>'off']) !!}--}}
                                {{--<div class="invalid-feedback" id="subscribe_to-form-error"></div>--}}
                            {{--</div>--}}
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
                </div>

            </div>
        {!! Form::close() !!}
            <!-- end:: Content Body -->
        </div>
    </div>
    <!-- end:: Content -->
@endsection

@section('footer')
    <script src="{{asset('assets/uploader/jquery.fileuploader.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
    <script type="text/javascript">

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.service.update',$result->id):route('system.service.store')}}',
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

        $(document).ready(function() {
            function customImageThumb() {
                var listThumbs = $(".fileuploader-items-list li");
                for (let li of listThumbs) {
                    let imgThumb = $(li);
                    let imgUrl = imgThumb.find('.fileuploader-action-download').attr("href");
                    imgThumb.find('.fileuploader-item-image').html('<img width="60" height="60" src="'+ imgUrl +'" ">');
                }
            }

            function getImageSizeInBytes(imgURL) {
                var request = new XMLHttpRequest();
                request.open("HEAD", imgURL, false);
                request.send(null);
                var headerText = request.getAllResponseHeaders();
                var re = /Content\-Length\s*:\s*(\d+)/i;
                re.exec(headerText);
                return parseInt(RegExp.$1);
            }

            var oldImages = [];

                    @if( isset($result) && $result->images->isNotEmpty())

                    @foreach($result->images as $key => $value)
            var file = '{{asset($value->path)}}';
            var fileSize =  getImageSizeInBytes(file);
            //console.log(fileSize);
            oldImages.push({
                type: 'image',
                name: '{{$value->image_name}}',//'file.txt', // file name
                size: fileSize,//1024, // file size in bytes
                file: file,//'uploads/file.txt', // file path
                local: file,
                data: {
                    thumbnail:  '<img width="60" height="60" src="{{asset($value->path)}}">',
                    readerCrossOrigin: 'anonymous', // fix image cross-origin issue (optional)
                    readerForce: true, // prevent the browser cache of the image (optional)
                    readerSkip: true, // skip file from reading by rendering a thumbnail (optional)
                    popup: true, // remove the popup for this file (optional)
                    custom_key : '{{$value->custom_key}}',
                    image_name : '{{$value->image_name}}',
                }
            });
            @endforeach

                    @else
                oldImages = null;

            @endif

            // enable fileupload plugin
            $('#images-form-input').fileuploader({
                onSelect: function(item) {
                    // if (!item.html.find('.fileuploader-action-start').length)
                    //  item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-start" title="Upload"><i></i></a>');
                },
                upload: {
                    url: '{{route('system.service.image-upload')}}',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'key':  '{{$randKey}}',
                        'service_id': '{{ isset($result) ? $result->id : NULL }}'
                    },
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    start: true,
                    synchron: true,
                    onSuccess: function(result, item) {

                        //console.log(item);
                        //console.log(result);

                        item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
                        item.html.find('.fileuploader-item-image').html('<img width="60" height="60" src="'+ result.path +'" ">');
                    },
                    onError: function(item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {

                        // console.log(jqXHR);


                        item.upload.status != 'cancelled' && item.html.find('.fileuploader-action-retry').length == 0 ? item.html.find('.column-actions').prepend(
                            '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
                        ) : null;
                    },
                    onComplete: null,
                },
                files: oldImages,
                addMore: true,


                onRemove: function(item) {
                    //console.log(oldImages[0]);
                    //console.log(item);
                    // send POST request
                    var imageName = item.name;
                    var randKey = '{{$randKey}}';
                    if(item.data.image_name){
                        imageName = item.data.image_name;
                    }
                    if(item.data.custom_key){
                        randKey = item.data.custom_key;
                    }

                    $.post('{{route('system.service.remove-image')}}', {
                        'name': imageName,
                        '_token': '{{csrf_token()}}',
                        'key':  randKey
                    });
                }
            });
            customImageThumb();
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
    <link href="{{asset('assets/uploader/font/font-fileuploader.css')}}" rel="stylesheet">
    <link href="{{asset('assets/uploader/jquery.fileuploader.min.css')}}" media="all" rel="stylesheet">
@endsection