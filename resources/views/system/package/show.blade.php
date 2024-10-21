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
                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="{{route('system.package.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Package Date')}}" data-placement="left">
                        <i class="la la-edit"></i>
                    </a>
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
                        <div class="col-lg-4 col-xl-4  order-lg-1 order-xl-1">

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
                                        @if(getService($result->parent_id))
                                        <tr>
                                            <td>{{__('Service')}}</td>
                                            <td><a target="_blank" href="{{route('system.service.show',$result->parent_id)}}">{{getService($result->parent_id)->{'title_'.lang()} }}</a></td>
                                        </tr>
                                        @endif
                                        @if($result->type)
                                            <tr>
                                                <td>{{__('Type')}}</td>
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">
                                                    @if($result->type == 'manage')
                                                            {{__('management')}}
                                                        @elseif($result->type == 'star')
                                                            {{__('Special Properties')}}
                                                        @else
                                                            {{__('Ads')}}
                                                        @endif
                                                    </span></td>
                                            </tr>
                                        @endif

                                        @if($result->type_count)
                                        <tr>
                                            <td>{{__('Count Per Day')}}</td>
                                            <td>{{ $result->type_count }}</td>
                                        </tr>
                                        @endif

                                        @if($result->properties_count)
                                        <tr>
                                            <td>{{__('Properties Count')}}</td>
                                            <td>{{ $result->properties_count }}</td>
                                        </tr>
                                        @endif

                                        @if($result->price)
                                            <tr>
                                                <td>{{__('Package Price')}}</td>
                                                <td>{{number_format($result->price)}}</td>
                                            </tr>
                                        @endif
                                        @if($result->offer)
                                            <tr>
                                                <td>{{__('Offer')}}</td>
                                                <td>{{number_format($result->offer)}}</td>
                                            </tr>
                                        @endif
                                        @if($result->duration)
                                            <tr>
                                                <td>{{__('Duration') .' '. __('Per Day')}}</td>
                                                <td>{{number_format($result->duration)}}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Status')}}</td>
                                            @if($result->status == 'active')
                                                <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Active')}}</span></td>
                                            @else
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('In-Active')}}</span></td>
                                            @endif
                                        </tr>

                                        @if($result->discount_type && $result->discount_value)
                                            <tr>
                                                <td>{{__('Discount Type')}}</td>
                                                <td><span class="k-badge  k-badge--info k-badge--inline k-badge--pill">{{__(ucwords($result->discount_type))}}</span></td>
                                            </tr>
                                        @endif

                                        @if($result->discount_value)
                                        <tr>
                                            <td>{{__('Discount Value')}}</td>
                                            @if($result->discount_value)
                                                <td>
                                                    @if($result->discount_type == 'fixed')
                                                        {{ amount($result->discount_value,true) }}
                                                    @else
                                                        {{  $result->discount_value.' %' }}
                                                    @endif
                                                </td>
                                            @else
                                                <td>
                                                    {{__('0.00')}}
                                                </td>
                                            @endif
                                        </tr>
                                        @endif

                                        @if($result->discount_value && $result->discount_from  && $result->discount_from != '0000-00-00')
                                            <tr>
                                                <td>{{__('Discount From')}}</td>
                                                <td>
                                                    {{ date('Y-m-d',strtotime($result->discount_from))}}
                                                </td>
                                            </tr>
                                        @endif
                                        @if($result->discount_value && $result->discount_to && $result->discount_to != '0000-00-00')
                                            <tr>
                                                <td>{{__('Discount To')}}</td>
                                                <td>
                                                    {{ date('Y-m-d',strtotime($result->discount_to))}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->discount_code)
                                            <tr>
                                                <td>{{__('Discount Code')}}</td>
                                                <td>{{ $result->discount_code }}</td>
                                            </tr>
                                        @endif

                                        @if($result->discount_code && $result->discount_code_from  && $result->discount_code_from != '0000-00-00')
                                            <tr>
                                                <td>{{__('Discount Code From')}}</td>
                                                <td>
                                                    {{ date('Y-m-d',strtotime($result->discount_code_from))}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->discount_code && $result->discount_code_to  && $result->discount_code_to != '0000-00-00')
                                            <tr>
                                                <td>{{__('Discount Code To')}}</td>
                                                <td>
                                                    {{ date('Y-m-d',strtotime($result->discount_code_to))}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->discount_code_value && $result->discount_code)
                                            <tr>
                                                <td>{{__('Discount Code Value')}}</td>
                                                <td>{{ amount($result->discount_code_value) }}</td>
                                            </tr>
                                        @endif

                                        @if($result->percentage)
                                            <tr>
                                                <td>{{__('Percentage')}}</td>
                                                <td>{{ $result->percentage.' %' }}</td>
                                            </tr>
                                        @endif


                                        {{--<tr>--}}
                                            {{--<td>{{__('Subscribers Count')}}</td>--}}
                                            {{--<td>{{ $result->subscribers_count  }}</td>--}}
                                        {{--</tr>--}}

                                        {{--<tr>--}}
                                            {{--<td>{{__('UnSubscribers Count')}}</td>--}}
                                            {{--<td>{{ $result->unsubscribers_count  }}</td>--}}
                                        {{--</tr>--}}

                                        {{--<tr>--}}
                                            {{--<td>{{__('Subscribe Monthly Value')}}</td>--}}
                                            {{--<td>{{ amount($result->subscribe_monthly,true)  }}</td>--}}
                                        {{--</tr>--}}

                                        {{--@if($result->subscribe_from)--}}
                                            {{--<tr>--}}
                                                {{--<td>{{__('Subscribe From')}}</td>--}}
                                                {{--<td>--}}
                                                    {{--{{ date('Y-m-d',strtotime($result->subscribe_from))}}--}}
                                                {{--</td>--}}
                                            {{--</tr>--}}
                                        {{--@endif--}}

                                        {{--@if($result->subscribe_to)--}}
                                            {{--<tr>--}}
                                                {{--<td>{{__('Subscribe To')}}</td>--}}
                                                {{--<td>--}}
                                                    {{--{{ date('Y-m-d',strtotime($result->subscribe_to))}}--}}
                                                {{--</td>--}}
                                            {{--</tr>--}}
                                        {{--@endif--}}

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
                        <div class="col-lg-4 col-xl-4  order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Information Ar')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <td>{{__('Title')}}</td>
                                            <td>
                                                {{$result->title_ar}}
                                            </td>
                                        </tr>

                                        @if($result->content_ar)
                                            <tr>
                                                <td>{{__('Content')}}</td>
                                                <td>
                                                    {!! $result->content_ar !!}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->meta_key_ar)
                                            <tr>
                                                <td>{{__('Meta Key')}}</td>
                                                <td>
                                                    {{$result->meta_key_ar}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->meta_description_ar)
                                            <tr>
                                                <td>{{__('Meta Description')}}</td>
                                                <td>
                                                    {{$result->meta_description_ar}}
                                                </td>
                                            </tr>
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        <div class="col-lg-4 col-xl-4  order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">
                                            {{__('Information En')}}
                                        </h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <div class="tab-content">
                                        <table class="table table-striped">
                                            <tbody>
                                            <tr>
                                                <td>{{__('Title')}}</td>
                                                <td>
                                                    {{$result->title_en}}
                                                </td>
                                            </tr>
                                            @if($result->content_en)
                                                <tr>
                                                    <td>{{__('Content')}}</td>
                                                    <td>
                                                        {!! $result->content_en !!}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($result->meta_key_en)
                                                <tr>
                                                    <td>{{__('Meta Key')}}</td>
                                                    <td>
                                                        {{$result->meta_key_en}}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($result->meta_description_en)
                                                <tr>
                                                    <td>{{__('Meta Description')}}</td>
                                                    <td>
                                                        {{$result->meta_description_en}}
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>

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
    </script>
@endsection