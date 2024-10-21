@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />

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
            @if($result->client && empty($result->ticket_id))
            <div class="k-content__head-toolbar">
                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="javascript:void(0);" id="to-ticket-btn" class="btn btn-sm btn-info btn-brand">
                        {{ __('Convert To TS Ticket') }} <i class="k-menu__link-icon flaticon2-talk" aria-hidden="true"></i>
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
                        <div class=" @if(empty($result->replay))col-lg-6 col-xl-6 @else col-lg-12 col-xl-12 @endif order-lg-1 order-xl-1">

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

                                        @if($result->name)
                                            <tr>
                                                <td>{{__('Name')}}</td>
                                                <td> {{$result->name}} </td>
                                            </tr>
                                        @endif

                                        @if($result->mobile)
                                            <tr>
                                                <td>{{__('Mobile')}}</td>
                                                <td> {{$result->mobile}} </td>
                                            </tr>
                                        @endif

                                        @if($result->email)
                                            <tr>
                                                <td>{{__('Email')}}</td>
                                                <td> {{$result->email}} </td>
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

                                        @if($result->ticket)
                                            <tr>
                                                <td ><b style="background-color: #4ce2b5;padding: 3px;">{{__('Ticket ID')}}</b></td>
                                                <td> <a style="background-color: #a8fe8c2e;padding: 3px;" href="{{route('system.ticket.show',$result->id)}}"> {{'#'. $result->ticket_id}}</a> </td>
                                            </tr>
                                        @endif

                                        @if($result->subject)
                                            <tr>
                                                <td>{{__('Subject')}}</td>
                                                <td> {{$result->subject}} </td>
                                            </tr>
                                        @endif

                                        @if($result->message)
                                            <tr>
                                                <td>{{__('Message')}}</td>
                                                <td>  {!! $result->message !!} </td>
                                            </tr>
                                        @endif

                                        @if($result->replay)
                                            <tr>
                                                <td>{{__('Last Replay')}}</td>
                                                <td> {!! $result->replay !!} </td>
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
                        @if(empty($result->replay))
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
                                    {!! Form::open(['route' =>  ['system.contact.update',$result->id],'files'=>true, 'method' => 'PATCH','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                                    <div class="k-portlet__body" style="background: #FFF;">
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                {{--<label>{{__('Replay')}}</label>--}}
                                                {!! Form::textarea('replay',isset($result) ? $result->replay: null,['class'=>'form-control text_editor','id'=>'replay-form-input','autocomplete'=>'off']) !!}
                                                <div class="invalid-feedback" id="replay-form-error"></div>
                                            </div>
                                        </div>
                                        <div class="k-portlet__foot">
                                            <div class="k-form__actions">
                                                <div class="row" style="float: right;">
                                                    <button type="submit" class="btn btn-primary">{{__('Send Replay')}}</button>
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
                </div>



            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
@section('footer')
<script type="text/javascript">
    function submitMainForm(){
        var form = $('#main-form')[0];
        var formData = new FormData(form);
        formSubmit(
            '{{route('system.contact.update',$result->id)}}',
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

    $(document).ready(function () {

        $('body').on('click', '#to-ticket-btn', function (e) {
            e.preventDefault();
            $('#form-alert-message').html('');

            $.ajax({
                url:'{{route('system.contact.to-ticket',$result->id)}}',
                type: 'post',
                data: {'_token': '{!! csrf_token() !!}'},
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
                        $('#to-ticket-btn').remove();
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','success',json['message']);
                        setTimeout(function(){location.reload();},1000);
                     } else {
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',json['message']);
                    }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });

    });

</script>
@endsection