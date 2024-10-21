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

                {!! Form::open(['route' => isset($result) ? ['system.staff.update',$result->id]:'system.staff.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
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
                                <div class="col-md-6">
                                    <label>{{__('First Name')}}<span class="red-star">*</span></label>
                                    {!! Form::text('firstname',isset($result) ? $result->firstname : null,['class'=>'form-control','id'=>'firstname-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="firstname-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Last Name')}}<span class="red-star">*</span></label>
                                    {!! Form::text('lastname',isset($result) ? $result->lastname : null,['class'=>'form-control','id'=>'lastname-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="lastname-form-error"></div>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('E-mail')}}<span class="red-star">*</span></label>
                                    {!! Form::email('email',isset($result) ? $result->email : null,['class'=>'form-control','id'=>'email-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="email-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Mobile')}}<span class="red-star">*</span></label>
                                    {!! Form::text('mobile',isset($result) ? $result->mobile : null,['class'=>'form-control','id'=>'mobile-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="mobile-form-error"></div>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Password')}} @if(!isset($result)) <span class="red-star">*</span>@endif</label>
                                    {!! Form::password('password',['class'=>'form-control','id'=>'password-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="password-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Confirm password')}} @if(!isset($result)) <span class="red-star">*</span>@endif</label>
                                    {!! Form::password('password_confirmation',['class'=>'form-control','id'=>'password_confirmation-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="password_confirmation-form-error"></div>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Gender')}}<span class="red-star">*</span></label>
                                    {!! Form::select('gender',['male'=>__('Male'),'female'=>__('Female')],isset($result) ? $result->gender : null,['class'=>'form-control','id'=>'gender-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="gender-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Birth Date')}}<span class="red-star">*</span></label>
                                    {!! Form::text('birthdate',isset($result) ? $result->birthdate : null,['class'=>'form-control k_datepicker_1','id'=>'birthdate-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="birthdate-form-error"></div>
                                </div>

                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('National ID')}}<span class="red-star">*</span></label>
                                    {!! Form::text('national_id',isset($result) ? $result->national_id : null,['class'=>'form-control','id'=>'national_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="national_id-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Address')}}<span class="red-star">*</span></label>
                                    {!! Form::textarea('address',isset($result) ? $result->address : null,['class'=>'form-control','id'=>'address-form-input','autocomplete'=>'off','rows'=> 3]) !!}
                                    <div class="invalid-feedback" id="address-form-error"></div>
                                </div>
                            </div>



                            <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Job Title')}}<span class="red-star">*</span></label>
                                    {!! Form::text('job_title',isset($result) ? $result->job_title : null,['class'=>'form-control','id'=>'job_title-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="job_title-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Status')}}<span class="red-star">*</span></label>
                                    {!! Form::select('status',['active'=>__('Active'),'in-active'=>__('In-Active')],isset($result) ? $result->status : null,['class'=>'form-control','id'=>'status-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="status-form-error"></div>
                                </div>

                            </div>


                            <div class="form-group row">

                                <div class="col-md-12">
                                    <label>{{__('Permission Group')}}<span class="red-star">*</span></label>
                                    {!! Form::select('permission_group_id',array_column($PermissionGroup->toArray(),'name','id'),isset($result) ? $result->permission_group_id : null,['class'=>'form-control','id'=>'permission_group_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="permission_group_id-form-error"></div>
                                </div>

                            </div>


                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Avatar')}}<span class="red-star">*</span></label>
                                {!! Form::file('avatar',['class'=>'form-control','id'=>'avatar-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="avatar-form-error"></div>
                            </div>

                        </div>



                        <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Description')}}</label>
                                    {!! Form::textarea('description',isset($result) ? $result->description : null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off','rows'=> 3]) !!}
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
                '{{isset($result) ? route('system.staff.update',$result->id):route('system.staff.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    window.location = $data.data.url;
                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }

    </script>
@endsection