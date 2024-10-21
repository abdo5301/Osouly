@extends('system.layout')
@section('content')


    <div class="modal fade" id="sales-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {!! Form::open(['id'=>'salesForm','onsubmit'=>'return false;','class'=>'k-form']) !!}
                <input type="hidden" id="importer_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Staff')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row mb1">
                        <div class="col-md-12 param-div">
                            {{--<select name="to_sales_id[]" class="form-control  sales-select" multiple id="select_to_sales_id" style="width: 100%;">--}}
                                {{--<option >{{__('Select Sales')}}</option>--}}
                            {{--</select>--}}
                            @php
                                $salesViewSelect = array();
                                $salesViewSelect = $salesViewSelect+array_column(getStaff()->toArray(),'name','id');
                            @endphp
                            {!! Form::select('to_sales_id[]',$salesViewSelect,null,['style'=>'width: 100%','class'=>'form-control sales-select','id'=>'select_to_sales_id','autocomplete'=>'off','multiple']) !!}
                            <span id="select_to_sales_id_error" style="color: red"></span>
                        </div>
                        {{--                        <input type="hidden" name="data_ids" id="data_ids">--}}
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                    <input type="submit" class="btn btn-outline-primary btn-md dist-staff" value="{{__('Save')}}">
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
            <div class="k-portlet__body table-responsive">
                <div id="form-alert-message"></div>

                <!--begin: Datatable -->
                <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-main">
                    <thead>
                    <tr>
                        @foreach($tableColumns as $key => $value)
                            <th>{{$value}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        @foreach($tableColumns as $key => $value)
                            <th>{{$value}}</th>
                        @endforeach
                    </tr>
                    </tfoot>
                </table>

                <!--end: Datatable -->
            </div>
        </div>
    </div>

    <!-- end:: Content Body -->
</div>
<!-- end:: Content -->
@endsection
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />

    <style>
        .sales-select{
            width: 100% !important;
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

         .select2-container--default .select2-selection--single .select2-selection__clear,.select2-selection__choice__remove{
            font-size: large;
            color: red !important;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }
    </style>
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script type="text/javascript">

        noAjaxSelect2('.sales-select','{{__('Select Staff')}}','{{App::getLocale()}}');
        {{--simpleAjaxSelect2('.sales-select','staff',1,'{{__('Select Sales')}}');--}}

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

       function set_importer(m_id){
            $('#importer_id').val(m_id);
            $('#select_to_sales_id').val('').change();
        }

        $(document).ready(function(){
            $('body').on('click', '.dist-staff', function (e) {
                e.preventDefault();
                $('.param-div').find('.select2-selection--multiple').css('border-color','#4d65dc94');
                $('#select_to_sales_id_error').text('');

                var id = $('#importer_id').val();
                var sales_ids = $('#select_to_sales_id').val();

                if(!$('#select_to_sales_id').val() || empty($('#select_to_sales_id').val())){
                    $('.param-div').find('.select2-selection--multiple').css('border-color','red');
                    $('#select_to_sales_id_error').text('{{__('You must select sales to distribute')}}');
                    return false;
                }
                // alert('here');
                $.ajax({
                    url:'{{route('system.importer.distribute')}}',
                    type: 'post',
                    data: {'importer_id':id,'sales_ids':sales_ids,'_token': '{!! csrf_token() !!}'},
                    dataType: 'json',
                    beforeSend: function() {
                        addLoading();
                    },
                    complete: function() {
                        removeLoading();
                    },
                    success: function(json) {
                        console.log(json);
                        if (json['error']) {
                            // $("html, body").animate({ scrollTop: 0 }, "fast");
                            // pageAlert('#form-alert-message','error',json['error']);
                            $('.param-div').find('.select2-selection--multiple').css('border-color','red');
                            $('#select_to_sales_id_error').text(json['error']);
                        } else {
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            pageAlert('#form-alert-message','success',json['success']);
                            $('#sales-modal').modal('hide');
                        }

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            });
        });



    </script>

@endsection