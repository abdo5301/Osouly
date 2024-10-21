@extends('system.layout')
@section('header')
    @if(lang() == 'ar')
        <link href="{{asset('assets/custom/user/profile-v1.rtl.css')}}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />
    <style>
        .number-high-light{
            padding: 8px;
            color: #000000;
            background-color: #ebedf2;
            letter-spacing: 3px;
        }
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
                <a href="{{route('system.'.$type.'.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Client Data')}}" data-placement="left">
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
                                <div class="k-profile__main-info-name">
                                        {{$result->fullname}}

                                    @if($result->status == 'active')
                                        <span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span>
                                    @elseif($result->status == 'pending')
                                        <span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__('Pending')}}</span>
                                    @else
                                        <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('In-Active')}}</span>
                                    @endif

                                </div>
                                <div class="k-profile__main-info-position">
                                    {{__(ucfirst($result->type))}}
                                </div>
                                @if($result->created_at)
                                <div style="margin-bottom: 0.1rem;padding-top:10px;" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-time-2"></i></span>
                                    <span class="k-profile__contact-item-text">{{__('Created At')}}: {{$result->created_at->format('Y-m-d h:i A')}} ( {{$result->created_at->diffForHumans()}} )</span>
                                </div>
                                @endif
                                @if($result->updated_at)
                                <div style="margin-bottom: 0.1rem;" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-time-1"></i></span>
                                    <span class="k-profile__contact-item-text">{{__('Last Update')}}: {{$result->updated_at->format('Y-m-d h:i A')}} ( {{$result->updated_at->diffForHumans()}} )</span>
                                </div>
                                @endif
                                @if($result->created_notes)
                                <div style="margin-bottom: 0.1rem;" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-user-settings"></i></span>
                                    <span class="k-profile__contact-item-text">{{__('Create Notes')}}: {{$result->created_notes}}</span>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-4 col-xl-4">
                        <div class="k-profile__contact">
                            @if($result->mobile)
                                <a onclick="save_log_phone()" style="margin-bottom: 0.1rem;" href="tel:{{$result->mobile}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->mobile}}</span>
                                </a>
                            @endif
                            @if($result->phone)
                                <a onclick="save_log_phone()" style="margin-bottom: 0.1rem;" href="tel:{{$result->phone}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->phone}}</span>
                                </a>
                            @endif
                            @if($result->email)
                                <a onclick="save_log_mail()" style="margin-bottom: 0.1rem;" href="mailto:{{$result->email}}" class="k-profile__contact-item">
                                    <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                    <span class="k-profile__contact-item-text">{{$result->email}}</span>
                                </a>
                            @endif

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
                        <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_111" role="tab">
                            {{__('Client Data')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_8" role="tab">
                            {{__('Invoices')}}
                            @php
                                $invoicesCount = $result->invoices()->count();
                            @endphp
                            @if($invoicesCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$invoicesCount}}</span>
                            @endif
                        </a>
                    </li>

                    @if($result->type != 'renter')
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_1" role="tab">
                            {{__('Properties')}}
                            @php
                                $propertyCount = $result->property()->count();
                            @endphp
                            @if($propertyCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$propertyCount}}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                    @if($result->type != 'owner')
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_2" role="tab">
                            {{__('Requests')}}
                            @php
                                $requestsCount = $result->renterRequests()->count();
                            @endphp
                            @if($requestsCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$requestsCount}}</span>
                            @endif
                        </a>
                    </li>
                    @endif
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
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_9" role="tab">
                            {{__('Favorite')}}
                            @php
                                $favoriteCount = $result->favorite()->count();
                            @endphp
                            @if($favoriteCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$favoriteCount}}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_11" role="tab">
                            {{__('Transactions')}}
                            @php
                                $c_transactionsCount = $result->clientTransactions()->count();
                            @endphp
                            @if($c_transactionsCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$c_transactionsCount}}</span>
                            @endif
                        </a>
                    </li>

                    @if($result->type !='renter')
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_12" role="tab">
                            {{__('Owner Installments')}}
                            @php
                                $installmentsCount = $result->owner_installments()->count();
                            @endphp
                            @if($installmentsCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$installmentsCount}}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                   @if($result->type !='owner')
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_13" role="tab">
                            {{__('Renter Installments')}}
                            @php
                                $installmentsCount = $result->renter_installments()->count();
                            @endphp
                            @if($installmentsCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$installmentsCount}}</span>
                            @endif
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#k_tabs_1_14" role="tab">
                            {{__('Client Packages')}}
                            @php
                                $c_packagesCount = $result->packages()->count();
                            @endphp
                            @if($c_packagesCount)
                                <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$c_packagesCount}}</span>
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

            <div class="tab-pane fade show active" id="k_tabs_1_111" role="tabpanel">
                <!--begin::Row-->
                <div class="row">
                    <div class="col-lg-7 col-xl-7 order-lg-1 order-xl-1">

                        <!--begin::Portlet-->
                        <div class="k-portlet k-portlet--height-fluid">
                            <div class="k-portlet__head">
                                <div class="k-portlet__head-label">
                                    <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                </div>
                            </div>
                            <div class="k-portlet__body">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td>{{__('ID')}}</td>
                                        <td>{{$result->id}}</td>
                                    </tr>
                                    @if($result->id_number)
                                    <tr>
                                        <td>{{__('ID Number')}}</td>
                                        <td><span class="number-high-light">{{$result->id_number}}</span></td>
                                    </tr>
                                    @endif
                                    @if($result->parent_id && getClientById($result->parent_id))
                                        <tr>
                                            <td>{{__('Client Parent')}}</td>
                                            <td>
                                                {!! getClientById($result->parent_id) !!}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($result->permissions)
                                        <tr>
                                            <td>{{__('Client Permissions')}}</td>
                                            <td>
                                                {{ $result->permissions }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{__('Name')}}</td>
                                        <td> {{$result->fullname}}</td>
                                    </tr>
                                    @if($result->phone)
                                    <tr>
                                        <td>{{__('Phone')}}</td>
                                        <td> {{$result->phone}}</td>
                                    </tr>
                                    @endif
                                    @if($result->mobile)
                                    <tr>
                                        <td>{{__('Mobile')}}</td>
                                        <td> {{$result->mobile}}</td>
                                    </tr>
                                    @endif
                                    @if($result->email)
                                    <tr>
                                        <td>{{__('Email')}}</td>
                                        <td> {{$result->email}}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>{{__('Client Type')}}</td>
                                        <td> {{__(ucfirst($result->type))}}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('Gender')}}</td>
                                        <td> {{__(ucfirst($result->gender))}}</td>
                                    </tr>
                                    @if($result->area_id)
                                    <tr>
                                        <td>{{__('Area')}}</td>
                                        <td>{{implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true))}}</td>
                                    </tr>
                                    @endif
                                    @if($result->description)
                                    <tr>
                                        <td>{{__('Address')}}</td>
                                        <td> {{$result->address}}</td>
                                    </tr>
                                    @endif
                                    @if($result->description)
                                    <tr>
                                        <td>{{__('Description')}}</td>
                                        <td> {!! $result->description !!} </td>
                                    </tr>
                                    @endif

                                    @if($result->created_by_staff_id && $result->created_by)
                                        <tr>
                                            <td>{{__('Created By')}}</td>
                                            <td>
                                              <a href="{{route('system.staff.show',$result->created_by_staff_id)}}" target="_blank" > {{ $result->created_by->fullname }} </a>
                                            </td>
                                        </tr>
                                    @endif

                                    @if($result->credit)
                                        <tr>
                                            <td>{{__('Credit')}}</td>
                                            <td>
                                                {{ amount($result->credit,true) }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($result->birth_date)
                                        <tr>
                                            <td>{{__('Birth Date')}}</td>
                                            <td>
                                                {{ date('Y-m-d',strtotime($result->birth_date))}}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($result->bank_code)
                                        <tr>
                                            <td>{{__('Bank Code')}}</td>
                                            <td>
                                               {{ $result->bank_code }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($result->branch_code)
                                        <tr>
                                            <td>{{__('Branch Code')}}</td>
                                            <td>
                                                {{ $result->branch_code }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($result->bank_account_number)
                                        <tr>
                                            <td>{{__('Bank Account Number')}}</td>
                                            <td>
                                                <span class="number-high-light">{{ $result->bank_account_number }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{__('Status')}}</td>
                                        @if($result->status == 'active')
                                        <td> <span class="k-badge  k-badge--success k-badge--inline k-badge--pill"> {{__('Active')}} </span> </td>
                                        @elseif($result->status == 'pending')
                                        <td> <span class="k-badge  k-badge--info k-badge--inline k-badge--pill"> {{__('Pending')}} </span> </td>
                                        @else
                                        <td> <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">  {{__('In-Active')}} </span> </td>
                                        @endif
                                    </tr>
                                    @if($result->verified_at)
                                        <tr>
                                            <td>{{__('Verified At')}}</td>
                                            <td>
                                                {{ date('Y-m-d h:i A',strtotime($result->verified_at))}}
                                                {{--{{$result->verified_at->format('Y-m-d h:i A')}} ({{$result->verified_at->diffForHumans()}})--}}
                                            </td>
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

                        <div class="col-lg-5 col-xl-5 order-lg-1 order-xl-1">
                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Images')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    @if($result->images->isNotEmpty())
                                    <ul id="image-view" class="pictures">
                                        @foreach($result->images as $key => $value)
                                            <li><img src="{{asset($value->path)}}" alt="{{__(ucwords(str_replace('_',' ',$value->image_name)))}}" title="{{__(ucwords(str_replace('_',' ',$value->image_name)))}}"></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                            </div>
                            <!--end::Portlet-->
                        </div>

                </div>
                <!--end::Row-->
            </div>


            <div class="tab-pane fade" id="k_tabs_1_1" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Properties')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-property">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Property Title')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Purpose')}}</th>
                                <th>{{__('Price')}}</th>
                                <th>{{__('space')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Property Title')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Purpose')}}</th>
                                <th>{{__('Price')}}</th>
                                <th>{{__('space')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


            <div class="tab-pane fade" id="k_tabs_1_2" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Requests')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-request">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Property ID')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Property ID')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="k_tabs_1_3" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">
                                {{__('Calls')}}
                                <a href="{{route('system.call.index',['client_id'=> $result->id])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand" title="{{__('Create Call')}}" data-placement="left">
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Create Call')}}</span>
                                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>
                                </a>
                            </h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-call">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Action')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Created At')}}</th>
                                {{--<th>{{__('Action')}}</th>--}}
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Action')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Created At')}}</th>
                                {{--<th>{{__('Action')}}</th>--}}
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
                            <a href="{{route('system.calendar.index',['sign_type'=>'client','sign_id'=>$result->id])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand">
                                <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Add')}}</span>
                                <i class="flaticon2-plus-1 k-padding-l-5 k-padding-r-0"></i>
                            </a>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('By')}}</th>
                                <th>{{__('Date & Time')}}</th>
                                <th>{{__('Comment')}}</th>
                                <th>{{__('Created At')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($result->reminders as $key => $value)

                                <tr>
                                    <td>{{$value->id}}</td>
                                    <td>
                                        <a href="{{route('system.staff.show',$value->staff->id)}}" target="_blank">
                                            {{$value->staff->fullname}}
                                        </a>
                                    </td>
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
            <div class="tab-pane fade " id="k_tabs_1_5" role="tabpanel">
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
                                <th>{{__('Staff')}}</th>
                                <th>{{__('Created At')}}</th>
{{--                                <th>{{__('Action')}}</th>--}}
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Staff')}}</th>
                                <th>{{__('Created At')}}</th>
{{--                                <th>{{__('Action')}}</th>--}}
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

                <div class="tab-pane fade" id="k_tabs_1_8" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Invoices')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table  class="table table-striped- table-bordered table-hover table-checkable" id="datatable-invoice">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Property ID')}}</th>
                                    <th>{{__('Due Name')}}</th>
                                    <th>{{__('Installment ID')}}</th>
                                    <th>{{__('Amount Value')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Property ID')}}</th>
                                    <th>{{__('Due Name')}}</th>
                                    <th>{{__('Installment ID')}}</th>
                                    <th>{{__('Amount Value')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>



            <div class="tab-pane fade" id="k_tabs_1_9" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Favorite')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-favorite">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Property ID')}}</th>
                                <th>{{__('Created At')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Property ID')}}</th>
                                <th>{{__('Created At')}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="k_tabs_1_11" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Transactions')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-transactions">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Transaction ID')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Transaction ID')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Type')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="k_tabs_1_12" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Owner Installments')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-owner-installments">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Amount Value')}}</th>
                                <th>{{__('Renter')}}</th>
                                <th>{{__('Invoice ID')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Created At')}}</th>
                                {{--<th>{{__('Action')}}</th>--}}
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Amount Value')}}</th>
                                <th>{{__('Renter')}}</th>
                                <th>{{__('Invoice ID')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Created At')}}</th>
{{--                                <th>{{__('Action')}}</th>--}}
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="k_tabs_1_13" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Renter Installments')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-renter-installments">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Amount Value')}}</th>
                                <th>{{__('Owner')}}</th>
                                <th>{{__('Invoice ID')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Created At')}}</th>
                                {{--<th>{{__('Action')}}</th>--}}
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Amount Value')}}</th>
                                <th>{{__('Owner')}}</th>
                                <th>{{__('Invoice ID')}}</th>
                                <th>{{__('Due Date')}}</th>
                                <th>{{__('Created At')}}</th>
                                {{--                                <th>{{__('Action')}}</th>--}}
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>

            <div class="tab-pane fade" id="k_tabs_1_14" role="tabpanel">
                <div class="k-portlet k-portlet--height-fluid">
                    <div class="k-portlet__head">
                        <div class="k-portlet__head-label">
                            <h3 class="k-portlet__head-title">{{__('Requests')}}</h3>
                        </div>
                    </div>
                    <div class="k-portlet__body">
                        <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-packages">
                            <thead>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Service ID')}}</th>
                                <th>{{__('Transaction ID')}}</th>
                                <th>{{__('Date From')}}</th>
                                <th>{{__('Date To')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>{{__('ID')}}</th>
                                <th>{{__('Service ID')}}</th>
                                <th>{{__('Transaction ID')}}</th>
                                <th>{{__('Date From')}}</th>
                                <th>{{__('Date To')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Created At')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.js"></script>

    <script type="text/javascript">

        window.addEventListener('DOMContentLoaded', function () {
            var galley = document.getElementById('image-view');
            var viewer = new Viewer(galley, {
                url: 'data-original',
                title: function (image) {
                    return image.alt + ' (' + (this.index + 1) + '/' + this.length + ')';
                },
            });
        });

            $datatableRequest = $('#datatable-request').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "request";
                    }
                }
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
            $datatableCall = $('#datatable-favorite').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "favorite";
                    }
                }
            });
            $datatableCall = $('#datatable-renter-installments').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "renter_installments";
                    }
                }
            });
            $datatableCall = $('#datatable-owner-installments').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "owner_installments";
                    }
                }
            });
            $datatableCall = $('#datatable-transactions').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "client_transactions";
                    }
                }
            });
            $datatableProperty = $('#datatable-property').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "property";
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

            $datatableLog = $('#datatable-invoice').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "invoice";
                    }
                }
            });

            $datatableLog = $('#datatable-packages').DataTable({
                "iDisplayLength": 25,
                processing: true,
                serverSide: true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{url()->full()}}",
                    "type": "GET",
                    "data": function(data){
                        data.isDataTable = "client_packages";
                    }
                }
            });



            function save_log_phone(){
                $.get(
                    '{{route('system.misc.ajax')}}',
                    {
                        'type':'saveLog',
                        'id': {{$result->id}},
                        'desc': 'Call on a number',
                        'model': "App\\Models\\Client"
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
                        'model': "App\\Models\\Client"
                    },
                    function(){
                        // location.reload();
                        $url = '{{url()->full()}}?isDataTable=log';
                        $datatableLog.ajax.url($url).load();

                    }
                );
            }
        </script>
@endsection