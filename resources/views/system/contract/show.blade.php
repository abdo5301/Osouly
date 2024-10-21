@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
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
            <div class="k-content__head-toolbar">
                {{--<div class="k-content__head-wrapper" style="margin-left:10px;">--}}
                    {{--<a href="{{route('system.contract.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Contract Data')}}" data-placement="left">--}}
                        {{--<i class="la la-edit"></i>--}}
                    {{--</a>--}}
                {{--</div>--}}
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
                        <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">

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
                                        @if($result->property)
                                        <tr>
                                            <td>{{__('Property ID')}}</td>
                                            <td><a href="{{route('system.property.show',$result->property_id)}}" target="_blank">{{$result->property_id}}</a></td>
                                        </tr>
                                        @endif

                                        @if($result->renter)
                                            <tr>
                                                <td>{{__('Renter')}}</td>
                                                <td><a href="{{route('system.renter.show',$result->renter_id)}}" target="_blank">{{$result->renter->fullname}}</a></td>
                                            </tr>
                                        @endif

                                        @if($result->price)
                                            <tr>
                                                <td>{{__('Price')}}</td>
                                                <td>{{ amount($result->price,true)}}</td>
                                            </tr>
                                        @endif

                                        @if($result->insurance_price)
                                            <tr>
                                                <td>{{__('Insurance Price')}}</td>
                                                <td>{{ amount($result->insurance_price,true)}}</td>
                                            </tr>
                                        @endif
                                        @if($result->deposit_rent)
                                            <tr>
                                                <td>{{__('Deposit Rent')}}</td>
                                                <td>{{ amount($result->deposit_rent,true)}}</td>
                                            </tr>
                                        @endif
                                        @if($result->date_from)
                                            <tr>
                                                <td>{{__('Date From')}}</td>
                                                <td>{{ date('d-m-Y',strtotime($result->date_from))}}</td>
                                            </tr>
                                        @endif

                                        @if($result->date_to)
                                            <tr>
                                                <td>{{__('Date To')}}</td>
                                                <td>{{ date('d-m-Y',strtotime($result->date_to))}}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Contract Type')}}</td>
                                            @if($result->contract_type == 'month')
                                                <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Month')}}</span></td>
                                            @elseif($result->contract_type == 'year')
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__('Year')}}</span></td>
                                            @else
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('Day')}}</span></td>
                                            @endif
                                        </tr>

                                        @if($result->pay_from)
                                            <tr>
                                                <td>{{__('Pay From')}}</td>
                                                <td>{{ date('d-m-Y',strtotime($result->pay_from))}}</td>
                                            </tr>
                                        @endif

                                        @if($result->pay_to)
                                            <tr>
                                                <td>{{__('Pay To')}}</td>
                                                <td>{{ date('d-m-Y',strtotime($result->pay_to))}}</td>
                                            </tr>
                                        @endif

                                        @if($result->pay_every)
                                            <tr>
                                                <td>{{__('Pay Every')}}</td>
                                                <td>{{ $result->pay_every .' '.__('month') }}</td>
                                            </tr>
                                        @endif

                                        @if($result->limit_to_pay)
                                            <tr>
                                                <td>{{__('Limit To Pay')}}</td>
                                                <td>{{ $result->limit_to_pay .' ('.__('month').')' }}</td>
                                            </tr>
                                        @endif

                                        @if($result->pay_at)
                                            <tr>
                                                <td>{{__('Pay At')}}</td>
                                                @if($result->pay_at == 'start')
                                                    <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{ __(ucwords($result->pay_at)) }}</span></td>
                                                @else
                                                    <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{ __(ucwords($result->pay_at)) }}</span></td>
                                                @endif
                                            </tr>
                                        @endif


                                        @if($result->calendar)
                                            <tr>
                                                <td>{{__('Calendar')}}</td>
                                                @if($result->calendar == 'm')
                                                    <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{ __('Gregorian') }}</span></td>
                                                @else
                                                    <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{ __('Hijri') }}</span></td>
                                                @endif
                                            </tr>
                                        @endif

                                        @if($result->increase_value)
                                            <tr>
                                                <td>{{__('Increase Value')}}</td>
                                                <td>{{ amount($result->increase_value,true)}}</td>
                                            </tr>
                                        @endif

                                        @if($result->increase_percentage)
                                            <tr>
                                                <td>{{__('Increase Percentage')}}</td>
                                                <td>{{ $result->increase_percentage .' %' }}</td>
                                            </tr>
                                        @endif
                                        @if($result->increase_from)
                                            <tr>
                                                <td>{{__('Increase From')}}</td>
                                                <td>{{ date('d-m-Y',strtotime($result->increase_from))}}</td>
                                            </tr>
                                        @endif


                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            @if($result->status == 'active')
                                                <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span></td>
                                            @elseif($result->status == 'pendding')
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__('Pending')}}</span></td>
                                            @else
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('Canceled')}}</span></td>
                                            @endif
                                        </tr>

                                        @if($result->canceled_by_client_id)
                                            <tr>
                                                <td>{{__('Canceled Owner ID')}}</td>
                                                <td><a href="{{route('system.owner.show',$result->canceled_by_client_id)}}" target="_blank">{{$result->canceled_by_client_id}}</a></td>
                                            </tr>
                                        @endif

                                        @if($result->canceled_reason)
                                            <tr>
                                                <td>{{__('Canceled Reason')}}</td>
                                                <td>{!! $result->canceled_reason !!}</td>
                                            </tr>
                                        @endif

                                        {{--@if(!empty($contract_content))--}}
                                        {{--<tr>--}}
                                            {{--<td>{{__('Contract Content')}}</td>--}}
                                            {{--<td>{!!  $contract_content !!}</td>--}}
                                        {{--</tr>--}}
                                        {{--@endif--}}

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
                        <div class="col-lg-8 col-xl-8 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Contract Content')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <p>
                                        @if(!empty($contract_content))
                                                {!!  $contract_content !!}
                                        @endif
                                    </p>
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