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
                <div class="k-portlet__body">
                    {!! Form::open(['route' => isset($permission_group) ? ['system.permission-group.update',$permission_group->id]:'system.permission-group.store','files'=>true, 'method' => isset($permission_group) ?  'PATCH' : 'POST','class'=> 'k-form']) !!}
                    <div class="k-portlet__body">
                        @if($errors->any())
                            <div class="alert alert-danger fade show" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">{{__('Some fields are invalid please fix them')}}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @elseif(Session::has('status'))
                            <div class="alert alert-{{Session::get('status')}} fade show" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">{{ Session::get('msg') }}</div>
                                <div class="alert-close">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                    </button>
                                </div>
                            </div>
                        @endif


                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('name',isset($permission_group) ? $permission_group->name : null,['class'=>'form-control'.formError($errors,'name',true),'autocomplete'=>'off']) !!}
                                {!! formError($errors,'name') !!}
                            </div>

                            {{--<div class="col-md-6">--}}
                                {{--<label>{{__('Supervisor')}}<span class="red-star">*</span></label>--}}
                                {{--{!! Form::select('is_supervisor',['yes'=>__('Yes'),'no'=>__('No')],isset($permission_group->id) ? $permission_group->is_supervisor:old('is_supervisor'),['class'=>'form-control']) !!}--}}
                                {{--{!! formError($errors,'is_supervisor') !!}--}}
                            {{--</div>--}}
                            <input type="hidden" name="is_supervisor" value="yes">

                        </div>


                        <div class="form-group row">
                            <div class="col-md-12">
                                <a href="javascript:void(0);" class="btn btn-primary text-center" onclick="$('input[name=\'permissions[]\']').prop('checked',true)">
                                    <i class="fa fa-star"></i> {{__('Select All')}}
                                </a>
                                <a href="javascript:void(0);" class="btn btn-outline-warning text-center" onclick="$('input[name=\'permissions[]\']').prop('checked',false)">
                                    <i class="fa fa-star-o"></i> {{__('Deselect All')}}
                                </a>
                            </div>
                        </div>
                        <div class="form-group row">
                            @foreach($permissions as $permission)
                                <div class="col-md-12">
                                    <div style="margin-bottom: 20px;" class="bs-callout-primary callout-border-left callout-bordered p-2 permissions">
                                        <h4 class="primary">{{ucfirst($permission['name'])}}</h4>
                                        <div class="row">
                                            @foreach($permission['permissions'] as $key=>$val)
                                                <label class="col-sm-4">
                                                    {!! Form::checkbox("permissions[]", "$key", isset($permission_group->id) ? !array_diff($val,$currentpermissions) : false) !!}
                                                    {!! __(ucfirst(str_replace('-',' ',$key))) !!}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </div>


                        <div class="k-portlet__foot">
                            <div class="k-form__actions">
                                <div class="row" style="float: right;">
                                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                                </div>
                            </div>
                        </div>



                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            <!-- end:: Content Body -->
        </div>
        <!-- end:: Content -->
        @endsection
        @section('footer')
            <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
            <script type="text/javascript">
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
                    /*,
                    "fnPreDrawCallback": function(oSettings) {
                        for (var i = 0, iLen = oSettings.aoData.length; i < iLen; i++) {
                            if(oSettings.aoData[i]._aData[6] != ''){
                                oSettings.aoData[i].nTr.className = oSettings.aoData[i]._aData[6];
                            }
                        }
                    }*/
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

            </script>
@endsection