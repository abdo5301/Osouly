@extends('system.layout')
@section('header')
    @if(lang() == 'ar')
        <link href="{{asset('assets/custom/user/profile-v1.rtl.css')}}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    @endif
    <style>
        td:first-child {
            font-weight: bold
        }
        .dt-button-save{
            color : #1dc9b7 !important;
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
            <div class="k-content__head-toolbar">
                {{--<div class="k-content__head-wrapper">--}}
                    {{--<a onclick="save_log_share()" href="whatsapp://send?text={{urlencode(implode("\n",requestToText($result)))}}" data-action="share/whatsapp/share" class="btn btn-sm btn-success btn-brand" data-toggle="k-tooltip" title="{{__('Share on WhatsApp')}}" data-placement="left">--}}
                        {{--<i style="padding-left:0px !important;" class="flaticon-whatsapp k-padding-l-5 k-padding-r-0"></i>--}}
                    {{--</a>--}}
                {{--</div>--}}
                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="{{route('system.request.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Request')}}" data-placement="left">
                        <i class="la la-edit"></i>
                    </a>
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

                                <div class="k-profile__main-info">

                                    <div class="k-profile__main-info-position">
                                        {{ __("Renter") }}
                                    </div>
                                    <div class="k-profile__main-info-name">
                                        <a target="_blank" href="{{route('system.client.show',$result->renter->id)}}">{{$result->renter->Fullname}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-4">
                            <div class="k-profile__contact">
                                @if($result->renter->mobile)
                                    <a onclick="save_log_phone()" style="margin-bottom: 0.1rem;" href="tel:{{$result->renter->mobile}}" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                        <span class="k-profile__contact-item-text">{{$result->renter->mobile}}</span>
                                    </a>
                                @endif
                                @if($result->renter->phone)
                                    <a  onclick="save_log_phone()"  style="margin-bottom: 0.1rem;" href="tel:{{$result->renter->phone}}" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                        <span class="k-profile__contact-item-text">{{$result->renter->phone}}</span>
                                    </a>
                                @endif
                                @if($result->renter->email)
                                    <a  onclick="save_log_mail()"  style="margin-bottom: 0.1rem;" href="mailto:{{$result->renter->email}}" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                        <span class="k-profile__contact-item-text">{{$result->renter->email}}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="k-profile__nav">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_1" role="tab">{{__('Request')}}</a>
                        </li>

                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" data-toggle="tab" href="#k_tabs_1_2" role="tab">--}}
                                {{--{{__('Properties')}}--}}
                                {{--@php--}}
                                    {{--$propertyCount = $result->property()->count();--}}
                                {{--@endphp--}}
                                {{--@if($propertyCount)--}}
                                    {{--<span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$propertyCount}}</span>--}}
                                {{--@endif--}}
                            {{--</a>--}}
                        {{--</li>--}}
                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" data-toggle="tab" href="#k_tabs_1_10" role="tab">--}}
                                {{--{{__('Imported Data')}}--}}
                                {{--@if($importerCount)--}}
                                    {{--<span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$importerCount}}</span>--}}
                                {{--@endif--}}
                            {{--</a>--}}
                        {{--</li>--}}

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_3" role="tab">
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
                                    {{--<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{$remindersCount}}</span>--}}
                                {{--@endif--}}
                            {{--</a>--}}
                        {{--</li>--}}

                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" data-toggle="tab" href="#k_tabs_1_5" role="tab">--}}
                                {{--{{__('Share With Client')}}--}}
                                {{--@if($result->sharing_until && new DateTime($result->sharing_until->format('Y-m-d H:i:s')) > new DateTime(date('Y-m-d H:i:s')))--}}
                                    {{--<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span>--}}
                                {{--@endif--}}
                            {{--</a>--}}
                        {{--</li>--}}

                        {{--<li class="nav-item">--}}
                            {{--<a class="nav-link" data-toggle="tab" href="#k_tabs_1_6" role="tab">--}}
                                {{--{{__('Log')}}--}}
                            {{--</a>--}}
                        {{--</li>--}}

                    </ul>
                </div>
            </div>

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-6 col-xl-6  order-lg-1 order-xl-1">

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
                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            <td> {{ __(ucfirst($result->status)) }} </td>
                                        </tr>
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
                        @if($result->property)
                        <div class="col-lg-6 col-xl-6  order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">
                                            {{__('Requested Property')}}
                                        </h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <div class="tab-content">
                                        <table class="table table-striped">
                                            <tbody>
                                            <tr>
                                                <td>{{__('ID')}}</td>
                                                <td><a href="{{route('system.property.show',$result->property->id)}}" target="_blank">{{$result->property->id}}</a></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Owner')}}</td>
                                                <td><a href="{{route('system.client.show',$result->property->owner->id)}}" target="_blank">{{$result->property->owner->Fullname}}</a></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Price')}}</td>
                                                <td>{{number_format($result->property->price)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Space')}}</td>
                                                <td>{{number_format($result->property->space)}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Address')}}</td>
                                                <td>{{$result->property->address}}</td>
                                            </tr>

                                            @if($result->property->description)
                                                <tr>
                                                    <td>{{__('Description')}}</td>
                                                    <td><a href="{{route('system.property.show',$result->property->id)}}" target="_blank">{{$result->property->description}}</a></td>
                                                </tr>
                                            @endif

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                            @endif
                    </div>

                    <!--end::Row-->
                </div>



                <div class="tab-pane fade" id="k_tabs_1_3" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Calls')}}</h3>
                                <a href="{{route('system.call.index',['client_id'=> $result->client_id,'sign_id'=>$result->id,'sign_type'=>'request'])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand" title="{{__('Create Call')}}" data-placement="left">
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Create Call')}}</span>
                                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>
                                </a>
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



                    <!--end::Row-->
                </div>
                {{--<div class="tab-pane fade" id="k_tabs_1_4" role="tabpanel">--}}
                    {{--<div class="k-portlet k-portlet--height-fluid">--}}
                        {{--<div class="k-portlet__head">--}}
                            {{--<div class="k-portlet__head-label">--}}
                                {{--<h3 class="k-portlet__head-title">{{__('Reminders')}}</h3>--}}
                                {{--<a href="{{route('system.calendar.index',['sign_type'=>'request','sign_id'=>$result->id])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand">--}}
                                    {{--<span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Add')}}</span>--}}
                                    {{--<i class="flaticon2-plus-1 k-padding-l-5 k-padding-r-0"></i>--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="k-portlet__body">--}}
                            {{--<table class="table table-striped">--}}
                                {{--<thead>--}}
                                    {{--<tr>--}}
                                        {{--<th>{{__('ID')}}</th>--}}
                                        {{--<th>{{__('By')}}</th>--}}
                                        {{--<th>{{__('Date & Time')}}</th>--}}
                                        {{--<th>{{__('Comment')}}</th>--}}
                                        {{--<th>{{__('Created At')}}</th>--}}
                                    {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tbody>--}}
                                    {{--@foreach($result->reminders as $key => $value)--}}
                                    {{--<tr>--}}
                                        {{--<td>{{$value->id}}</td>--}}
                                        {{--<td>--}}
                                            {{--<a href="{{route('system.staff.show',$value->staff->id)}}" target="_blank">--}}
                                                {{--{{$value->staff->fullname}}--}}
                                            {{--</a>--}}
                                        {{--</td>--}}
                                        {{--<td>{{$value->date_time->format('Y-m-d h:i A')}}</td>--}}
                                        {{--<td>{{$value->comment}}</td>--}}
                                        {{--<td>{{$value->created_at->format('Y-m-d h:i A')}}</td>--}}
                                    {{--</tr>--}}
                                    {{--@endforeach--}}
                                {{--</tbody>--}}
                            {{--</table>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="tab-pane fade" id="k_tabs_1_5" role="tabpanel">--}}
                    {{--<div class="k-portlet k-portlet--height-fluid">--}}
                        {{--<div class="k-portlet__head">--}}
                            {{--<div class="k-portlet__head-label">--}}
                                {{--<h3 class="k-portlet__head-title">{{__('Share With Client')}}</h3>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="k-portlet__body">--}}
                            {{--<table class="table table-striped">--}}
                                {{--<thead>--}}
                                {{--<tr>--}}
                                    {{--<th>{{__('Key')}}</th>--}}
                                    {{--<th>{{__('Value')}}</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tbody>--}}
                                    {{--<tr>--}}
                                        {{--<td>{{__('Status')}}</td>--}}
                                        {{--<td>--}}
                                            {{--@if($result->sharing_until && new DateTime($result->sharing_until->format('Y-m-d H:i:s')) > new DateTime(date('Y-m-d H:i:s')))--}}
                                                {{--<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span>--}}
                                            {{--@else--}}
                                                {{--<span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('In-Active')}}</span>--}}
                                            {{--@endif--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                    {{--@if($result->sharing_properties_ids)--}}
                                    {{--<tr>--}}
                                        {{--<td>{{__('Selected Properties IDs')}}</td>--}}
                                        {{--<td>--}}
                                           {{--( {{$result->sharing_properties_ids}} )--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>{{__('Sharing Properties Count')}}</td>--}}
                                        {{--<td>--}}
                                            {{--( {{count(explode(',',$result->sharing_properties_ids))}} )--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                    {{--@endif--}}
                                    {{--@if($result->sharing_until && new DateTime($result->sharing_until->format('Y-m-d H:i:s')) > new DateTime(date('Y-m-d H:i:s')))--}}
                                        {{--<tr>--}}
                                            {{--<td>{{__('Expiration date')}}</td>--}}
                                            {{--<td>--}}
                                                {{--{{$result->sharing_until}}--}}
                                                {{--<button type="button" class="btn btn-sm btn-danger" onclick="closeSharing();">{{__('Close')}}</button>--}}

                                            {{--</td>--}}
                                        {{--</tr>--}}
                                        {{--<tr>--}}
                                            {{--<td>{{__('Views')}}</td>--}}
                                            {{--<td>--}}
                                                {{--{{$result->sharing_views}}--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}

                                        {{--<tr>--}}
                                            {{--<td>{{__('By')}}</td>--}}
                                            {{--<td><a target="_blank" href="{{route('system.staff.show',$result->share_staff->id)}}">{{$result->share_staff->fullname}}</a></td>--}}
                                        {{--</tr>--}}
                                        {{--<tr>--}}
                                            {{--<td>{{__('URL')}} <a href="javascript:copyToClipboard('{{route('web.request.view',$result->sharing_slug)}}')" onclick="$('#share-url-input').select().css('background','cornflowerblue');">( {{__('Copy')}} )</a></td>--}}
                                            {{--<td>--}}
                                                {{--<input type="text" id="share-url-input" class="form-control" value="{{route('web.request.view',$result->sharing_slug)}}" onclick="$(this).select()">--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
                                    {{--@endif--}}
                                    {{--<tr>--}}
                                        {{--<td colspan="2" style="text-align: center;">--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(1);">{{__('1 Hours')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(2);">{{__('2 Hours')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(4);">{{__('4 Hours')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(6);">{{__('6 Hours')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(12);">{{__('12 Hours')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(24);">{{__('1 Day')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(24);">{{__('2 Days')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(168);">{{__('1 Week')}}</button>--}}
                                            {{--<button type="button" class="btn btn-primary" onclick="activeSharing(720);">{{__('1 Month')}}</button>--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                {{--</tbody>--}}
                            {{--</table>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}


                {{--<div class="tab-pane fade" id="k_tabs_1_6" role="tabpanel">--}}
                    {{--<div class="k-portlet k-portlet--height-fluid">--}}
                        {{--<div class="k-portlet__head">--}}
                            {{--<div class="k-portlet__head-label">--}}
                                {{--<h3 class="k-portlet__head-title">{{__('Log')}}</h3>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="k-portlet__body">--}}
                            {{--<table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-log">--}}
                                {{--<thead>--}}
                                {{--<tr>--}}
                                    {{--<th>{{__('ID')}}</th>--}}
                                    {{--<th>{{__('Status')}}</th>--}}
                                    {{--<th>{{__('Created At')}}</th>--}}
                                    {{--<th>{{__('Action')}}</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tfoot>--}}
                                {{--<tr>--}}
                                    {{--<th>{{__('ID')}}</th>--}}
                                    {{--<th>{{__('Status')}}</th>--}}
                                    {{--<th>{{__('Created At')}}</th>--}}
                                    {{--<th>{{__('Action')}}</th>--}}
                                {{--</tr>--}}
                                {{--</tfoot>--}}
                            {{--</table>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}



            </div>
        </div>
        <!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
            <script type="text/javascript">
                
                $datatable = $('#datatable-main').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "columnDefs": [ {
                        "targets": 0,
                        "orderable" : false,
                        "className": 'select-checkbox'
                    },
                        {  "orderable" : false, "targets": 1 },
                        {  "orderable" : false, "targets": 2 },
                        {  "orderable" : false, "targets": 3 },
                        {  "orderable" : false, "targets": 4 },
                        {  "orderable" : false, "targets": 5 },
                        {  "orderable" : false, "targets": 6 },
                        {  "orderable" : false, "targets": 7 },
                        {  "orderable" : false, "targets": 8 },
                        {  "orderable" : false, "targets": 9 },
                        {  "orderable" : false, "targets": 10 },

                    ],
                    "select": {
                        "style":   'multi', //'os',
                        "selector": 'td:first-child'
                    },

                    {{--],--}}
                    "order": [[ 1, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "true";
                        }
                    }


                });


                var sharing_prop_ids = []; //global var

                $datatable.on( 'select', function ( e, dt, type, indexes ) {
                    var selectedData = $datatable.rows( {selected:true} ).data();
                    sharing_prop_ids = [];
                    for( var i = 0; i < selectedData.length; i++){
                        var prop_id = selectedData[i][1];
                        if ( sharing_prop_ids.indexOf(prop_id) != -1) {
                            continue;
                        }
                        sharing_prop_ids.push(prop_id);

                    }
                       //console.log(sharing_prop_ids);


                }).on( 'deselect', function ( e, dt, type, indexes ) {

                        var selectedData = $datatable.rows( {selected:true} ).data();
                        sharing_prop_ids = [];
                        for( var i = 0; i < selectedData.length; i++){
                            var prop_id = selectedData[i][1];
                            if ( sharing_prop_ids.indexOf(prop_id) != -1) {
                                continue;
                            }
                            sharing_prop_ids.push(prop_id);

                        }
                          //console.log(sharing_prop_ids);
                    });



                $datatableImporter = $('#datatable-importer').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "columnDefs": [{
                        "targets": 0,
                        "orderable" : false,
                        "className": 'select-checkbox'
                    }],
                    "select": {
                        "style":   'multi', //'os',
                        "selector": 'td:first-child'
                    },
                    "order": [[ 1, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "importer";
                        }
                    }
                });

                var sharing_import_ids = []; //global var

                $datatableImporter.on( 'select', function ( e, dt, type, indexes ) {
                    var selectedData = $datatableImporter.rows( {selected:true} ).data();
                    sharing_import_ids = [];
                    for( var i = 0; i < selectedData.length; i++){
                        var prop_id = selectedData[i][1];
                        if ( sharing_import_ids.indexOf(prop_id) != -1) {
                            continue;
                        }
                        sharing_import_ids.push(prop_id);

                    }
                    //console.log(sharing_import_ids);


                }).on( 'deselect', function ( e, dt, type, indexes ) {

                    var selectedData = $datatableImporter.rows( {selected:true} ).data();
                    sharing_import_ids = [];
                    for( var i = 0; i < selectedData.length; i++){
                        var prop_id = selectedData[i][1];
                        if ( sharing_import_ids.indexOf(prop_id) != -1) {
                            continue;
                        }
                        sharing_import_ids.push(prop_id);

                    }
                    //console.log(sharing_import_ids);
                });


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


                function share_importer_whats_app() {
                    if(empty(sharing_import_ids)){
                        alert('{{__('Please select some importer data to share !')}}');
                        return false;
                    }else{
                        if(!confirm('{{__('Are you sure, you will share these selected related importer data on WhatsApp')}}')){
                            return false;
                        }
                    }
                    addLoading();
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'sharingWhatsAppImporterRequest',
                            'sharingWhatsAppImporter': sharing_import_ids
                        },
                        function($data){
                            sharing_import_ids = [];
                            $url = '{{url()->full()}}?isDataTable=importer';
                            $datatableImporter.ajax.url($url).load();
                            if ($(".select-all-import.all-import-checked")[0]){
                                $(".select-all-import").click();
                                //$(".select-all-prop").removeClass("all-prop-checked");
                            }
                            location.href = 'whatsapp://send?text='+$data;
                            removeLoading();
                        }
                    );
                }
                
                
                function share_property_whats_app() {
                    if(empty(sharing_prop_ids)){
                        alert('{{__('Please select some properties to share !')}}');
                        return false;
                    }else{
                        if(!confirm('{{__('Are you sure, you will share these selected related properties on WhatsApp')}}')){
                            return false;
                        }
                    }
                    addLoading();
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'sharingWhatsAppPropertiesRequest',
                            'sharingWhatsAppProperties': sharing_prop_ids
                        },
                        function($data){
                            sharing_prop_ids = [];
                            //$(".select-all-prop").click();
                            $url = '{{url()->full()}}?isDataTable=true';
                            $datatable.ajax.url($url).load();
                            if ($(".select-all-prop.all-prop-checked")[0]){
                                $(".select-all-prop").click();
                                //$(".select-all-prop").removeClass("all-prop-checked");
                            }
                            location.href = 'whatsapp://send?text='+$data;
                            removeLoading();

                            //alert('Done');

                        }
                    );

                }

                function saveSharingProp(){
                    if(empty(sharing_prop_ids)){
                        if(!confirm('{{__('Are you sure, you will share all related properties')}}')){
                            return false;
                        }
                    }else{
                        if(!confirm('{{__('Are you sure, you will share these selected related properties')}}')){
                            return false;
                        }
                    }

                    addLoading();
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'sharingPropertiesRequest',
                            'id': {{$result->id}},
                            'sharingProperties': sharing_prop_ids
                        },
                        function(){
                            $url = '{{url()->full()}}?isDataTable=true';
                            removeLoading();
                            location.reload();
                            // $datatable.ajax.url($url).load();
                            //alert('Done');

                        }
                    );
                }

                function activeSharing($hours){
                    if(!confirm('{{__('Are you sure, you will share the link for XX hours')}}'.replace('XX',$hours))){
                        return false;
                    }
                    addLoading();
                    $.post(
                        '{{route('system.request.share')}}',
                        {
                            'id': {{$result->id}},
                            'hours': $hours,
                            '_token': '{{csrf_token()}}'
                        },
                        function($data){
                            removeLoading();
                            location.reload();
                        }
                    );

                }

                function closeSharing(){
                    if(!confirm('{{__('Are you sure, you want to close sharing')}}')){
                        return false;
                    }
                    addLoading();
                    $.post(
                        '{{route('system.request.close-share')}}',
                        {
                            'id': {{$result->id}},
                            '_token': '{{csrf_token()}}'
                        },
                        function($data){
                            removeLoading();
                            location.reload();
                        }
                    );

                }

                function save_log_share() {
                    // addLoading();
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'saveLog',
                            'id': {{$result->id}},
                            'desc': 'Share On WhatsApp',
                            'model': "App\\Models\\Request"
                        },
                        function(){
                           // location.reload();
                            $url = '{{url()->full()}}?isDataTable=log';
                            $datatableLog.ajax.url($url).load();

                        }
                    );
                }


                function save_log_phone(){
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'saveLog',
                            'id': {{$result->id}},
                            'desc': 'Call on a number',
                            'model': "App\\Models\\Request"
                        },
                        function(){
                            // location.reload();
                            $url = '{{url()->full()}}?isDataTable=log';
                            $datatableLog.ajax.url($url).load();

                        }
                    );
                }


                function save_log_mail(){
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'saveLog',
                            'id': {{$result->id}},
                            'desc': 'send E-mail',
                            'model': "App\\Models\\Request"
                        },
                        function(){
                            // location.reload();
                            $url = '{{url()->full()}}?isDataTable=log';
                            $datatableLog.ajax.url($url).load();

                        }
                    );
                }

function select_prop(){
    //alert('dsd');
        $(".select-all-prop").toggleClass('all-prop-checked');
    if ($(".select-all-prop.all-prop-checked")[0]){
        $datatable.rows().select();
    } else {
        $datatable.rows().deselect();
    }

    }

    function select_import(){
    //alert('dsd');
        $(".select-all-import").toggleClass('all-import-checked');
    if ($(".select-all-import.all-import-checked")[0]){
        $datatableImporter.rows().select();
    } else {
        $datatableImporter.rows().deselect();
    }

    }









            </script>
@endsection