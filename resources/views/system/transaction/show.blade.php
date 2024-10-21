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
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">

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

                                        @if($result->invoice)
                                        <tr>
                                            <td>{{__('Invoice ID')}}</td>
                                            <td><a target="_blank" href="{{route('system.invoice.show',$result->invoice_id)}}" target="_blank">{{'# '.$result->invoice_id}}</a></td>
                                        </tr>
                                        @endif

                                        @if($result->service)
                                            <tr>
                                                <td>{{__('Service')}}</td>
                                                <td><a target="_blank" href="{{route('system.service.show',$result->service_id)}}" target="_blank">{{$result->service->{'title_'.lang()} }}</a></td>
                                            </tr>
                                        @endif

                                        @if($result->payment_method)
                                            <tr>
                                                <td>{{__('Payment Method')}}</td>
                                                <td><a target="_blank" href="{{route('system.payment-methods.show',$result->payment_method_id)}}" target="_blank">{{$result->payment_method->name}}</a></td>
                                            </tr>
                                        @endif

                                        @if($result->amount)
                                            <tr>
                                                <td>{{__('Amount Value')}}</td>
                                                <td>{{ amount($result->amount,true)}}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            <td>
                                                @if($result->status == 'pending')
                                                <span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__(ucwords($result->status))}}</span>
                                                @elseif($result->status == 'paid')
                                                <span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__(ucwords($result->status))}}</span>
                                                @else
                                                <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__(ucwords($result->status))}}</span>
                                                @endif

                                            </td>
                                        </tr>

                                        @if($result->notes)
                                        <tr>
                                            <td>{{__('Notes')}}</td>
                                            <td>{!! $result->notes !!}</td>
                                        </tr>
                                        @endif

                                        @if($result->request)
                                            <tr>
                                                <td>{{__('Request')}}</td>
                                                <td>{!! $result->request !!}</td>
                                            </tr>
                                        @endif

                                        @if($result->response)
                                            <tr>
                                                <td>{{__('Response')}}</td>
                                                <td>{!! $result->response !!}</td>
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