@extends('system.layout')
@section('header')
    @if(lang() == 'ar')
        <link href="{{asset('assets/custom/user/profile-v1.rtl.css')}}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    @endif
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
        <div class="k-portlet k-profile">
            <div class="k-profile__content">
                <div class="row">
                    <div class="col-md-12 col-lg-5 col-xl-4">
                        <div class="k-profile__main">
                            {{--<div class="k-profile__main-pic">
                                <img src="{{asset('assets/media/users/300_21.jpg')}}" alt="" />
                                <label class="k-profile__main-pic-upload">
                                    <i class="flaticon-photo-camera"></i>
                                </label>
                            </div>--}}
                            <div class="k-profile__main-info">
                                <div class="k-profile__main-info-name">
                                    @if($result->gender == 'male')
                                        <i class="fa fa-male"></i>
                                    @else
                                        <i class="fa fa-female"></i>
                                    @endif
                                    {{$result->fullname}}

                                    @if($result->status == 'active')
                                        <span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span>
                                    @else
                                        <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('In-Active')}}</span>
                                    @endif
                                </div>
                                <div class="k-profile__main-info-position">{{$result->job_title}}</div>
                                <div class="k-profile__main-info-position">({{$result->permission_group->name}})</div>
                                <div class="k-profile__main-info-position">{{__('NID')}}: {{$result->national_id}}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-4 col-xl-4">
                        <div class="k-profile__contact">
                            @if(staffCan('show-all-phones') || $result->id == Auth::id())
                            <a href="tel:{{$result->mobile}}" class="k-profile__contact-item">
                                <span class="k-profile__contact-item-icon"><i class="flaticon-support k-font-danger"></i></span>
                                <span class="k-profile__contact-item-text">{{$result->mobile}}</span>
                            </a>
                            @endif
                            <a href="#" class="k-profile__contact-item">
                                <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                <span class="k-profile__contact-item-text">{{$result->email}}</span>
                            </a>
                            <a href="#" class="k-profile__contact-item">
                                <span class="k-profile__contact-item-icon"><i class="fa fa-calendar-alt k-font-danger"></i></span>
                                <span class="k-profile__contact-item-text">{{$result->birthdate}}</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-12 col-lg-3 col-xl-4">
                        <div class="k-profile__stats">
                            @if($result->address)
                            <div class="k-profile__stats-item">
                                <div class="k-profile__stats-item-label">{{__('Address')}}</div>
                                <div class="k-profile__stats-item-chart">
                                    {{$result->address}}
                                </div>
                            </div>
                            @endif
                            @if($result->description)
                            <div class="k-profile__stats-item">
                                <div class="k-profile__stats-item-label">{{__('Description')}}</div>
                                <div class="k-profile__stats-item-chart">
                                    {{$result->description}}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="k-profile__nav">
                <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_3" role="tab">
                            {{__('Calls')}}
                            @php
                                $callsCount = $result->calls()->count();
                            @endphp
                            @if($callsCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$callsCount}}</span>
                            @endif
                        </a>
                    </li>
                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link" data-toggle="tab" href="#k_tabs_1_4" role="tab">--}}
                            {{--{{__('Reminders')}}--}}

                            {{--@php--}}
                                {{--$remindersCount = $result->reminders()->count();--}}
                            {{--@endphp--}}
                            {{--@if($remindersCount)--}}
                                {{--<span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$remindersCount}}</span>--}}
                            {{--@endif--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link" data-toggle="tab" href="#k_tabs_1_5" role="tab">--}}
                            {{--{{__('Log')}}--}}
                        {{--</a>--}}
                    {{--</li>--}}
                </ul>
            </div>
        </div>

        <!--end::Portlet-->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="k_tabs_1_3" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Calls')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-call">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Client')}}</th>
                                <th>{{__('Action')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Created By')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Client')}}</th>
                                <th>{{__('Action')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Created By')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="k_tabs_1_4" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Reminders')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Date & Time')}}</th>
                                <th>{{__('Comment')}}</th>
                                <th>{{__('Created At')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result->reminders as $key => $value)
                                <tr>
                                    <td>{{$value->id}}</td>
                                    <td>{{$value->date_time->format('Y-m-d h:i A')}}</td>
                                    <td>{{$value->comment}}</td>
                                    <td>{{$value->created_at->format('Y-m-d h:i A')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="k_tabs_1_5" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Log')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-log">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Modal')}}</th>
                                <th>{{__('Created At')}}</th>
{{--                                <th>{{__('Action')}}</th>--}}
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Modal')}}</th>
                                <th>{{__('Created At')}}</th>
{{--                                <th>{{__('Action')}}</th>--}}
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
<!-- end:: Content -->
@endsection
@section('footer')
        <script type="text/javascript">
            $datatableCall = $('#datatable-call').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "call";
                    }
                }
            });

            $datatableLog = $('#datatable-log').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "log";
                    }
                }
            });
        </script>
@endsection