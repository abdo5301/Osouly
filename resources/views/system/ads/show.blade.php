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
            <div class="k-content__head-toolbar">
                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="{{route('system.ads.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Ads')}}" data-placement="left">
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
                                    <table class="table table-striped">
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>

                                        @if($result->url)
                                            <tr>
                                                <td>{{__('URL')}}</td>
                                                <td> <a target="_blank" href="{{$result->url}}">{{$result->url}}</a> </td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td>{{__('Type')}}</td>
                                            @if($result->type == 'osouly')
                                                <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__('Osouly')}}</span></td>
                                            @else
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__(ucfirst($result->type))}}</span></td>
                                            @endif
                                        </tr>

                                        <tr>
                                            <td>{{__('Page')}}</td>
                                            <td><span class="k-badge  k-badge--success k-badge--inline k-badge--pill">{{__(ucfirst(str_replace('_',' ',$result->page)))}}</span></td>
                                        </tr>
                                        @if($result->date_from)
                                            <tr>
                                                <td>{{__('Date From')}}</td>
                                                <td>
                                                    {{$result->date_from}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->date_to)
                                            <tr>
                                                <td>{{__('Date To')}}</td>
                                                <td>
                                                    {{$result->date_to}}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($result->date_to && strtotime($result->date_to) < strtotime(date('Y-m-d')))
                                            <tr>
                                                <td>{{__('Status')}}</td>
                                                <td><span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{__('Expired')}}</span></td>
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
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>

                        @if(!empty($result->image) && is_file($result->image))
                            <div class="col-lg-12 col-xl-12 order-lg-1 order-xl-1">

                                <!--begin::Portlet-->
                                <div class="k-portlet k-portlet--height-fluid">
                                    <div class="k-portlet__head">
                                        <div class="k-portlet__head-label">
                                            <h3 class="k-portlet__head-title">{{__('Images')}}</h3>
                                        </div>
                                    </div>
                                    <div class="k-portlet__body">
                                        <ul id="image-view" class="pictures">
                                            <li><img src="{{asset($result->image)}}" alt="{{$result->title_en}}"></li>
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