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
                        <label>{{__('Created At')}}</label>
                        <div class="input-daterange input-group" id="k_datepicker_5">
                            {!! Form::text('created_at1',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                            </div>
                            {!! Form::text('created_at2',null,['class'=>'form-control','autocomplete'=>'off']) !!}
                        </div>
                        <span class="form-text text-muted">{{__('Linked pickers for date range selection')}}</span>

                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('ID')}}</label>
                            {!! Form::number('id',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Select Status')}}</label>
                            {!! Form::select('status',[''=>__('All'),'for_rent'=>__('For Rent'),'rented'=>__('Rented')],null,['class'=>'form-control status-select']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Publish Status')}}</label>
                            {!! Form::select('publish',[''=>__('All'),'1'=>__('Active'),'0'=>__('In-Active')],null,['class'=>'form-control publish-select']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">

                        <div class="col-md-6">
                            <label>{{__('Type')}}<span class="red-star">*</span></label>
                            @php
                                $typesData = [''=>__('Select Type')];
                                foreach ($property_types as $key => $value){
                                    $typesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('property_type_id',$typesData,null,['class'=>'form-control property-type-select','autocomplete'=>'off','id'=>'filterPropertyTypeSelect']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Purpose')}}<span class="red-star">*</span></label>
                            @php
                                $purposesData = [''=>__('Select Purpose')];
                                foreach ($purposes as $key => $value){
                                    $purposesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('purpose_id',$purposesData,null,['class'=>'form-control purpose-select','autocomplete'=>'off']) !!}
                        </div>

                    </div>

                    <div  id="params-render" class="form-group row mb1 param-div"></div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Data Source')}}</label>
                            @php
                                $dataSourcesData = [''=>__('Select Data Source')];
                                foreach ($data_sources as $key => $value){
                                    $dataSourcesData[$value->id] = $value->name;
                                }
                            @endphp
                            {!! Form::select('data_source_id',$dataSourcesData,null,['class'=>'form-control data-source-select','autocomplete'=>'off']) !!}
                        </div>

                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Owner')}}</label>
                            {!! Form::select('owner_id',[''=> __('Select Owner')],null,['class'=>'form-control owner-select','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Renter')}}</label>
                            {!! Form::select('renter_id',[''=> __('Select Renter')],null,['class'=>'form-control renter-select','autocomplete'=>'off']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Area')}}</label>
                            {!! Form::select('area_id',[''=> __('Select Area')],null,['class'=>'form-control  area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-4">
                            <label>{{__('Building Number')}}</label>
                            {!! Form::text('building_number',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-4">
                            <label>{{__('Flat Number')}}</label>
                            {!! Form::text('flat_number',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-4">
                            <label>{{__('Floor')}}</label>
                            @php
                                $propertyFloors = [/*''=>__('Select Floor'),*/'basement'=>__('Basement'),'ground'=>__('Ground')];
                                $floors = range(1,100);
                                foreach ($floors as $key => $value){
                                    $propertyFloors[$value] = $value;
                                }

                            @endphp
                            {!! Form::select('floor[]',$propertyFloors,null,['class'=>'form-control floor-select','id'=>'floor-form-input','autocomplete'=>'off','multiple']) !!}
                        </div>
                    </div>


                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Title')}}</label>
                            {!! Form::text('title',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Description')}}</label>
                            {!! Form::text('description',null,['class'=>'form-control']) !!}
                        </div>

                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Price From')}}</label>
                            {!! Form::number('price1',null,['class'=>'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Price To')}}</label>
                            {!! Form::number('price2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group row mb1">
                        <div class="col-md-6">
                            <label>{{__('Space From')}}</label>
                            {!! Form::number('space1',null,['class'=>'form-control']) !!}
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Space To')}}</label>
                            {!! Form::number('space2',null,['class'=>'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group row mb1">
                        <div class="col-md-12">
                            <label>{{__('Address')}}</label>
                            {!! Form::text('address',null,['class'=>'form-control']) !!}
                        </div>
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

                @if(staffCan('download-property-excel'))
                <a href="javascript:filterFunction($('#filterForm'),true)" class="btn btn-sm btn-elevate btn-brand">
                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Download Excel')}}</span>
                    <i class="flaticon-download k-padding-l-5 k-padding-r-0"></i>
                </a>
                @endif

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



@section('footer')
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

    <script type="text/javascript">

      noAjaxSelect2('.purpose-select','{{__('Purpose')}}','{{App::getLocale()}}');
      noAjaxSelect2('.property-type-select','{{__('Type')}}','{{App::getLocale()}}');
      noAjaxSelect2('.data-source-select','{{__('Data Source')}}','{{App::getLocale()}}');
      noAjaxSelect2('.contract-type-select','{{__('Contract Type')}}','{{App::getLocale()}}');
      noAjaxSelect2('.floor-select','{{__('Select Floor')}}','{{App::getLocale()}}');
      noAjaxSelect2('.status-select','{{__('Status')}}','{{App::getLocale()}}');
      noAjaxSelect2('.publish-select','{{__('Publish')}}','{{App::getLocale()}}');



      simpleAjaxSelect2('.owner-select','owner',2,'{{__('Owner')}}');
      simpleAjaxSelect2('.renter-select','renter',2,'{{__('Renter')}}');
      simpleAjaxSelect2('.area-select','area',1,'{{__('Select Area')}}');


        $datatable = $('#datatable-main').DataTable({
            "iDisplayLength": 25,
            columns: [
                { name: 'id' },
                { name: 'owner_id' },
                { name: 'property_type_id' },
                { name: 'status' },
                { name: 'publish' },
                { name: 'created_at' },
                { name: 'action' },
            ],
            "columnDefs": [
                //{ "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": false, "targets": 3 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },
                { "orderable": false, "targets": 6 },


            ],
            processing: true,
            serverSide: true,
            orderCellsTop: true,
            "order": [[ 0, "asc" ]],
            "ajax": {
                "url": "{{url()->full()}}",
                "type": "GET",
                "data": function(data){
                    data.isDataTable = "true";
                }
            }
        });



        function filterFunction($this,downloadExcel = false){

            if(downloadExcel == false) {
                $url = '{{url()->full()}}?is_total=true&'+$this.serialize();
                $datatable.ajax.url($url).load();
                $('#filter-modal').modal('hide');
            }else{
                $url = '{{url()->full()}}?is_total=true&isDataTable=true&'+$this.serialize()+'&downloadExcel='+downloadExcel;
                location = $url;
            }

        }


        function dropdownMenuArea($id){
            $back = 0;
            if($id == 0){
                $id = $('#th_area_header').val();
                $back = 1;
            }

            $('#th_area_header').val($id).change();
            $.ajax({
                url: "{{route('system.misc.ajax',['type'=>'dropdownMenuArea'])}}",
                method:"GET",
                data: {
                    'id':$id,
                    'back':$back
                },
                cache: false
            }).done(function( data ) {
                if(empty(data)){
                    return false;
                }

                var $return = '';

                if(data.area_type_id != 1){
                    $return += '<li><a onclick="dropdownMenuArea(0);" href="javascript:void(0);">{{__('>')}}</a></li>';
                }



                $.each(data.areas,function(key,value){
                    $return += '<li><a onclick="dropdownMenuArea('+value.id+');" href="javascript:void(0);">'+value.name+'</a></li>';
                });

                $('#dropdown_menu_id').html($return);

            });
        }

      function publishProperty($routeName,$reload = false){

          if(!confirm("{{__('Are you sure?')}}")){
              return false;
          }

          if($reload == undefined){
              $reload = 3000;
          }
          addLoading();

          $.post(
              $routeName,
              {
                  '_method':'POST',
                  '_token':$('meta[name="csrf-token"]').attr('content')
              },
              function(response){
                  removeLoading();
                  if(isJSON(response)){
                      $data = response;
                      if($data.status == true){
                          toastr.success($data.message, 'Success !', {"closeButton": true});
                          if($reload){
                              setTimeout(function(){location.reload();},$reload);
                          }
                          $url = '{{url()->full()}}?is_total=true&'+$('#filterForm').serialize();
                          $datatable.ajax.url($url).load();
                      }else{
                          toastr.error($data.message, 'Error !', {"closeButton": true});
                      }
                  }
              }
          )
      }

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

        .multi-table-div .select2-search__field{
            width: 100px !important;
        }

        /*.select2-container--default .select2-selection--multiple .select2-selection__clear{*/
        /*    display: none;*/
        /*}*/

        /*.select2-selection {*/
        /*    display: inline-table;*/
        /*    width: 100%;;*/
        /*}*/

    </style>
@endsection