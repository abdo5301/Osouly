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

                    {!! Form::open(['route' => isset($result) ? ['system.page.update',$result->id]:'system.page.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    {!! Form::hidden('key',$randKey) !!}
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
                    </div>


                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
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
                            <div class="col-md-12">
                                <label>{{__('Images')}}</label>
                                <input type="file" class="form-control" id="images-form-input" autocomplete="off" name="images" multiple />
                                <div class="invalid-feedback" id="images-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Video URL')}}</label>
                                {!! Form::url('video_url',isset($result) ? $result->video_url: null,['class'=>'form-control','id'=>'video_url-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="video_url-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Sort')}}</label>
                                {!! Form::text('sort',isset($result) ? $result->sort: null,['class'=>'form-control','id'=>'sort-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="sort-form-error"></div>
                            </div>
                        </div>
                        </div>



                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                        {{-- About us page only --}}
                        @if(isset($result) && $result->id == 1)
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Paragraph Title Ar (1)')}}</label>
                                {!! Form::text('p_title_ar[]',isset($p_data) && isset($p_data[0]->title_ar) ? $p_data[0]->title_ar: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Title Ar (2)')}}</label>
                                {!! Form::text('p_title_ar[]',isset($p_data) && isset($p_data[1]->title_ar) ? $p_data[1]->title_ar: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Title Ar (3)')}}</label>
                                {!! Form::text('p_title_ar[]',isset($p_data) && isset($p_data[2]->title_ar) ? $p_data[2]->title_ar: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Paragraph Content Ar (1)')}}</label>
                                {!! Form::textarea('p_content_ar[]',isset($p_data) && isset($p_data[0]->content_ar) ? $p_data[0]->content_ar: null,['class'=>'form-control','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Content Ar (2)')}}</label>
                                {!! Form::textarea('p_content_ar[]',isset($p_data) && isset($p_data[1]->content_ar) ? $p_data[1]->content_ar: null,['class'=>'form-control','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Content Ar (3)')}}</label>
                                {!! Form::textarea('p_content_ar[]',isset($p_data) && isset($p_data[2]->content_ar) ?$p_data[2]->content_ar: null,['class'=>'form-control','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Paragraph Title En (1)')}}</label>
                                {!! Form::text('p_title_en[]',isset($p_data) && isset($p_data[0]->title_en) ? $p_data[0]->title_en: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Title En (2)')}}</label>
                                {!! Form::text('p_title_en[]',isset($p_data) && isset($p_data[1]->title_en) ? $p_data[1]->title_en: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Title En (3)')}}</label>
                                {!! Form::text('p_title_en[]',isset($p_data) && isset($p_data[2]->title_en) ? $p_data[2]->title_en: null,['class'=>'form-control','id'=>'title_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Paragraph Content En (1)')}}</label>
                                {!! Form::textarea('p_content_en[]',isset($p_data) && isset($p_data[0]->content_en) ?$p_data[0]->content_en: null,['class'=>'form-control','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Content En (2)')}}</label>
                                {!! Form::textarea('p_content_en[]',isset($p_data) && isset($p_data[1]->content_en) ?$p_data[1]->content_en: null,['class'=>'form-control','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                            <div class="col-md-4">
                                <label>{{__('Paragraph Content En (3)')}}</label>
                                {!! Form::textarea('p_content_en[]',isset($p_data) && isset($p_data[2]->content_en) ?$p_data[2]->content_en: null,['class'=>'form-control','id'=>'content_ar-form-input','autocomplete'=>'off']) !!}
                            </div>
                        </div>
                         @endif

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
            <script src="{{asset('assets/uploader/jquery.fileuploader.min.js')}}" type="text/javascript"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
            <script type="text/javascript">

                function submitMainForm(){
                    formSubmit(
                        '{{isset($result) ? route('system.page.update',$result->id):route('system.page.store')}}',
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
                            url: '{{route('system.page.image-upload')}}',
                            data: {
                                '_token': '{{csrf_token()}}',
                                'key':  '{{$randKey}}',
                                'page_id': '{{ isset($result) ? $result->id : NULL }}'
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

                            $.post('{{route('system.page.remove-image')}}', {
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
            <link href="{{asset('assets/uploader/font/font-fileuploader.css')}}" rel="stylesheet">
            <link href="{{asset('assets/uploader/jquery.fileuploader.min.css')}}" media="all" rel="stylesheet">
        @endsection