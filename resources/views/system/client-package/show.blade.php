@extends('system.layout')
@section('header')
    @if(lang() == 'ar')
        <link href="{{asset('assets/custom/user/profile-v1.rtl.css')}}" rel="stylesheet" type="text/css" />
    @else
        <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />

    <style>
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
            {{--<div class="k-content__head-toolbar">--}}
                {{--<div class="k-content__head-wrapper" style="margin-left:10px;">--}}
                    {{--<a href="{{route('system.client-package.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Client Package')}}" data-placement="left">--}}
                        {{--<i class="la la-edit"></i>--}}
                    {{--</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">

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
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>

                                        @if($result->client)
                                            <tr>
                                                <td>{{__('Client')}}</td>
                                                <td><a target="_blank" href="{{route('system.'.$result->client->type.'.show',$result->client_id)}}" target="_blank">{{$result->client->fullname .' ( ' .__(ucwords($result->client->type)).' ) '}}</a></td>
                                            </tr>
                                        @endif

                                        @if($result->service)
                                            @php
                                                $rout_name = 'service';
                                                   if($result->service->parent_id){
                                                   $rout_name = 'package';
                                                   }
                                            @endphp
                                        <tr>
                                            <td>{{__('Service / Package')}}</td>
                                            <td><a target="_blank" href="{{route('system.'.$rout_name.'.show',$result->service_id)}}" target="_blank">{{ $result->service->{'title_'.lang()} }}<br>{{ ' ( '.__(ucwords($rout_name)).' )'  }}</a></td>
                                        </tr>
                                        @endif

                                        @if($result->transaction_id)
                                            <tr>
                                                <td>{{__('Transaction ID')}}</td>
                                                <td><a target="_blank" href="{{route('system.transaction.show',$result->transaction_id)}}" target="_blank">{{'# '.$result->transaction_id}}</a></td>
                                            </tr>
                                        @endif

                                        @if($result->service_count)
                                            <tr>
                                                <td>{{__('Service Count')}}</td>
                                                <td> {{$result->service_count}} </td>
                                            </tr>
                                            <tr>
                                                <td>{{__('Rest Count')}}</td>
                                                <td> {{$result->rest_count}} </td>
                                            </tr>
                                        @endif

                                        @if($result->service_type)
                                            <tr>
                                                <td>{{__('Service Type')}}</td>
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">
                                                    @if($result->service_type == 'manage')
                                                            {{__('management')}}
                                                        @elseif($result->service_type == 'star')
                                                            {{__('Special Properties')}}
                                                        @else
                                                            {{__('Ads')}}
                                                        @endif
                                                    </span></td>
                                            </tr>
                                        @endif

                                        @if($result->count_per_day)
                                            <tr>
                                                <td>{{__('Count Per Day')}}</td>
                                                <td>{{$result->count_per_day}}</td>
                                            </tr>
                                        @endif


                                        @if($result->date_from)
                                            <tr>
                                                <td>{{__('Date From')}}</td>
                                                <td>
                                                    {{ date('Y-m-d',strtotime($result->date_from))}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->date_to)
                                            <tr>
                                                <td>{{__('Date To')}}</td>
                                                <td>
                                                    {{ date('Y-m-d',strtotime($result->date_to))}}
                                                </td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            @if($result->status == 'active')
                                                <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__(ucwords($result->status))}}</span></td>
                                            @elseif($result->status == 'pendding')
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__(ucwords($result->status))}}</span></td>
                                            @else
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{ __(ucwords(str_replace('-',' ',$result->status)))}}</span></td>
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

                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('(Service / Package) Details')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table">
                                        <tbody>
                                        @if(!empty($service_details))

                                            <tr>
                                                <td>{{__('ID')}}</td>
                                                @if($service_details->parent_id)
                                                <td> <a href="{{route('system.package.show',$service_details->id)}}" target="_blank"> # {{$service_details->id}} </a> </td>
                                                @else
                                                <td> <a href="{{route('system.service.show',$service_details->id)}}" target="_blank"> # {{$service_details->id}} </a> </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <td>{{__('Name')}}</td>
                                                <td>{{$service_details->{'title_'.lang()} }}</td>
                                            </tr>

                                            {{--<tr>--}}
                                                {{--<td>{{__('Content')}}</td>--}}
                                                {{--<td>{!! $service_details->{'content_'.lang()} !!}</td>--}}
                                            {{--</tr>--}}

                                            @if(getService($service_details->parent_id))
                                                <tr>
                                                    <td>{{__('Service')}}</td>
                                                    <td><a target="_blank" href="{{route('system.service.show',$service_details->parent_id)}}">{{getService($service_details->parent_id)->{'title_'.lang()} }}</a></td>
                                                </tr>
                                            @endif
                                            @if($service_details->type)
                                                <tr>
                                                    <td>{{__('Type')}}</td>
                                                    <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">
                                                    @if($service_details->type == 'manage')
                                                                {{__('management')}}
                                                            @elseif($service_details->type == 'star')
                                                                {{__('Special Properties')}}
                                                            @else
                                                                {{__('Ads')}}
                                                            @endif
                                                    </span></td>
                                                </tr>
                                            @endif


                                            <tr>
                                                <td>{{__('Type Count')}}</td>
                                                <td>{{ $service_details->type_count }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('Properties Count')}}</td>
                                                <td>{{ $service_details->properties_count }}</td>
                                            </tr>


                                            @if($service_details->price)
                                                <tr>
                                                    <td>{{__('Price')}}</td>
                                                    <td>{{amount($service_details->price)}}</td>
                                                </tr>
                                            @endif
                                            @if($service_details->offer)
                                                <tr>
                                                    <td>{{__('Offer')}}</td>
                                                    <td>{{amount($service_details->offer)}}</td>
                                                </tr>
                                            @endif
                                            @if($service_details->duration)
                                                <tr>
                                                    <td>{{__('Duration') .' '. __('Per Day')}}</td>
                                                    <td>{{$service_details->duration}}</td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td>{{__('Status')}}</td>
                                                @if($service_details->status == 'active')
                                                    <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span></td>
                                                @else
                                                    <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('In-Active')}}</span></td>
                                                @endif
                                            </tr>

                                            @if($service_details->discount_type)
                                                <tr>
                                                    <td>{{__('Discount Type')}}</td>
                                                    <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__(ucwords($service_details->discount_type))}}</span></td>
                                                </tr>
                                            @endif


                                            <tr>
                                                <td>{{__('Discount Value')}}</td>
                                                @if($service_details->discount_value)
                                                    <td>
                                                        @if($service_details->discount_type == 'fixed')
                                                            {{ amount($service_details->discount_value,true) }}
                                                        @else
                                                            {{  $service_details->discount_value.' %' }}
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        {{__('0.00')}}
                                                    </td>
                                                @endif
                                            </tr>

                                            @if($service_details->discount_from)
                                                <tr>
                                                    <td>{{__('Discount From')}}</td>
                                                    <td>
                                                        {{ date('Y-m-d',strtotime($service_details->discount_from))}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($service_details->discount_to)
                                                <tr>
                                                    <td>{{__('Discount To')}}</td>
                                                    <td>
                                                        {{ date('Y-m-d',strtotime($service_details->discount_to))}}
                                                    </td>
                                                </tr>
                                            @endif

                                            @if($service_details->discount_code)
                                                <tr>
                                                    <td>{{__('Discount Code')}}</td>
                                                    <td>{{ $service_details->discount_code }}</td>
                                                </tr>
                                            @endif

                                            @if($service_details->discount_code_value && $service_details->discount_code)
                                                <tr>
                                                    <td>{{__('Discount Code Value')}}</td>
                                                    <td>{{ amount($result->discount_code_value,true) }}</td>
                                                </tr>
                                            @endif

                                            @if($service_details->percentage)
                                                <tr>
                                                    <td>{{__('Percentage')}}</td>
                                                    <td>{{ $service_details->percentage.' %' }}</td>
                                                </tr>
                                            @endif


                                            <tr>
                                                <td>{{__('Subscribers Count')}}</td>
                                                <td>{{ $service_details->subscribers_count  }}</td>
                                            </tr>

                                            {{--<tr>--}}
                                                {{--<td>{{__('UnSubscribers Count')}}</td>--}}
                                                {{--<td>{{ $service_details->unsubscribers_count  }}</td>--}}
                                            {{--</tr>--}}

                                            <tr>
                                                <td>{{__('Subscribe Monthly Value')}}</td>
                                                <td>{{ amount($service_details->subscribe_monthly,true)  }}</td>
                                            </tr>

                                            @if($service_details->subscribe_from)
                                                <tr>
                                                    <td>{{__('Subscribe From')}}</td>
                                                    <td>
                                                        {{ date('Y-m-d',strtotime($service_details->subscribe_from))}}
                                                    </td>
                                                </tr>
                                            @endif

                                            @if($service_details->subscribe_to)
                                                <tr>
                                                    <td>{{__('Subscribe To')}}</td>
                                                    <td>
                                                        {{ date('Y-m-d',strtotime($service_details->subscribe_to))}}
                                                    </td>
                                                </tr>
                                            @endif

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
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.js"></script>

@endsection