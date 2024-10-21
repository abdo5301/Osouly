@extends('system.layout')
@section('header')
    @if(lang() == 'ar')
    <link href="{{asset('assets/custom/user/profile-v1.rtl.css')}}" rel="stylesheet" type="text/css" />
    @else
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    @endif
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

@endsection
@section('content')
    <!-- begin:: Content -->
    <div class="k-content	k-grid__item k-grid__item--fluid k-grid k-grid--hor" id="k_content">

        <!-- begin:: Content Head -->
        <div class="k-content__head	k-grid__item">
            <div class="k-content__head-main">
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
                    {{--<a onclick="save_log_share()" href="whatsapp://send?text={{urlencode(implode("\n",propertyToText($result)))}}" data-action="share/whatsapp/share" class="btn btn-sm btn-success btn-brand" data-toggle="k-tooltip" title="{{__('Share on WhatsApp')}}" data-placement="left">--}}
                        {{--<i style="padding-left:0px !important;" class="flaticon-whatsapp k-padding-l-5 k-padding-r-0"></i>--}}
                    {{--</a>--}}
                {{--</div>--}}

                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="{{route('system.property.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Property')}}" data-placement="left">
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
                                        {{ __("Owner") }}
                                    </div>
                                    <div class="k-profile__main-info-name">
                                        <a target="_blank" href="{{route('system.'.$result->owner->type.'.show',$result->owner->id)}}">{{$result->owner->Fullname}}</a>
                                    </div>
                                    <br>

                                    @if(specialPropertyCheck($result->id))
                                        <div style="margin-bottom: 0.1rem;" class="k-profile__contact-item">
                                        <a  target="_blank" style="margin-bottom: 0.1rem;" href="{{route('system.special-property.show',$result->id)}}" class="k-profile__contact-item">
                                            <span class="k-profile__contact-item-icon"><i class="fa fa-star fa-spin  k-font-warning"></i></span>
                                            <span class="k-profile__contact-item-text">{{__('Special Property')}}</span>
                                        </a>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-4 col-xl-4">
                            <div class="k-profile__contact">
                                @if($result->owner->mobile)
                                    <a onclick="save_log_phone()" style="margin-bottom: 0.1rem;" href="tel:{{$result->owner->mobile}}" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                        <span class="k-profile__contact-item-text">{{$result->owner->mobile}}</span>
                                    </a>
                                @endif
                                @if($result->owner->phone)
                                    <a  onclick="save_log_phone()"  style="margin-bottom: 0.1rem;" href="tel:{{$result->owner->phone}}" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                        <span class="k-profile__contact-item-text">{{$result->owner->phone}}</span>
                                    </a>
                                @endif
                                @if($result->owner->email)
                                    <a  onclick="save_log_mail()"  style="margin-bottom: 0.1rem;" href="mailto:{{$result->owner->email}}" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                        <span class="k-profile__contact-item-text">{{$result->owner->email}}</span>
                                    </a>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
                <div class="k-profile__nav">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_1" role="tab">{{__('Property')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_2" role="tab">
                                {{__('Requests')}}
                                @php
                                    $requestsCount = $result->requests()->count();
                                @endphp
                                @if($requestsCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$requestsCount}}</span>
                                @endif
                            </a>
                        </li>
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
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_4" role="tab">
                                {{__('Dues')}}
                                @php
                                    $duesCount = $result->dues()->count();
                                @endphp
                                @if($duesCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$duesCount}}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_5" role="tab">
                                {{__('Facilities')}}
                                @php
                                    $facilitiesCount = $result->facilities()->count();
                                @endphp
                                @if($facilitiesCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$facilitiesCount}}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_6" role="tab">
                                {{__('Invoices')}}
                                @php
                                    $invoicesCount = $result->invoices()->count();
                                @endphp
                                @if($invoicesCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$invoicesCount}}</span>
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
                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table table-striped">
                                      {{--  <thead>
                                        <tr>
                                            <th>{{__('Key')}}</th>
                                            <th>{{__('Value')}}</th>
                                        </tr>
                                        </thead>--}}
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>
                                        @if($result->property_type)
                                        <tr>
                                            <td>{{__('Type')}}</td>
                                            <td>{{$result->property_type->{'name_'.\App::getLocale()} }}</td>
                                        </tr>
                                        @endif
                                        @if($result->purpose)
                                        <tr>
                                            <td>{{__('Purpose')}}</td>
                                            <td>{{$result->purpose->{'name_'.\App::getLocale()} }}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            <td>{{ __(ucwords(str_replace('_',' ',$result->status))) }}</td>
                                        </tr>

                                        @if($result->area)
                                        <tr>
                                            <td>{{__('Area')}}</td>
                                            <td>{{implode(' -> ',\App\Libs\AreasData::getAreasUp($result->local_id,true))}}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Address')}}</td>
                                            <td>{{$result->address}}</td>
                                        </tr>
                                        @if($result->building_number)
                                            <tr>
                                                <td>{{__('Building Number')}}</td>
                                                <td>{{$result->building_number }}</td>
                                            </tr>
                                        @endif

                                        @if($result->flat_number)
                                            <tr>
                                                <td>{{__('Flat Number')}}</td>
                                                <td>{{$result->flat_number }}</td>
                                            </tr>
                                        @endif
                                        @if($result->floor)
                                            <tr>
                                                <td>{{__('Floor')}}</td>
                                                <td>{{__($result->floor)}}</td>
                                            </tr>
                                        @endif
                                        @if($result->description)
                                        <tr>
                                            <td>{{__('Description')}}</td>
                                            <td>{{$result->description}}</td>
                                        </tr>
                                        @endif
                                        @if($result->space)
                                        <tr>
                                            <td>{{__('Space')}}</td>
                                            <td>{{number_format($result->space)}}</td>
                                        </tr>
                                       @endif
                                        @if($result->space)
                                        <tr>
                                            <td>{{__('Price')}}</td>
                                            <td>{{ amount($result->price,true) }}</td>
                                        </tr>
                                        @endif
                                        @if($result->space)
                                        <tr>
                                            <td>{{__('Insurance Price')}}</td>
                                            <td>{{amount($result->insurance_price,true)}}</td>
                                        </tr>
                                        @endif
                                        @if($result->contract_period)
                                        <tr>
                                            <td>{{__('Contract Period')}}</td>
                                            <td>{{$result->contract_period}} {{__($result->contract_type)}}</td>
                                        </tr>
                                        @endif

                                        @if($result->deposit_rent)
                                        <tr>
                                            <td>{{__('Deposit Rent')}}</td>
                                            <td>{{amount($result->deposit_rent,true)}} </td>
                                        </tr>
                                        @endif

                                        @if($result->latitude && $result->longitude)
                                        <tr>
                                            <td>{{__('Location')}}</td>
                                            <td>{{$result->latitude}}, {{$result->longitude}}</td>
                                        </tr>
                                        @endif
                                        @if($result->video_url)
                                        <tr>
                                            <td>{{__('Video')}}</td>
                                            <td>
                                                <a target="_blank" href="{{$result->video_url}}">{{$result->video_url}}</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($result->data_source)
                                        <tr>
                                            <td>{{__('Data Source')}}</td>
                                            <td>{{$result->data_source->{'name_'.\App::getLocale()} }}</td>
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

                                        @if($result->importer_data_id && $result->importer)
                                            <tr>
                                                <td>{{__('Importer')}}</td>
                                                <td>
                                                    <a target="_blank" href="{{route('system.importer.show',$result->importer_data_id)}}">
                                                    {{__('#ID: :id',['id'=>$result->importer_data_id])}}
                                                        @if($result->importer->connector)
                                                        {{$result->importer->connector}}
                                                        @endif
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif


                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">
                                            {{__("Property Features")}}
                                        </h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <div class="tab-content">
                                        @if($result->features)
                                        @php
                                         $features_arr = explode(',',$result->features);
                                          foreach($features_arr as $k => $v){
                                             $feature_info =  getPropertyFeature_byId($v);
                                             if(!empty($feature_info)){
                                              $propertyFeaturesValue[$v] = $feature_info->name;
                                             }

                                           }
                                        @endphp
                                        @foreach($propertyFeaturesValue as $fKey =>$fValue)
                                                <span class="k-badge  k-badge--info k-badge--inline k-badge--pill"> {{$fValue}} </span>
                                        @endforeach
                                     @endif
                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        @if($result->images->isNotEmpty())
                        <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Images')}}</h3>
                                    </div>
                                </div>
{{--                                @php--}}
{{--                                dd($result->images);--}}
{{--                                @endphp--}}
                                <div class="k-portlet__body">
                                    <ul id="image-view" class="pictures">
                                        @foreach($result->images as $key => $value)
                                            <li><img src="{{asset($value->path)}}" alt="{{$value->image_name}}"></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        @endif
                    </div>

                    <!--end::Row-->
                </div>
                <div class="tab-pane fade" id="k_tabs_1_2" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Requests')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table  class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Renter')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Renter')}}</th>
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
                                <h3 class="k-portlet__head-title">{{__('Calls')}}</h3>
                                @if($result->owner)
                                <a style="margin: 0 10px;" href="{{route('system.call.index',['client_id'=> $result->owner_id,'sign_id'=>$result->id,'sign_type'=>'property'])}}" target="_blank" class="btn btn-sm btn-elevate btn-info" title="{{__('Owner Call')}}" data-placement="left">
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Owner Call')}}</span>
                                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>
                                </a>
                                @endif
                                @if($result->renter)
                                <a style="margin: 0 10px;" href="{{route('system.call.index',['client_id'=> $result->renter_id,'sign_id'=>$result->id,'sign_type'=>'property'])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand" title="{{__('Renter Call')}}" data-placement="left">
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Renter Call')}}</span>
                                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>
                                </a>
                                @endif
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
                                    {{--<th>{{__('Action')}}</th>--}}
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
                                    {{--<th>{{__('Action')}}</th>--}}
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>



                    <!--end::Row-->
                </div>

                <div class="tab-pane fade" id="k_tabs_1_4" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Dues')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table  class="table table-striped- table-bordered table-hover table-checkable" id="datatable-dues">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Due Name')}}</th>
                                    <th>{{__('Value')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Duration')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Due Name')}}</th>
                                    <th>{{__('Value')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Duration')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="k_tabs_1_5" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Facilities')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table  class="table table-striped- table-bordered table-hover table-checkable" id="datatable-facilities">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Facility Companies')}}</th>
                                    <th>{{__('Device Number')}}</th>
                                    <th>{{__('Created At')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Facility Companies')}}</th>
                                    <th>{{__('Device Number')}}</th>
                                    <th>{{__('Created At')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="k_tabs_1_6" role="tabpanel">
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
                                    <th>{{__('Due Name')}}</th>
                                    <th>{{__('Installment ID')}}</th>
                                    <th>{{__('Client')}}</th>
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
                                    <th>{{__('Due Name')}}</th>
                                    <th>{{__('Installment ID')}}</th>
                                    <th>{{__('Client')}}</th>
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

                {{--<div class="tab-pane fade" id="k_tabs_1_4" role="tabpanel">--}}
                    {{--<div class="k-portlet k-portlet--height-fluid">--}}
                        {{--<div class="k-portlet__head">--}}
                            {{--<div class="k-portlet__head-label">--}}
                                {{--<h3 class="k-portlet__head-title">{{__('Reminders')}}</h3>--}}

                                {{--<a href="{{route('system.calendar.index',['sign_type'=>'property','sign_id'=>$result->id])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand">--}}
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
                <div class="tab-pane fade" id="k_tabs_1_8" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Log')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table  class="table table-striped- table-bordered table-hover table-checkable" id="datatable-log">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
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
                $datatable = $('#datatable-main').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "true";
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

                $datatableCall = $('#datatable-invoice').DataTable({
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

                $datatableCall = $('#datatable-facilities').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "facilities";
                        }
                    }
                });

                $datatableCall = $('#datatable-dues').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "dues";
                        }
                    }
                });


                window.addEventListener('DOMContentLoaded', function () {
                    var galley = document.getElementById('image-view');
                    var viewer = new Viewer(galley, {
                        url: 'data-original',
                        title: function (image) {
                            return image.alt + ' (' + (this.index + 1) + '/' + this.length + ')';
                        },
                    });
                });


                function updateCallDate(){
                    addLoading();
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'updateCallDate',
                            'id': {{$result->id}}
                        },
                        function(){
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
                            'model': "App\\Models\\Property"
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
                            'model': "App\\Models\\Property"
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
                            'model': "App\\Models\\Property"
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