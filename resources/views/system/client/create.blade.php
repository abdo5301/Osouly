
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
        <div class="k-portlet k-portlet--mobile">
            <div class="k-portlet__body"  style="background: #f7f7fb;">


                {!! Form::open(['route' => isset($result) ? ['system.'.$type.'.update',$result->id]:'system.'.$type.'.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','enctype'=>"multipart/form-data",'id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body" style="background: #FFF;">
                        <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Main Data')}}</h3>
                        <div id="form-alert-message"></div>
                        @if(request('type') == 'owner')
                            {!! Form::hidden('type','owner',['id'=>'type-form-input','autocomplete'=>'off']) !!}
                        @elseif(request('type') == 'renter')
                            {!! Form::hidden('type','renter',['id'=>'type-form-input','autocomplete'=>'off']) !!}
                        @else
                            {{--<div class="form-group row">--}}
                                {{--<div class="col-md-12">--}}
                                    {{--<label>{{__('Type')}}<span class="red-star">*</span></label>--}}
                                    {{--{!! Form::select('type',['owner'=>__('Owner'),'renter'=>__('Renter')],isset($result) ? $result->type : null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}--}}
                                    {{--<div class="invalid-feedback" id="type-form-error"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {!! Form::hidden('type',$type,['id'=>'type-form-input','autocomplete'=>'off']) !!}
                        @endif

                        <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('First Name')}}<span class="red-star">*</span></label>
                                    {!! Form::text('first_name',isset($result) ? $result->first_name : null,['class'=>'form-control','id'=>'first_name-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="first_name-form-error"></div>
                                </div>
                            <div class="col-md-6">
                                <label>{{__('Second Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('second_name',isset($result) ? $result->second_name : null,['class'=>'form-control','id'=>'second_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="second_name-form-error"></div>
                            </div>
                            </div>

                            <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Third Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('third_name',isset($result) ? $result->third_name : null,['class'=>'form-control','id'=>'third_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="third_name-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Last Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('last_name',isset($result) ? $result->last_name : null,['class'=>'form-control','id'=>'last_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="last_name-form-error"></div>
                            </div>
                            </div>


                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Gender')}}<span class="red-star">*</span></label>
                                {!! Form::select('gender',['male'=>__('Male'),'female'=>__('Female')],isset($result) ? $result->gender : null,['class'=>'form-control','id'=>'gender-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="gender-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Birth Date')}}</label>
                                {!! Form::text('birth_date',(isset($result) && !empty($result->birth_date) && $result->birth_date != '0000-00-00') ? date('Y-m-d',strtotime($result->birth_date)) : null,['class'=>'form-control k_datepicker_1','id'=>'birth_date-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="birth_date-form-error"></div>
                            </div>

                        </div>


                        {{--<div class="form-group row" id="more-option-button">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<button type="button" onclick="$('#more-option-button').hide();$('#option-div').show();" class="btn btn-success col-md-12">{{__('More Option')}}</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Mobile')}}<span class="red-star">*</span></label>
                                    {!! Form::text('mobile',isset($result) ? $result->mobile : null,['class'=>'form-control','id'=>'mobile-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="mobile-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('ID Number')}}</label>
                                    {!! Form::text('id_number',isset($result) ? $result->id_number : null,['class'=>'form-control','id'=>'id_number-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="id_number-form-error"></div>
                                </div>

                            </div>



                        <div class="form-group row">
                            <div class="col-md-3">
                                <label>{{__('Bank Code')}}</label>
                                {!! Form::text('bank_code',isset($result) ? $result->bank_code : null,['class'=>'form-control','id'=>'bank_code-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="bank_code-form-error"></div>
                            </div>
                            <div class="col-md-3">
                                <label>{{__('Branch Code')}}</label>
                                {!! Form::text('branch_code',isset($result) ? $result->branch_code : null,['class'=>'form-control','id'=>'branch_code-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="branch_code-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Bank Account Number')}}</label>
                                {!! Form::text('bank_account_number',isset($result) ? $result->bank_account_number : null,['class'=>'form-control','id'=>'bank_account_number-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="bank_account_number-form-error"></div>
                            </div>
                        </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Area')}}</label>
                                    @php
                                        $areaViewSelect = [''=> __('Select Area')];
                                        if(isset($result)){
                                            $areaViewSelect[$result->area_id] = implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true) );
                                        }
                                        $areaValue = isset($result) ? $result->area_id: null;
                                    @endphp
                                    {!! Form::select('area_id',$areaViewSelect,$areaValue,['class'=>'form-control area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="area_id-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Address')}}</label>
                                    {!! Form::textarea('address',isset($result) ? $result->address : null,['class'=>'form-control','rows'=>'1','id'=>'address-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="address-form-error"></div>
                                </div>

                                </div>



                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Email')}}</label>
                                    {!! Form::email('email',isset($result) ? $result->email : null,['class'=>'form-control','id'=>'email-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="email-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Phone')}}</label>
                                    {!! Form::text('phone',isset($result) ? $result->phone : null,['class'=>'form-control','id'=>'phone-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="phone-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Password')}} @if(!isset($result)) <span class="red-star">*</span>@endif</label>
                                    {!! Form::password('password',['class'=>'form-control','id'=>'password-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="password-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Confirm password')}} @if(!isset($result)) <span class="red-star">*</span> @endif</label>
                                    {!! Form::password('password_confirmation',['class'=>'form-control','id'=>'password_confirmation-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="password_confirmation-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label>{{__('Status')}}</label>
                                    {!! Form::select('status',['pending'=>__('Pending'),'active'=> __('Active'),'in-active'=> __('In-Active')],isset($result) ? $result->status : null,['class'=>'form-control','id'=>'status-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="status-form-error"></div>
                                </div>
                                <div class="col-md-6">
                                    <label>{{__('Description')}}</label>
                                    {!! Form::textarea('description',isset($result) ? $result->description : null,['class'=>'form-control','id'=>'description-form-input','rows'=>'1','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="description-form-error"></div>
                                </div>
                            </div>
                    </div>


                        <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                            <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Files')}}</h3>
                            <div class="form-group row">
                                <div class="col-md-2 col-image-upload">
                                    <h6 class="image-upload-title"> {{__('Personal Photo')}} <span class="red-star">*</span></h6>
                                    <label class="image-upload">
                                        <i class="flaticon2-plus image-upload-icon"></i>
                                        <img class="image-upload-src" @if(isset($result) && !empty($images) && !empty($images['personal_photo'])) style="display:block;" src="{{$images['personal_photo']}}" @endif >
                                        {!! Form::file('personal_photo',['class'=>'form-control','style'=>'display:none;','id'=>'personal_photo-form-input','autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="personal_photo-form-error"></div>
                                    </label>
                                </div>

                                <div class="col-md-2 col-image-upload">
                                   <h6 class="image-upload-title"> {{__('Card Face')}} <span class="red-star">*</span></h6>
                                    <label class="image-upload">
                                        <i class="flaticon2-plus image-upload-icon"></i>
                                        <img class="image-upload-src"  @if(isset($result) && !empty($images) && !empty($images['card_face'])) style="display:block;" src="{{$images['card_face']}}" @endif >
                                    {!! Form::file('card_face',['class'=>'form-control','style'=>'display:none;','id'=>'card_face-form-input']) !!}
                                        <div class="invalid-feedback" id="card_face-form-error"></div>
                                    </label>

                                </div>

                                <div class="col-md-2 col-image-upload">
                                    <h6 class="image-upload-title"> {{__('Card Back')}} <span class="red-star">*</span></h6>
                                    <label class="image-upload">
                                        <i class="flaticon2-plus image-upload-icon"></i>
                                        <img class="image-upload-src" @if(isset($result) && !empty($images) && !empty($images['card_back'])) style="display:block;" src="{{$images['card_back']}}" @endif >
                                        {!! Form::file('card_back',['class'=>'form-control','style'=>'display:none;','id'=>'card_back-form-input','autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="card_back-form-error"></div>
                                    </label>
                                </div>

                                <div class="col-md-2 col-image-upload">
                                    <h6 class="image-upload-title"> {{__('Criminal record')}} </h6>
                                    <label class="image-upload">
                                        <i class="flaticon2-plus image-upload-icon"></i>
                                        <img class="image-upload-src"  @if(isset($result) && !empty($images) && !empty($images['criminal_record'])) style="display:block;" src="{{$images['criminal_record']}}" @endif >
                                        {!! Form::file('criminal_record',['class'=>'form-control','style'=>'display:none;','id'=>'criminal_record-form-input','autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="criminal_record-form-error"></div>
                                    </label>
                                </div>

                                <div class="col-md-2 col-image-upload">
                                    <h6 class="image-upload-title"> {{__('Passport')}} </h6>
                                    <label class="image-upload">
                                        <i class="flaticon2-plus image-upload-icon"></i>
                                        <img class="image-upload-src" @if(isset($result) && !empty($images) && !empty($images['passport'])) style="display:block;" src="{{$images['passport']}}" @endif >
                                        {!! Form::file('passport',['class'=>'form-control','style'=>'display:none;','id'=>'passport-form-input','autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="passport-form-error"></div>
                                    </label>
                                </div>

                            </div>

                            {{--<div class="form-group row">--}}
                                {{--<div class="col-md-3">--}}
                                    {{--<label>{{__('File Name En')}}<span class="red-star">*</span></label>--}}
                                    {{--<input class="form-control" type="text">--}}
                                    {{--<div class="invalid-feedback" id="job_title-form-error"></div>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-3">--}}
                                    {{--<label>{{__('File Name Ar')}}<span class="red-star">*</span></label>--}}
                                    {{--<input class="form-control" type="text">--}}
                                    {{--<div class="invalid-feedback" id="job_title-form-error"></div>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-3">--}}
                                    {{--<label>{{__('File')}}<span class="red-star">*</span></label>--}}
                                    {{--<input class="form-control" type="file">--}}
                                    {{--<div class="invalid-feedback" id="job_title-form-error"></div>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-1">--}}
                                    {{--<label style="color: #FFF;">-</label>--}}
                                    {{--<a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">--}}
                                        {{--<i class="flaticon2-delete form-control" style="color: red;border: 0;"></i>--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<div class="form-group row">--}}
                                {{--<div class="col-md-2 col-image-upload">--}}
                                    {{--<a href="javascript:void(0);" onclick="addMultiRowFile();">--}}
                                        {{--<i class="flaticon2-file"></i>--}}
                                        {{--{{__('Add File')}}--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                        </div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <div class="jobs-data-container" @if($type == 'owner') style="display: none;" @endif>
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Other items')}}</h3>
                    <h5 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Previous jobs')}}</h5>

                    <div class="jobs-data">
                        @if(isset($result) && !empty($client_jobs))
                            @foreach($client_jobs as $key => $value)
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <label>{{__('Job Title')}}<span class="red-star">*</span></label>
                                        {!! Form::text('job_title[]',$value['job_title'],['class'=>'form-control','id'=>'job_title-form-input','autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="job_title-form-error"></div>
                                    </div>

                                    <div class="col-md-2">
                                        <label>{{__('Company Name')}}</label>
                                        {!! Form::text('company_name[]',$value['company_name'],['class'=>'form-control','autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="job_title-form-error"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{__('From')}}<span class="red-star">*</span></label>
                                        {!! Form::date('from_date[]',$value['from_date'],['class'=>'form-control','placeholder'=> __('From'),'autocomplete'=>'off']) !!}
                                        <div class="invalid-feedback" id="from_date-form-error"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{__('To')}}<span class="red-star">*</span></label>
                                        {!! Form::date('to_date[]',$value['to_date'],['class'=>'form-control','placeholder'=> __('To'),'autocomplete'=>'off']) !!}
                                    </div>

                                    <div class="col-md-2">
                                        <label></label>
                                        <div class="k-radio-inline">
                                            <label class="k-radio">
                                                <input type="radio" @if($value['present'] == 'yes') checked @endif name="pre" value="yes"> {{__('Present Job')}}
                                                <input type="hidden" value="{{$value['present']}}" class="present-value" name="present[]">
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>


                                    <div class="col-md-1">
                                        <label style="color: #FFF;">-</label>
                                        <a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">
                                            <i class="flaticon2-delete form-control" style="color: red;border: 0;"></i>
                                        </a>
                                    </div>

                                </div>
                             @endforeach
                         @else
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label>{{__('Job Title')}}<span class="red-star">*</span></label>
                            {!! Form::text('job_title[]',null,['class'=>'form-control','id'=>'job_title-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="job_title-form-error"></div>
                        </div>

                        <div class="col-md-2">
                            <label>{{__('Company Name')}}</label>
                            {!! Form::text('company_name[]',null,['class'=>'form-control','id'=>'job_title-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="job_title-form-error"></div>
                        </div>
                        <div class="col-md-2">
                        <label>{{__('From')}}<span class="red-star">*</span></label>
                            {!! Form::date('from_date[]',null,['class'=>'form-control','placeholder'=> __('From'),'autocomplete'=>'off']) !!}
                        </div>
                        <div class="col-md-2">
                            <label>{{__('To')}}<span class="red-star">*</span></label>
                            {!! Form::date('to_date[]',null,['class'=>'form-control','placeholder'=> __('To'),'autocomplete'=>'off']) !!}
                        </div>

                        <div class="col-md-2">
                            <label></label>
                            <div class="k-radio-inline">
                                    <label class="k-radio">
                                            <input type="radio" name="pre" value="yes"> {{__('Present Job')}}
                                            <input type="hidden" class="present-value" name="present[]" value="no">
                                        <span></span>
                                    </label>
                            </div>
                            <div class="invalid-feedback" id="present-form-error"></div>
                        </div>

                        <div class="col-md-1">
                            <label style="color: #FFF;">-</label>
                            <a href="javascript:void(0);" onclick="removeMultiRowParameter($(this));">
                                <i class="flaticon2-delete form-control" style="color: red;border: 0;"></i>
                            </a>
                        </div>

                    </div>
                        @endif
                    </div>


                    <div class="form-group row">
                        <div class="col-md-12" style="text-align: right;">
                            <a href="javascript:void(0);" onclick="addMultiRowJob();">
                                <i class="flaticon2-add"></i>
                                {{__('Add Job')}}
                            </a>
                        </div>
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



            </div>
                {!! Form::close() !!}
        </div>
    </div>

    <!-- end:: Content Body -->
</div>

<!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/uploader/jquery.fileuploader.min.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
    <script type="text/javascript">
        simpleAjaxSelect2('.area-select','area',1,'{{__('Select Area')}}');

        $('body').on('change','input:file', function () {
            let input = this;
            input = $(this);
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    input.parent().find('.image-upload-src').css('display','block');
                    input.parent().find('.image-upload-src').attr('src',e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });


        $('body').on('change', 'input:radio[name="pre"]', function () {
                $('.present-value').val('no');
                if (this.checked) {
                    $(this).parent().find('.present-value').val('yes');
                }
            });

        function addMultiRowJob(){
            $data = "<div class=\"form-group row\">\n" +
                "                        <div class=\"col-md-3\">\n" +
                "                            <label>{{__('Job Title')}}<span class=\"red-star\">*</span></label>\n" +
                "                            <input class=\"form-control\" autocomplete=\"off\" id=\"job_title-form-input\" name=\"job_title[]\" type=\"text\">\n" +
                "                            <div class=\"invalid-feedback\" id=\"job_title-form-error\"></div>\n" +
                "                        </div>\n" +
                "\n" +
                "                        <div class=\"col-md-2\">\n" +
                "                            <label>{{__('Company Name')}}</label>\n" +
                "                            <input class=\"form-control\"  autocomplete=\"off\" name=\"company_name[]\" type=\"text\">\n" +
                "                            <div class=\"invalid-feedback\" ></div>\n" +
                "                        </div>\n" +
                "                        <div class=\"col-md-2\">\n" +
                "                        <label>{{__('From')}}<span class=\"red-star\">*</span></label>\n" +
                "                            <input class=\"form-control\" placeholder=\"من\" autocomplete=\"off\" name=\"from_date[]\" type=\"date\">\n" +
                "                        </div>\n" +
                "                        <div class=\"col-md-2\">\n" +
                "                            <label>{{__('To')}}<span class=\"red-star\">*</span></label>\n" +
                "                            <input class=\"form-control\" placeholder=\"الى\" autocomplete=\"off\" name=\"to_date[]\" type=\"date\">\n" +
                "                        </div>\n" +
                "\n" +
                "                        <div class=\"col-md-2\">\n" +
                "                            <label></label>\n" +
                "                            <div class=\"k-radio-inline\">\n" +
                "                                    <label class=\"k-radio\">\n" +
                "                                            <input type=\"radio\" name=\"pre\" value=\"yes\"> {{__('Present Job')}}\n" +
                "<input type=\"hidden\" value=\"no\"  class=\"present-value\" name=\"present[]\">                                        " +
                "<span></span>\n" +
                "                                    </label>\n" +
                "                            </div>\n" +
                "                            <div class=\"invalid-feedback\" id=\"present-form-error\"></div>\n" +
                "                        </div>\n" +
                "\n" +
                "                        <div class=\"col-md-1\">\n" +
                "                            <label style=\"color: #FFF;\">-</label>\n" +
                "                            <a href=\"javascript:void(0);\" onclick=\"removeMultiRowParameter($(this));\">\n" +
                "                                <i class=\"flaticon2-delete form-control\" style=\"color: red;border: 0;\"></i>\n" +
                "                            </a>\n" +
                "                        </div>\n" +
                "\n" +
                "                    </div>";


            $('.jobs-data').append($data);
        }


        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.'.$type.'.update',$result->id):route('system.'.$type.'.store')}}',
                formData,
                function ($data) {
                   //console.log($data.data.url);
                    @if(request('addClientFromProperty'))
                        $newHTML = "<div class=\"col-md-12\">\n" +
                        "<label>{{__('Client')}}<span class="red-star">*</span></label>\n" +
                        "<input type=\"text\" class=\"form-control\" disabled=\"disabled\" value=\""+$data.data.name+"\" />"+
                        "<input type=\"hidden\" name=\"client_id\" value=\""+$data.data.id+"\" />"+
                        "<div class=\"invalid-feedback\" id=\"client_id-form-error\"></div>\n" +
                        "</div>";
                    $("#client-select-information", parent.document.body).html($newHTML);
                    window.parent.closeModal();
                    @elseif(request('addClientFromCall'))
                        $newHTML = "<div class=\"col-md-12\">\n" +
                        "<label>{{__('Client')}}<span class="red-star">*</span></label>\n" +
                        "<input type=\"text\" class=\"form-control\" disabled=\"disabled\" value=\""+$data.data.name+"\" />"+
                        "<input type=\"hidden\" name=\"client_id\" value=\""+$data.data.id+"\" />"+
                        "<div class=\"invalid-feedback\" id=\"client_id-form-error\"></div>\n" +
                        "</div>";
                    $("#client-select-information", parent.document.body).html($newHTML);
                    window.parent.closeModal();
                    @else
                        window.location = $data.data.url;
                    @endif


                },
                function ($data){
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                    pageAlert('#form-alert-message','error',$data.message);
                }
            );
        }



    </script>
@endsection

@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/uploader/font/font-fileuploader.css')}}" rel="stylesheet">
    <link href="{{asset('assets/uploader/jquery.fileuploader.min.css')}}" media="all" rel="stylesheet">

    <style>
        .select2-container--default .select2-selection--single .select2-selection__clear{
            font-size: large;
            color: red;
        @if( App::getLocale() !== 'ar')
/*margin-right: -12px;*/
            float: right !important;
            @else
margin-left: -12px;
        @endif
}

        .form-control-sm {
            min-width:100px;
        }


        .select2-container--default .select2-selection--multiple .select2-selection__clear{
            display: none;
        }

        .select2-selection {
            display: inline-table;
            width: 100%;
        }

        .select2-selection__arrow{
            @if( App::getLocale() !== 'ar')
left: auto !important;
            right: 1px !important;
        @endif
}

        .select2-search__field{
            @if( App::getLocale() !== 'ar')
direction: ltr;
        @endif
}

        .image-upload{
            width: 90px;
            height: 90px;
            border: solid 1px #E8EBE8;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            z-index: 9999;
        }

        .col-image-upload{
            @if( App::getLocale() !== 'ar')
             margin-right: -40px;
            @else
             margin-left: -40px;
            @endif
        }

        .image-upload-icon{
            position: absolute;
            margin-top: 24px;
            opacity: 0.4;
            border: 1px solid #ddd;
            border-radius: 50%;
            padding: 10px;
            font-size: 12px;
            @if( App::getLocale() !== 'ar')
            margin-left: -18px;
            @else
            margin-right: -18px;
            @endif
}

        .image-upload-src{
            width: 100%;
            height: 100%;
            display: none;
            border-radius: 5px;
        }

        .image-upload-title{
            padding-right: 4px;
        }

    </style>
@endsection


