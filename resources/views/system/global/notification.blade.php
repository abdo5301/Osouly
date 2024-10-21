@extends('system.layout')
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
            {{-- <div class="alert alert-light alert-elevate" role="alert">
                 <div class="alert-icon"><i class="flaticon-warning k-font-brand"></i></div>
                 <div class="alert-text">
                     With server-side processing enabled, all paging, searching, ordering actions that DataTables performs are handed off to a server where an SQL engine (or similar) can perform these actions on the large data set.
                     See official documentation <a class="k-link k-font-bold" href="https://datatables.net/examples/data_sources/server_side.html" target="_blank">here</a>.
                 </div>
             </div>--}}
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__head">
                    <div class="k-portlet__head-label">
                        <h3 class="k-portlet__head-title">
                            {{$pageTitle}}
                        </h3>
                    </div>
                </div>
                <div class="k-portlet__body">

                    <div class="card-body">
                        <div class="card-block card-dashboard" id="notification-data">
                            {{__('Loading...')}}
                        </div>

                        <div id="loading-notification"></div>

                    </div>




                </div>
            </div>
        </div>

        <!-- end:: Content Body -->
    </div>
    <!-- end:: Content -->
@endsection
@section('footer')
    <script type="text/javascript">
        $(document).ready(function(){
            getNotification('{{route("system.notifications.index")}}?page=1',true);
        });

        function getNotification($url,isFirst = false){
            $('#loading-notification-text').removeAttr('onclick').html('<h3>{{__('Loading...')}}</h3>');
            $.getJSON($url,function($data){
                if($data.next){
                    $('#loading-notification').html('<a href="javascript:void(0);" id="loading-notification-text" onclick="getNotification(\''+$data.next+'\')" class="dropdown-item text-muted text-xs-center"><h3>{{__('Load More...')}}</h3></a>');
                }else{
                    $('#loading-notification').remove();
                }
                if(!empty($data)){
                    if(isFirst){
                        $('#notification-data').html($data.content);
                    }else{
                        $('#notification-data').append($data.content);
                    }
                }
            });
        }
    </script>
@endsection