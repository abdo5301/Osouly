@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
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
                        <div class="col-md-12 col-lg-12 col-xl-12">
                            <div class="k-profile__main">

                                <div class="k-profile__main-info">
                                    <div class="k-profile__main-info-name">
                                        {{$result->name}}
                                    </div>


                                    <div style="margin-bottom: 0.1rem;" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-time-1"></i></span>
                                        <span class="k-profile__contact-item-text">{{__('Database')}}: {{$result->database_name}} </span>
                                    </div>

                                    <div style="margin-bottom: 0.1rem;padding-top:10px;" class="k-profile__contact-item">
                                        <span class="k-profile__contact-item-icon"><i class="flaticon-time-2"></i></span>
                                        <span class="k-profile__contact-item-text">{{__('Created At')}}: {{$result->created_at->format('Y-m-d h:i A')}} ( {{$result->created_at->diffForHumans()}} )</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="k-profile__nav">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_1" role="tab">
                                {{__('General')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_2" role="tab">
                                {{__('Activity Log')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_3" role="tab">
                                {{__('Setting')}}
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('General')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table class="table table-striped- table-bordered table-hover table-checkable">
                                <thead>
                                <tr>
                                    <th>{{__('#')}}</th>
                                    <th>{{__('Value')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>{{__('Staff')}}</th>
                                        <th>{{number_format($count['staff'])}}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('Clients')}}</th>
                                        <th>{{number_format($count['clients'])}}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('Properties')}}</th>
                                        <th>{{number_format($count['properties'])}}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('Requests')}}</th>
                                        <th>{{number_format($count['requests'])}}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('Importer')}}</th>
                                        <th>{{number_format($count['importer'])}}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('Importer Data')}}</th>
                                        <th>{{number_format($count['importer_data'])}}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="k_tabs_1_2" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Activity Log')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-activity">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('Staff')}}</th>
                                    <th>{{__('Model')}}</th>
                                    <th>{{__('Created At')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('Staff')}}</th>
                                    <th>{{__('Model')}}</th>
                                    <th>{{__('Created At')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="tab-pane fade show active" id="k_tabs_1_3" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Setting')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table class="table table-striped- table-bordered table-hover table-checkable">
                                <thead>
                                <tr>
                                    <th>{{__('#')}}</th>
                                    <th>{{__('Value')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>{{__('Status')}}</th>
                                    <th>
                                        <select onchange="location = '{{route('system.cloud.setting',[$result->id])}}?name=system_status&value='+$(this).val();" class="form-control">
                                            <option @if($systemStatus == 'active') selected @endif value="active">{{__('Active')}}</option>
                                            <option @if($systemStatus != 'active') selected @endif value="in-active">{{__('In-Active')}}</option>
                                        </select>
                                    </th>
                                </tr>
                                </tbody>
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
                $datatableRequest = $('#datatable-activity').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "activity";
                        }
                    }
                });
            </script>
@endsection
