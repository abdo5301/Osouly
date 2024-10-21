@extends('system.layout')
@section('header')
    <link href="{{asset('assets/custom/user/profile-v1.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
    <style>

        td:first-child {
            font-weight: bold
        }

        .select2-container--default .select2-selection--single .select2-selection__clear{
            font-size: large;
            color: red;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }
    </style>

@endsection
@section('content')

{{--    <div class="modal fade" id="lead-status-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                {!! Form::open(['id'=>'leadStatusForm','onsubmit'=>'changeLeadStatus($(this));return false;','class'=>'k-form']) !!}--}}

{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Select Status')}}</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <div class="form-group row mb1">--}}
{{--                        <div class="col-md-12">--}}
{{--                            <select name="lead_status_id" class="form-control  lead-status-select" id="select_lead_status_id" style="width: 100%;">--}}
{{--                                <option value="">{{__('Select Status')}}</option>--}}
{{--                                @foreach(getLeadStatus() as $key => $value)--}}
{{--                                    <option value="{{$value->id}}" @if($value->id == $result->lead_status_id) selected @endif >{{$value->name}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                            <span id="select_lead_status_id_error" style="color: red"></span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="modal-footer">--}}
{{--                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">--}}
{{--                    <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Save')}}">--}}
{{--                </div>--}}
{{--                {!! Form::close() !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}



<div class="modal fade" id="create-call-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['id'=>'main-form','onsubmit'=>'submitMainForm();return false;','class'=>'k-form']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{__('Create Call')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="form-alert-message"></div>


                    {!! Form::hidden('sign_id',$result->id) !!}
                    {!! Form::hidden('sign_type','leads') !!}

                @if(!$result->client)
                    <div class="form-group row" id="client-select-information">
                        <div class="col-md-10">
                            <label>{{__('Client')}}<span class="red-star">*</span></label>
                            {!! Form::select('client_id',[''=> __('Select Client')],null,['class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="client_id-form-error"></div>
                        </div>
                        <div class="col-md-2">
                            <label style="color: #FFF;">*</label>
                            <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.client.create',['addClientFromCall'=>'true'])}}');" class="form-control">
                                <i class="la la-plus"></i>
                            </a>
                        </div>
                    </div>
                @else
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-md-12">--}}
{{--                            <label>{{__('Client')}}<span class="red-star">*</span></label>--}}
{{--                            {!! Form::hidden('client_id',$result->client->id) !!}--}}
{{--                            {!! Form::text('client_id_hidden',$result->client->name,['class'=>'form-control','disabled'=>'disabled']) !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    {!! Form::hidden('client_id',$result->client->id) !!}

                @endif

                <div class="form-group row">
                    <div class="col-md-4">
                        <label>{{__('Type')}}<span class="red-star">*</span></label>
                        {!! Form::select('type',['in'=> __('IN'),'out'=> __('OUT')],null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="type-form-error"></div>
                    </div>


                    <div class="col-md-4">
                        <label>{{__('Status')}}<span class="red-star">*</span></label>
                        @php
                            $statusData = [''=>__('Select Status')];
                            foreach ($status as $key => $value){
                                $statusData[$value->id] = $value->{'name_'.App::getLocale()};
                            }
                        @endphp
                        {!! Form::select('call_status_id',$statusData,setting('default_call_status'),['class'=>'form-control','id'=>'call_status_id-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="call_status_id-form-error"></div>
                    </div>

                    <div class="col-md-4">
                        <label>{{__('Action')}}<span class="red-star">*</span></label>
                        @php
                            $purposesData = [''=>__('Select Action')];
                            foreach ($purposes as $key => $value){
                                $purposesData[$value->id] = $value->{'name_'.App::getLocale()};
                            }
                        @endphp
                        {!! Form::select('call_purpose_id',$purposesData,null,['class'=>'form-control','id'=>'call_purpose_id-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="call_purpose_id-form-error"></div>
                    </div>

                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <label>{{__('Remind Me')}}</label>
                        {!! Form::select('remind_me',['no'=>__('No'),'yes'=>__('Yes')],null,['class'=>'form-control','id'=>'remind_me-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="remind_me-form-error"></div>
                    </div>
                </div>

                <div class="form-group row" id="remind_me_on_div" style="display: none;">
                    <div class="col-md-12">
                        <label>{{__('Time of Remind')}}<span class="red-star">*</span></label>
                        {!! Form::text('remind_me_on',null,['class'=>'form-control k_datetimepicker_1','data-date-container=#create-call-modal','id'=>'remind_me_on-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="remind_me_on-form-error"></div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <label>{{__('Description')}}<span class="red-star">*</span></label>
                        {!! Form::textarea('description',null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                        <div class="invalid-feedback" id="description-form-error"></div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                <input type="submit" class="btn btn-outline-primary btn-md" value="{{__('Create')}}">
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>




    <!-- begin:: Content -->
    <div class="k-content	k-grid__item k-grid__item--fluid k-grid k-grid--hor" id="k_content">
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
        <!-- begin:: Content Head -->
        <div class="k-content__head	k-grid__item">
            <div class="k-content__head-main">
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

{{--                <div class="k-content__head-wrapper" style="margin-left:10px;">--}}
{{--                    <a href="#" data-toggle="modal" data-target="#lead-status-modal"  class="btn btn-sm btn-danger btn-brand"  title="{{__('Add Action')}}" data-placement="left">--}}
{{--                        {{__('Add Action')}}--}}
{{--                        <i style="padding-left:0px !important;" class="flaticon-refresh k-padding-l-5 k-padding-r-0"></i>--}}
{{--                    </a>--}}
{{--                </div>--}}

                <div class="k-content__head-wrapper" style="margin-left:10px;">
                    <a href="{{route('system.lead-data.edit',$result->id)}}" class="btn btn-sm btn-info btn-brand" data-toggle="k-tooltip" title="{{__('Edit Lead Data')}}" data-placement="left">
                        <i class="la la-edit"></i>
                    </a>
                </div>

            </div>
        </div>

        <!-- end:: Content Head -->

        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div class="k-portlet k-profile">
                @if($result->client)
                <div class="k-profile__content">
                    <div class="row">
                        <div class="col-md-12 col-lg-5 col-xl-4">
                            <div class="k-profile__main">

                                <div class="k-profile__main-info">
                                    <div class="k-profile__main-info-name">
                                        @if($result->client->investor_type == 'company')
                                            <a target="_blank" href="{{route('system.client.show',$result->client->id)}}">{{$result->client->company_name}}</a>
                                            <small>{{$result->client->name}}</small>
                                        @else
                                            <a target="_blank" href="{{route('system.client.show',$result->client->id)}}">{{$result->client->name}}</a>
                                        @endif

                                    </div>
                                    <div class="k-profile__main-info-position">
                                        {{__(ucfirst($result->client->type))}}
                                        ( {{__(ucfirst($result->client->investor_type))}} )
                                    </div>

                                    @if($result->client->created_notes)
                                        <div class="k-profile__main-info-position">
                                            <span class="k-profile__contact-item-icon"><i class="flaticon-user-settings"></i></span>
                                            <span class="k-profile__contact-item-text"> {{$result->client->created_notes}}</span>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        @if(staffCan('lead-manage-all') || staffCan('show-all-phones') || $result->transfer_to_sales_id == Auth::id() || $result->created_by_staff_id == Auth::id())
                        <div class="col-md-12 col-lg-4 col-xl-4">
                                <div class="k-profile__contact">
                                    @if($result->client->mobile1)
                                        <a onclick="save_log_phone()" style="margin-bottom: 0.1rem;" href="tel:{{$result->client->mobile1}}" class="k-profile__contact-item">
                                            <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                            <span class="k-profile__contact-item-text">{{$result->client->mobile1}}</span>
                                        </a>
                                    @endif
                                    @if($result->client->mobile2)
                                        <a onclick="save_log_phone()"  style="margin-bottom: 0.1rem;" href="tel:{{$result->client->mobile2}}" class="k-profile__contact-item">
                                            <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                            <span class="k-profile__contact-item-text">{{$result->client->mobile2}}</span>
                                        </a>
                                    @endif
                                    @if($result->client->phone)
                                        <a  onclick="save_log_phone()"  style="margin-bottom: 0.1rem;" href="tel:{{$result->client->phone}}" class="k-profile__contact-item">
                                            <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                            <span class="k-profile__contact-item-text">{{$result->client->phone}}</span>
                                        </a>
                                    @endif
                                    @if($result->client->email)
                                        <a  onclick="save_log_mail()"  style="margin-bottom: 0.1rem;" href="mailto:{{$result->client->email}}" class="k-profile__contact-item">
                                            <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                            <span class="k-profile__contact-item-text">{{$result->client->email}}</span>
                                        </a>
                                    @endif

                                </div>
                        </div>
                        @endif

                        <div class="col-md-12 col-lg-3 col-xl-4">
                                <div class="k-profile__stats">
                                    @if($result->client->address)
                                        <div class="k-profile__stats-item">
                                            <div class="k-profile__stats-item-label">{{__('Address')}}</div>
                                            <div class="k-profile__stats-item-chart">
                                                {{$result->client->address}}
                                            </div>
                                        </div>
                                    @endif
                                    @if($result->client->description)
                                        <div class="k-profile__stats-item">
                                            <div class="k-profile__stats-item-label">{{__('Description')}}</div>
                                            <div class="k-profile__stats-item-chart">
                                                {{$result->client->description}}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="k-profile__nav">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#k_tabs_1_1" role="tab">{{__('Lead Data')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_2" role="tab">
                                {{__('Calls')}}
                                @php
                                    $callsCount = $result->calls()->count();
                                @endphp
                                @if($callsCount)
                                    <span class="k-badge  k-badge--primary k-badge--inline k-badge--pill">{{$callsCount}}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_3" role="tab">
                                {{__('Reminders')}}

                                @php
                                    $remindersCount = $result->reminders()->count();
                                @endphp
                                @if($remindersCount)
                                    <span class="k-badge  k-badge--danger k-badge--inline k-badge--pill">{{$remindersCount}}</span>
                                @endif

                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#k_tabs_1_4" role="tab">
                                {{__('Log')}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!--end::Portlet-->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="k_tabs_1_1" role="tabpanel">

                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">{{__('Information')}}</h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <table class="table table-striped">
                                        {{--  <thead>
                                          <tr>
                                              <th>{{__('Key')}}</th>
                                              <th>{{__('Value')}}</th>
                                          </tr>
                                          </thead>--}}
                                        <tbody>
                                        <tr>
                                            <td>{{__('ID')}}</td>
                                            <td>{{$result->id}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Last Action')}}</td>
                                            <td>
                                                @php
                                               // $last_call = $result->calls()->orderBy('id','desc')->first();
                                                @endphp
                                                @if($result->last_call_purpose)
                                                    {!! '<b style="color:'.$result->last_call_purpose->color.'">'.$result->last_call_purpose->{'name_'.App::getLocale()}.'</b>'!!}
                                                @else
                                                    <b style="color:green">{{__('Fresh Lead')}}</b>
                                                @endif
{{--                                            @if($result->lead_status)--}}
{{--                                            {!! '<b style="color:'.$result->lead_status->color.'">'.$result->lead_status->{'name_'.App::getLocale()}.'</b>' !!}--}}
{{--                                            @else--}}
{{--                                                {{'--'}}--}}
{{--                                            @endif--}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Last Status')}}</td>
                                            <td>
                                                @if($result->last_call_status)
                                                    {!! '<b style="color:'.$result->last_call_status->color.'">'.$result->last_call_status->{'name_'.App::getLocale()}.'</b>'!!}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Last Description')}}</td>
                                            <td>{{$result->last_call_description ? $result->last_call_description : '--'}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Client Name')}}</td>
                                            <td>{{$result->name}}</td>
                                        </tr>
                                        @if(staffCan('lead-manage-all') || staffCan('show-all-phones') || $result->transfer_to_sales_id == Auth::id() || $result->created_by_staff_id == Auth::id())
                                            <tr>
                                            <td>{{__('Client Mobile')}}</td>
                                            <td><a  onclick="save_log_phone()"  style="margin-bottom: 0.1rem;" href="tel:{{$result->mobile}}" class="k-profile__contact-item">
                                                <span class="k-profile__contact-item-icon"><i class="flaticon-support"></i></span>
                                                <span class="k-profile__contact-item-text">{{$result->mobile}}</span>
                                            </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Client E-mail')}}</td>
                                            <td>
                                            @if($result->email)
                                            <a  onclick="save_log_mail()"  style="margin-bottom: 0.1rem;" href="mailto:{{$result->email}}" class="k-profile__contact-item">
                                                <span class="k-profile__contact-item-icon"><i class="flaticon-email-black-circular-button k-font-danger"></i></span>
                                                <span class="k-profile__contact-item-text">{{$result->email}}</span>
                                            </a>
                                            @else
                                                {{'--'}}
                                            @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>{{__('Client Description')}}</td>
                                            <td>{{$result->description ? $result->description : '--'}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Project Name')}}</td>
                                            <td>{{$result->project_name ? $result->project_name : '--'}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Campaign Name')}}</td>
                                            <td>{{$result->campaign_name ? $result->campaign_name : '--'}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Data Source')}}</td>
                                            <td>{{$result->data_source ? $result->data_source->{'name_'.App::getLocale()} : '--'}}</td>
                                        </tr>

                                        @if(staffCan('lead-manage-all'))
                                        <tr>
                                            <td>{{__('Transfer By')}}</td>
                                            <td>
                                                @if($result->transfer_by_staff)
                                                <a target="_blank" href="{{route('system.staff.show',$result->transfer_by_staff->id)}}">{{$result->transfer_by_staff->fullname}}</a>
                                                @else
                                                    {{'--'}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Transfer To')}}</td>
                                            <td>
                                                @if($result->transfer_to_sales)
                                                <a target="_blank" href="{{route('system.staff.show',$result->transfer_to_sales->id)}}">{{$result->transfer_to_sales->fullname}}</a>
                                                @else
                                                    {{'--'}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('Created By')}}</td>
                                            <td>
                                                @if($result->staff)
                                                <a target="_blank" href="{{route('system.staff.show',$result->staff->id)}}">{{$result->staff->fullname}}</a>
                                                @else
                                                    {{'--'}}
                                                @endif

                                            </td>
                                        </tr>
                                        @endif

{{--                                        <tr>--}}
{{--                                            <td>{{__('Requested')}}</td>--}}
{{--                                            <td>{!!  $result->requested == 'pending' ? '<span class="k-badge  k-badge--info k-badge--inline k-badge--pill">'.__(ucfirst($result->requested)).'</span>' : '<span class="k-badge  k-badge--success k-badge--inline k-badge--pill">'.__(ucfirst($result->requested)).'</span>' !!}</td>--}}
{{--                                        </tr>--}}

                                        <tr>
                                            <td>{{__('Created At')}}</td>
                                            <td>
                                                {{$result->created_at->format('Y-m-d h:i A')}} ({{$result->created_at->diffForHumans()}})
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{__('Last Update')}}</td>
                                            <td>
                                                {{$result->updated_at->format('Y-m-d h:i A')}} ({{$result->updated_at->diffForHumans()}})
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>
                        <div class="col-lg-6 col-xl-6 order-lg-1 order-xl-1">

                            <!--begin::Portlet-->
                            <div class="k-portlet k-portlet--tabs k-portlet--height-fluid">
                                <div class="k-portlet__head">
                                    <div class="k-portlet__head-label">
                                        <h3 class="k-portlet__head-title">
                                            {{__('Dashboard')}}
                                        </h3>
                                    </div>
                                </div>
                                <div class="k-portlet__body">
                                    <div class="tab-content">
                                        <table class="table table-striped">
                                            {{--<thead>
                                            <tr>
                                                <th>{{__('Key')}}</th>
                                                <th>{{__('Value')}}</th>
                                            </tr>
                                            </thead>--}}
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!--end::Portlet-->
                        </div>

                    </div>

                    <!--end::Row-->
                </div>


                <div class="tab-pane fade" id="k_tabs_1_2" role="tabpanel">

                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Calls')}}</h3>
{{--                                <a href="{{route('system.call.index',['client_id'=> $result->client_id,'sign_id'=>$result->id,'sign_type'=>'leads'])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand" title="{{__('Create Call')}}" data-placement="left">--}}
{{--                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Create Call')}}</span>--}}
{{--                                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>--}}
{{--                                </a>--}}
                                <a href="#" data-toggle="modal" data-target="#create-call-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Create Call')}}" data-placement="left">
{{--                                <a  href="javascript:void(0)" onclick="urlIframe('{{route('system.call.index',['client_id'=> $result->client_id,'sign_id'=>$result->id,'sign_type'=>'leads'])}}');" class="btn btn-sm btn-elevate btn-brand" title="{{__('Create Call')}}" data-placement="left">--}}
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Create Call')}}</span>
                                    <i class="flaticon-plus k-padding-l-5 k-padding-r-0"></i>
                                </a>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-call">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Client Name')}}</th>
                                    <th>{{__('Action')}}</th>
                                    <th>{{__('Call Status')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('Created By')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Client Name')}}</th>
                                    <th>{{__('Action')}}</th>
                                    <th>{{__('Call Status')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('Created By')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>



                    <!--end::Row-->
                </div>
                <div class="tab-pane fade" id="k_tabs_1_3" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Reminders')}}</h3>

                                <a href="{{route('system.calendar.index',['sign_type'=>'leads','sign_id'=>$result->id])}}" target="_blank" class="btn btn-sm btn-elevate btn-brand">
                                    <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Add')}}</span>
                                    <i class="flaticon2-plus-1 k-padding-l-5 k-padding-r-0"></i>
                                </a>
                            </div>

                        </div>
                        <div class="k-portlet__body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('By')}}</th>
                                    <th>{{__('Date & Time')}}</th>
                                    <th>{{__('Comment')}}</th>
                                    <th>{{__('Created At')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($result->reminders as $key => $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td>
                                            <a href="{{route('system.staff.show',$value->staff->id)}}" target="_blank">
                                                {{$value->staff->fullname}}
                                            </a>
                                        </td>
                                        <td>{{$value->date_time->format('Y-m-d h:i A')}}</td>
                                        <td>{{$value->comment}}</td>
                                        <td>{{$value->created_at->format('Y-m-d h:i A')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="k_tabs_1_4" role="tabpanel">
                    <div class="k-portlet k-portlet--height-fluid">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-label">
                                <h3 class="k-portlet__head-title">{{__('Log')}}</h3>
                            </div>
                        </div>
                        <div class="k-portlet__body">
                            <table style="text-align: center;" class="table table-striped- table-bordered table-hover table-checkable" id="datatable-log">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Staff')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Staff')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- end:: Content -->

<div class="modal fade" id="call-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="call-modal-title">{{__('Loading...')}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="call-modal-body">
                                            {{__('Loading...')}}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


        @endsection
        @section('footer')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
            <script type="text/javascript">
                noAjaxSelect2('.lead-status-select','{{__('Select Status')}}','{{App::getLocale()}}');
                $(document).ready(function(){
                    $('[data-toggle="tooltip"]').tooltip();
                });
                $datatableLog = $('#datatable-log').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "log";
                        }
                    }
                });

                $datatableCall = $('#datatable-call').DataTable({
                    "iDisplayLength": 25,
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": "{{url()->full()}}",
                        "type": "GET",
                        "data": function(data){
                            data.isDataTable = "call";
                        }
                    }
                });


                function changeLeadStatus($this) {
                    if(!$('#select_lead_status_id').val() || $('#select_lead_status_id').val() === '{{$result->lead_status_id}}'){
                        $('#select_lead_status_id').css('border-color','red');
                        $('#select_lead_status_id_error').text('{{__('Select Status')}}');
                        return;
                    }
                    $('#select_lead_status_id_error').text('');
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'changeLeadStatus',
                            'status_id': $('#select_lead_status_id').val(),
                            'lead_data_id': {{$result->id}},
                        },
                        function($data){
                             toastr.success('{{__('Status Changed')}}', '', {"closeButton": true});
                            setInterval(function(){ location.reload(); }, 1000); //location.reload();

                        }
                    );

                    $('#lead-status-modal').modal('hide');
                }






                function save_log_phone(){
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'saveLog',
                            'id': {{$result->id}},
                            'desc': 'Call on a number',
                            'model': "App\\Models\\LeadData"
                        },
                        function(){
                            // location.reload();
                            $url = '{{url()->full()}}?isDataTable=log';
                            $datatableLog.ajax.url($url).load();

                        }
                    );
                }

                function save_log_mail(){
                    $.get(
                        '{{route('system.misc.ajax')}}',
                        {
                            'type':'saveLog',
                            'id': {{$result->id}},
                            'desc': 'send E-mail',
                            'model': "App\\Models\\LeadData"
                        },
                        function(){
                            // location.reload();
                            $url = '{{url()->full()}}?isDataTable=log';
                            $datatableLog.ajax.url($url).load();

                        }
                    );
                }


                $('#create-call-modal').on('shown.bs.modal', function () {
                    ajaxSelect2('.client-select','investor-client');
                });

                function submitMainForm(){
                    formSubmit(
                        '{{route('system.call.store')}}',
                        $('#main-form').serialize(),
                        function ($data) {
                            $('#create-call-modal').modal('hide');
                            toastr.success('{{__('Action Added Successfully')}}', '', {"closeButton": true});
                            setInterval(function(){ location.reload(); }, 1000);
                            //loadCalls();
                            //showCall($data.data.id);
                            $url = '{{url()->full()}}?is_total=true&isDataTable=call';
                            $datatableCall.ajax.url($url).load();
                            $('#main-form')[0].reset();
                        },
                        function ($data){
                            $("#create-call-modal").animate({ scrollTop: 0 }, "fast");
                            pageAlert('#form-alert-message','error',$data.message);
                        }
                    );
                }

                function showCall($id){

                    $('#call-modal-title').text('{{__('#ID:')}} '+$id);
                    $('#call-modal-body').text('{{__('Loading...')}}');
                    $('#call-modal').modal('show');

                    $.get('{{route('system.call.index')}}/'+$id,function($data){
                        $('#call-modal-body').html($data);
                    });
                }




                $('#remind_me-form-input').change(function () {
                    if($(this).val() == 'yes'){
                        $('#remind_me_on_div').show();
                    }else{
                        $('#remind_me_on_div').hide();
                    }
                });


                $('.k_datetimepicker_1').datetimepicker({
                    todayHighlight: true,
                    autoclose: true,
                    format: 'yyyy-mm-dd hh:ii:ss'

                });

            </script>
@endsection