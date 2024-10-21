@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />
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

    <style>
        .pictures {
            list-style: none;
            margin: 0;
            /* max-width: 30rem;*/
            padding: 0;
        }

        .pictures > li {
            border: 1px solid transparent;
            float: left;
            height: 250px;
            margin: 0 -1px -1px 0;
            overflow: hidden;
            width: calc(100% / 3);
        }

        .pictures > li > img {
            cursor: zoom-in;
            width: 100%;
            height: 100%;
        }


        td:first-child {
            font-weight: bold
        }
    </style>
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css">
@endsection
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
            @if($result->status == 'solve' && staffCan('close-ticket'))
                <div class="k-content__head-toolbar">
                    <div class="k-content__head-wrapper" style="margin-left:10px;">
                        <a href="javascript:changeTicketStatus('close');" id="close-ticket-btn" class="btn btn-sm btn-success btn-brand">
                            {{ __('Close Ticket') }} <i class="k-menu__link-icon flaticon2-lock" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            @endif
            {{--@if($result->status == 'close')--}}
                {{--<div class="k-content__head-toolbar">--}}
                    {{--<div class="k-content__head-wrapper" style="margin-left:10px;">--}}
                        {{--<a href="javascript:void(0);" id="close-ticket-btn" class="btn btn-sm btn-success btn-brand">--}}
                            {{--{{ __('Open Ticket') }} <i class="k-menu__link-icon flaticon-alarm" aria-hidden="true"></i>--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--@endif--}}
            @if($result->status != 'close' && $result->status != 'solve')
                <div class="k-content__head-toolbar">
                    <div class="k-content__head-wrapper" style="margin-left:10px;">
                        <a href="javascript:changeTicketStatus('solve');"  id="solve-ticket-btn" class="btn btn-sm btn-success btn-brand">
                            {{ __('Mark Ticket As Solved') }} <i class="k-menu__link-icon flaticon2-checkmark-outline-symbol" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div id="form-alert-message"></div>

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class=" @if($result->status !== 'close' && $result->status !== 'solve')col-lg-6 col-xl-6 @else col-lg-12 col-xl-12 @endif order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>

                                        @if($result->title)
                                            <tr>
                                                <td>{{__('Title')}}</td>
                                                <td> {{$result->title}} </td>
                                            </tr>
                                        @endif

                                        @if($result->client)
                                            <tr>
                                                <td>{{__('Client')}}</td>
                                                <td> <a target="_blank"  href="{{route('system.'.$result->client->type.'.show',$result->client->id)}}">{{$result->client->fullname}}<br>
                                                        ( {{__(ucfirst($result->client->type))}} )
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            @if($result->status == 'new')
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__(ucfirst($result->status))}}</span></td>
                                            @elseif($result->status == 'pending_client')
                                                <td><span class="k-badge  k-badge--warning k-badge--inline k-badge--pill">{{__(ucfirst(str_replace('_',' ',$result->status)))}}</span></td>
                                            @elseif($result->status == 'pending_support')
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__(ucfirst(str_replace('_',' ',$result->status)))}}</span></td>
                                            @elseif($result->status == 'solve')
                                                <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__(ucfirst($result->status))}}</span></td>
                                            @else
                                                <td><span class="k-badge  k-badge--dark k-badge--inline k-badge--pill">{{__(ucfirst($result->status))}}</span></td>
                                            @endif
                                        </tr>

                                        @if($result->created_at)
                                            <tr>
                                                <td>{{__('Created At')}}</td>
                                                <td>
                                                    {{$result->created_at->format('Y-m-d h:i A')}} ({{$result->created_at->diffForHumans()}})
                                                </td>
                                            </tr>
                                        @endif
                                        @if($result->updated_at)
                                            <tr>
                                                <td>{{__('Last Update')}}</td>
                                                <td>
                                                    {{$result->updated_at->format('Y-m-d h:i A')}} ({{$result->updated_at->diffForHumans()}})
                                                </td>
                                            </tr>
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        @if($result->status !== 'close' && $result->status !== 'solve')
                            <div class="col-lg-6 col-xl-6  order-lg-1 order-xl-1">

                                <!--begin::Portlet-->
                                <div class="k-portlet k-portlet--height-fluid">
                                    <div class="k-portlet__head">
                                        <div class="k-portlet__head-label">
                                            <h3 class="k-portlet__head-title">{{__('Message Replay')}}</h3>
                                        </div>
                                    </div>
                                    <div class="k-portlet__body" style="background: #00a7520d;">
                                        {{--<div id="form-alert-message"></div>--}}
                                        {!! Form::open(['route' =>  ['system.ticket.update',$result->id],'files'=>true, 'method' => 'PATCH','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                                        <div class="k-portlet__body" style="background: #FFF;">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    {!! Form::textarea('comment',null,['class'=>'form-control ticket_text_editor','id'=>'comment-form-input','autocomplete'=>'off']) !!}
                                                    <div class="invalid-feedback" id="comment-form-error"></div>
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
                                                        <button type="submit" class="btn btn-primary">{{__('Add Comment')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                </div>

                                <!--end::Portlet-->
                            </div>
                        @endif
                    </div>
                    <!--end::Row-->
                    @if(!empty($result->comments))
                    <div class="row">
                        <div class="  col-lg-12 col-xl-12 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Comments')}}</h3>
                                    </div>
                                </div>
                                @foreach($result->comments as $comment)
                                <div class="k-portlet__body" @if(!$comment->staff) style="background: #00a7520d;" @else style="background: #2a88bd26;" @endif>
                                    <div class="k-portlet__body" style="background: #FFF;margin-top: 10px">
                                    <table class="table">
                                        <tbody>
                                        @if($comment->client)
                                            <tr>
                                                <td>@if(!$comment->staff) {{__('From Client')}}  @else {{__('To Client')}} @endif</td>
                                                <td> <a target="_blank"  href="{{route('system.'.$comment->client->type.'.show',$comment->client->id)}}">{{$comment->client->fullname}}<br>
                                                        ( {{__(ucfirst($comment->client->type))}} )
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif

                                        @if($comment->staff)
                                            <tr>
                                                <td>{{__('Staff')}}</td>
                                                <td> <a target="_blank"  href="{{route('system.staff.show',$comment->staff->id)}}">{{$comment->staff->fullname}}<br>

                                                    </a>
                                                </td>
                                            </tr>
                                        @endif

                                        @if(!empty($comment->comment))
                                            <tr>
                                                <td>{{__('Comment')}}</td>
                                                <td> {!! $comment->comment !!} </td>
                                            </tr>
                                        @endif

                                        @if(!empty($comment->image) && is_file($comment->image))
                                            <tr>
                                                <td>{{__('Image')}}</td>
                                                <td> <a target="_blank" href="{{asset($comment->image)}}"><img src="{{asset($comment->image)}}" width="100" height="100"></a></td>
                                            </tr>
                                        @endif

                                        @if($result->created_at)
                                            <tr>
                                                <td>{{__('Created At')}}</td>
                                                <td>
                                                    {{$result->created_at->format('Y-m-d h:i A')}} ({{$result->created_at->diffForHumans()}})
                                                </td>
                                            </tr>
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                                </div>
                                @endforeach
                            </div>
                            <!--end::Portlet-->
                        </div>

                    </div>
                    @endif

                </div>



            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
@section('footer')
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
                '{{route('system.ticket.update',$result->id)}}',
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

        function changeTicketStatus(ticket_status){
            $.ajax({
                url:'{{route('system.ticket.change-status',$result->id)}}',
                type: 'post',
                data: {'_token': '{!! csrf_token() !!}','status': ticket_status },
                dataType: 'json',
                beforeSend: function() {
                    addLoading();
                },
                complete: function() {
                    removeLoading();
                },
                success: function(json) {
                    console.log(json);
                    if (json['code'] === 200) {
                        location.reload();
                    } else {
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',json['message']);
                    }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
            return false;
        }


        $('.ticket_text_editor').summernote({
            height:200,
        });
    </script>
@endsection
