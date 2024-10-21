@extends('system.layout')
@section('header')
    <style>
        td:first-child {
            font-weight: bold
        }
    </style>
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
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-12 col-xl-12  order-lg-1 order-xl-1">

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
                                                <td>{{$result->title}}</td>
                                            </tr>
                                        @endif
                                            <tr>
                                                <td>{{__('Sent')}}</td>
                                                <td>{{$result->sent}} {{ ' '.__('mail')}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Status')}}</td>
                                                @if($result->status == 'done')
                                                    <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__(ucfirst($result->status))}}</span></td>
                                                @elseif($result->status == 'progress')
                                                    <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__(ucfirst($result->status))}}</span></td>
                                                @else
                                                    <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__(ucfirst($result->status))}}</span></td>
                                               @endif
                                            </tr>
                                        @if($result->content)
                                        <tr>
                                            <td>{{__('Content')}}</td>
                                            <td>{!! $result->content !!}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>{{__('Created At')}}</td>
                                            <td>
                                                {{$result->created_at->format('Y-m-d h:i A')}} ({{$result->created_at->diffForHumans()}})
                                            </td>
                                        </tr>

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
                    </div>
                    <!--end::Row-->
                </div>



            </div>
        </div>
    </div>

    <!-- end:: Content -->
@endsection
