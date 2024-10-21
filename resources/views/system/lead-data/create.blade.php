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

                    {!! Form::open(['route' => isset($result) ? ['system.lead-data.update',$result->id]:'system.lead-data.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body">

                        <div id="form-alert-message"></div>

                        {{--@if($errors->any())
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
                        @endif--}}


                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Client Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('name',isset($result) ? $result->name: null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Client Mobile')}}<span class="red-star">*</span></label>
                                {!! Form::text('mobile',isset($result) ? $result->mobile: null,['class'=>'form-control','id'=>'mobile-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="mobile-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Client E-mail')}}</label>
                                {!! Form::text('email',isset($result) ? $result->email: null,['class'=>'form-control','id'=>'email-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="email-form-error"></div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Project Name')}}</label>
                                {!! Form::text('project_name',isset($result) ? $result->project_name: null,['class'=>'form-control','id'=>'project_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="project_name-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Campaign Name')}}</label>
                                {!! Form::text('campaign_name',isset($result) ? $result->campaign_name: null,['class'=>'form-control','id'=>'campaign_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="campaign_name-form-error"></div>
                            </div>
@if(!empty($data_source))
                            <div class="col-md-4">
                                <label>{{__('Data Source')}}<span class="red-star">*</span></label>
                                @php
                                    $DataSourceData = [];
                                        foreach ($data_source as $key => $value){
                                            $DataSourceData[$value->id] = $value->name;
                                        }
                                @endphp
                                {!! Form::select('data_source_id',$DataSourceData,isset($result) ? $result->data_source_id: null,['class'=>'form-control','id'=>'data_source_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="lead_status_id-form-error"></div>
                            </div>
@endif
                        </div>

                        <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Client Description')}}</label>
                            {!! Form::textarea('description',isset($result) ? $result->description: null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="description-form-error"></div>
                        </div>
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

                function submitMainForm(){
                    formSubmit(
                        '{{isset($result) ? route('system.lead-data.update',$result->id):route('system.lead-data.store')}}',
                        new FormData($('#main-form')[0]),
                        function ($data) {
                            if($data.status === false){
                                $("html, body").animate({ scrollTop: 0 }, "fast");
                                pageAlert('#form-alert-message','error',$data.message);
                            }else{
                                window.location = $data.data.url;
                            }

                        },
                        function ($data){
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            pageAlert('#form-alert-message','error',$data.message);
                        }
                    );
                }

            </script>
@endsection