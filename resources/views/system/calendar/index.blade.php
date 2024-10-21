@extends('system.layout')
@section('content')

    <div class="modal fade" id="add-reminder-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['route' => ['system.calendar.store'], 'method' =>'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Add Reminder')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div id="form-alert-message"></div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Date & Time')}}</label>
                            {!! Form::text('date_time',null,['class'=>'form-control k_datetimepicker_1','id'=>'date_time-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="date_time-form-error"></div>

                        </div>
                    </div>

                    {{--<div class="form-group row mb1">--}}
                        {{--<div class="col-md-12">--}}
                            {{--<label>{{__('Sign Type')}}</label>--}}
                            {{--{!! Form::select('sign_type',[''=>__('General'),'property'=>__('Property'),'request'=>__('Request'),'client'=>__('Client')],null,['class'=>'form-control','id'=>'sign_type-form-input','autocomplete'=>'off']) !!}--}}
                            {{--<div class="invalid-feedback" id="sign_type-form-error"></div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {!! Form::hidden('sign_type',null) !!}




                    <div class="form-group row mb1" id="sign_id_div" style="display:none;">
                        <div class="col-md-12">
                            <label>{{__('Sign ID')}}</label>
                            {!! Form::number('sign_id',null,['class'=>'form-control','id'=>'sign_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="sign_id-form-error"></div>
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Comment')}}</label>
                            {!! Form::textarea('comment',null,['class'=>'form-control','id'=>'comment-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="comment-form-error"></div>

                        </div>
                    </div>



                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Add')}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>




    <div class="modal fade" id="show-event-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Calendar Data')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="show-event-modal-data">

                </div>

                <div class="modal-footer">
                    <button  class="btn btn-danger btn-md" onclick="deleteRrminder();">{{__('Delete')}}</button>

                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>











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
            <div class="k-content__head-wrapper">

                <a href="#" data-toggle="modal" data-target="#add-reminder-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Add')}}</span>
                    <i class="flaticon2-plus-1 k-padding-l-5 k-padding-r-0"></i>
                </a>
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
                        {{$pageTitle}}{{__("'s data")}}
                    </h3>
                </div>
            </div>
            <div class="k-portlet__body">
                <div id="k_calendar"></div>
            </div>
        </div>
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('footer')

    <script src="{{asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script type="text/javascript">

        function deleteRrminder(){
            $id = $('#modal_reminder_id').val();
            if(!confirm('{{__('Do you want to delete reminder?')}}')){
                return false;
            }

            $('#show-event-modal').modal('hide');
            addLoading();
            $.get('{{route('system.calendar.delete')}}?id='+$id,function($data){
                location.reload();
            });

        }

        jQuery(document).ready(function() {
            var todayDate = moment().startOf('day');
            var YM = todayDate.format('YYYY-MM');
            var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
            var TODAY = todayDate.format('YYYY-MM-DD');
            var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

            $('#k_calendar').fullCalendar({
                isRTL: KUtil.isRTL(),
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,listWeek'
                },
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                navLinks: true,
                events: '{{route('system.calendar.ajax')}}',

                eventClick: function(info) {
                    showEvent(info.id);
                },


                eventRender: function(event, element) {
                    if (element.hasClass('fc-day-grid-event')) {
                        element.data('content', event.description);
                        element.data('placement', 'top');
                        KApp.initPopover(element);
                    } else if (element.hasClass('fc-time-grid-event')) {
                        //element.find('.fc-title').append('<div class="fc-description">' + event.description + '</div>');
                    } else if (element.find('.fc-list-item-title').lenght !== 0) {
                        //element.find('.fc-list-item-title').append('<div class="fc-description">' + event.description + '</div>');
                    }
                }
            });
        });

        function showEvent($id){
            addLoading();
            $.get('{{route('system.calendar.show')}}?id='+$id,function($data){
                removeLoading();

                if(!$data.status) return false;


                if($data.sign_type == 'App\\Models\\Call'){
                    window.open('{{route('system.call.index')}}?call_id='+$data.sign_id);
                    return;
                }

                $('#show-event-modal').modal('show');
                $('#show-event-modal-data').html($data.table);
            });
        }


        function submitMainForm(){
            formSubmit(
                '{{route('system.calendar.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    if($data.status == true){
                        $('#k_calendar').fullCalendar('refetchEvents');
                        $('#main-form')[0].reset();
                        $('#add-reminder-modal').modal('hide');
                    }else{
                        pageAlert('#form-alert-message','error',$data.message);
                    }
                },
                function ($data){
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

        $('.k_datetimepicker_1').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss'
        });

        function signType(){
            $value = $('#sign_type-form-input').val();
            if(!empty($value)){
                $('#sign_id_div').show();
            }else{
                $('#sign_id_div').hide();
            }
        }

        $('#sign_type-form-input').change(function(){
            signType();
        });

        $(document).ready(function(){
            signType();

            @if(request('sign_type') && request('sign_id'))
                $('#add-reminder-modal').modal('show');
            @endif
        });



    </script>
@endsection
@section('header')
    <link href="{{asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css" />
@endsection