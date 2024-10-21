@extends('system.layout')
@section('content')

    <div class="modal fade" id="filter-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'filterForm','onsubmit'=>'filterFunction($(this));return false;','class'=>'k-form']) !!}

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Filter')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  param-div">
                    <div class="form-group mb1">
                        <label>{{__('Date')}}</label>
                        <div class="input-daterange input-group k_datepicker_5">
                            {!! Form::text('date1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('date2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Filter')}}">
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
                    <a href="#" data-toggle="modal" data-target="#filter-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Search on below data')}}" data-placement="left">
                        <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Filter')}}</span>
                        <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- end:: Content Head -->


        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">

            <div class="row">

               @foreach($due_data as $due)
                <div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">
                    <!--begin::Portlet-->
                    <a href="{{route('system.invoice.index',$due['filter'])}}">
                        <div class="k-portlet k-portlet--fit k-portlet--height-fluid">
                            <div class="k-portlet__body k-portlet__body--fluid">
                                <div class="k-widget-3 k-widget-3--brand">
                                    <div class="k-widget-3__content">
                                        <div class="k-widget-3__content-info">
                                            <div class="k-widget-3__content-section">
                                                <div class="k-widget-3__content-title">
                                                    {{--<i class="k-menu__link-icon flaticon-users"></i>--}}
                                                    {{$due['name']}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="k-widget-3__content-info">

                                            <div class="k-widget-3__content-section">
                                                <span class="k-widget-3__content-number">{{$due['total']}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!--end::Portlet-->
                </div>
               @endforeach

                </div>

        </div>
        <!-- end:: Content Body -->

    </div>

    <!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>

    <script type="text/javascript">

        function submitMainForm(){
            formSubmit(
                '{{route('system.report.upload-credit')}}',
                new FormData($('#main-form')[0]),
                function ($data) {
                    if($data.status === false){
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',$data.message);
                    }else{
                        //window.location = $data.data.url;
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','success',$data.message);
                        $('#main-form')[0].reset();
                    }

                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

        function filterFunction($this){
                $url = '{{url()->full()}}?is_total=true&custom_date=true&'+$this.serialize();
                location = $url;
        }

        $('.multiple-select2').select2();


        $('input[type="text"]').focus(function() {
            $(this).addClass("focus");
        });

        $('input[type="text"]').blur(function() {
            $(this).removeClass("focus");
        });

    </script>

@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single .select2-selection__clear{
            font-size: large;
            color: red;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }

        .form-control-sm {
            min-width:100px;
        }

        .param-div .select2-container--default{
            width: 100% !important;
        }
        .param-div .select2-search__field{
            width: 100% !important;
        }

        .param-div .select2-container--default .select2-selection--multiple .select2-selection__clear{
            display: none;
        }

        .param-div .select2-selection {
            display: inline-table;
            width: 100%;;
        }

    </style>
@endsection