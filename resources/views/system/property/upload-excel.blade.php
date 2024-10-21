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
                <div class="k-portlet__body" style="background: #f7f7fb;">

                    {!! Form::open(['route' => 'system.property.upload-excel-store','files'=>true, 'method' => 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    {!! Form::hidden('key',$randKey) !!}

                    @if($importer_data)
                        {!! Form::hidden('importer_data_id',$importer_data->id) !!}
                    @endif

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>

                        <div class="form-group row">
                            <div class="col-md-8">
                                <label>{{__('Excel File')}}<span class="red-star">*</span></label>
                                {!! Form::file('excel_file',['class'=>'form-control','id'=>'excel_file-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="excel_file-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Ignore First Row')}}<span class="red-star">*</span></label>
                                {!! Form::select('ignore_first_row',['yes'=>__('Yes'),'no'=>__('No')],null,['class'=>'form-control','id'=>'ignore_first_row-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="ignore_first_row-form-error"></div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-md-6">
                                <label>{{__('Type')}}<span class="red-star">*</span></label>
{{--                                @php--}}
{{--                                    $typesData = [''=>__('Select Type')];--}}
{{--                                    foreach ($property_types as $key => $value){--}}
{{--                                        $typesData[$value->id] = $value->name;--}}
{{--                                    }--}}

{{--                                    if($importer_data){--}}
{{--                                        $typesDataValue = $importer_data->importer->property_type_id;--}}
{{--                                    }else{--}}
{{--                                        $typesDataValue = isset($result) ? $result->property_type_id: null;--}}
{{--                                    }--}}

{{--                                @endphp--}}
{{--                                {!! Form::select('property_type_id',$typesData,$typesDataValue,['class'=>'form-control','id'=>'property_type_id-form-input','onchange'=>'propertyType();','autocomplete'=>'off']) !!}--}}
                                {!! Form::text('property_type',null,['class'=>'form-control','id'=>'property_type-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="property_type-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label>{{__('Purpose')}}<span class="red-star">*</span></label>
{{--                                @php--}}
{{--                                    $purposesData = [''=>__('Select Purpose')];--}}
{{--                                    foreach ($purposes as $key => $value){--}}
{{--                                        $purposesData[$value->id] = $value->name;--}}
{{--                                    }--}}

{{--                                    if($importer_data){--}}
{{--                                        $purposesDataValue = $importer_data->importer->purpose_id;--}}
{{--                                    }else{--}}
{{--                                        $purposesDataValue = isset($result) ? $result->purpose_id: null;--}}
{{--                                    }--}}
{{--                                @endphp--}}
{{--                                {!! Form::select('purpose_id',$purposesData,$purposesDataValue,['class'=>'form-control','id'=>'purpose_id-form-input','autocomplete'=>'off']) !!}--}}
                                {!! Form::text('purpose_id',null,['class'=>'form-control','id'=>'purpose_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="purpose_id-form-error"></div>
                            </div>

                        </div>
                        <div class="form-group row">

                            <div class="col-md-12">
                                <label>{{__('Area')}}<span class="red-star">*</span></label>
                                @php
                                    $areaViewSelect = [''=> __('Select Area')];
                                    if(isset($result)){
                                        $areaViewSelect[$result->area_id] = implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true) );
                                    }

                                    if($importer_data){
                                        $areaValue = $importer_data->importer->area_id;
                                        $areaViewSelect[$areaValue] = implode(' -> ',\App\Libs\AreasData::getAreasUp($areaValue,true) );
                                    }else{
                                        $areaValue = isset($result) ? $result->area_id: null;
                                    }
                                @endphp
                                {!! Form::select('area_id',$areaViewSelect,$areaValue,['class'=>'form-control area-select','id'=>'area_id-form-input','onchange'=>'changeAreaEvent($(this).val())','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="area_id-form-error"></div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Building Number')}}</label>
                                {!! Form::text('building_number',null,['class'=>'form-control','id'=>'building_number-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="building_number-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Flat Number')}}</label>
                                {!! Form::text('flat_number',null,['class'=>'form-control','id'=>'flat_number-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="flat_number-form-error"></div>
                            </div>
                        </div>

                    </div>



                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

                        <div class="form-group row">



                            <div class="col-md-4">
                                <label>{{__('Client Type')}}<span class="red-star">*</span></label>
                                {!! Form::select('client_type',['client'=>__('Client'),'investor'=>__('Investor')],null,['class'=>'form-control','id'=>'client_type-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_type-form-error"></div>
                            </div>


                            <div class="col-md-4">
                                <label>{{__('Client Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('client_name',null,['class'=>'form-control','id'=>'client_name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_name-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Client Mobile')}}<span class="red-star">*</span></label>
                                {!! Form::text('client_mobile',null,['class'=>'form-control','id'=>'client_mobile-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="client_mobile-form-error"></div>
                            </div>
                        </div>


                        <div id="investor-div" style="display: none;">

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label>{{__('Investor Type')}}<span class="red-star">*</span></label>
                                    {!! Form::select('client_investor_type',[''=>__('Select Investor Type'),'individual'=>__('Individual'),'company'=>__('Company'),'broker'=>__('Broker')],null,['class'=>'form-control','id'=>'client_investor_type-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="client_investor_type-form-error"></div>
                                </div>
                            </div>

                            <div class="form-group row" id="company-name-div" style="display: none;">
                                <div class="col-md-12">
                                    <label>{{__('Company Name')}}</label>
                                    {!! Form::text('client_company_name',null,['class'=>'form-control','id'=>'client_company_name-form-input','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="client_company_name-form-error"></div>
                                </div>
                            </div>

                        </div>


                    </div>


                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">

                        <div class="form-group row">


                            <div class="col-md-12">
                                <label>{{__('Model')}}</label>
                                {!! Form::text('model',null,['class'=>'form-control','id'=>'model-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="model-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Name')}}</label>
                                {!! Form::text('name',null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Description')}}</label>
                                {!! Form::text('description',null,['class'=>'form-control','id'=>'description-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="description-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Remarks')}}</label>
                                {!! Form::text('remarks',null,['class'=>'form-control','id'=>'remarks-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="remarks-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>{{__('Payment Type')}}<span class="red-star">*</span></label>
                                {!! Form::text('payment_type',null,['class'=>'form-control','id'=>'payment_type-form-input','autocomplete'=>'off']) !!}
{{--                                {!! Form::select('payment_type',['cash'=> __('Cash'),'installment'=> __('Installment'),'cash_installment'=> __('Cash or Installment')],isset($result) ? $result->payment_type: null,['class'=>'form-control','id'=>'payment_type-form-input','onchange'=>'paymentType();','autocomplete'=>'off']) !!}--}}
                                <div class="invalid-feedback" id="payment_type-form-error"></div>
                            </div>


                            <div class="col-md-4">
                                <label>{{__('Price')}}<span class="red-star">*</span></label>
                                {!! Form::text('price',null,['class'=>'form-control','id'=>'price-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="price-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Currency')}}<span class="red-star">*</span></label>
                                {!! Form::select('currency',['EGP'=>__('EGP'),'USD'=>__('USD')],null,['class'=>'form-control','id'=>'currency-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="currency-form-error"></div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Negotiable')}}<span class="red-star">*</span></label>
                                {!! Form::select('negotiable',['yes'=>__('Yes'),'no'=>__('No')],null,['class'=>'form-control','id'=>'negotiable-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="negotiable-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row" id="deposit_div">
                            <div class="col-md-6">
                                <label>{{__('Years Of Installment')}}</label>
                                {!! Form::text('years_of_installment',null,['class'=>'form-control','id'=>'years_of_installment-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="years_of_installment-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label>{{__('Deposit')}}</label>
                                {!! Form::text('deposit',null,['class'=>'form-control','id'=>'deposit-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="deposit-form-error"></div>
                            </div>

                        </div>

                        <div class="form-group row">

                            <div class="col-md-6">
                                <label>{{__('Space')}}<span class="red-star">*</span></label>
                                {!! Form::text('space',null,['class'=>'form-control','id'=>'space-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="space-form-error"></div>
                            </div>

                            <div class="col-md-6">
                                <label>{{__('Address')}}<span class="red-star">*</span></label>
                                {!! Form::text('address',null,['class'=>'form-control','id'=>'address-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="address-form-error"></div>
                            </div>

                        </div>

                    </div>

{{--                    <div id="parameters-html" style="display:none;"></div>--}}


                    <div class="k-portlet k-portlet--tabs">
                        <div class="k-portlet__head">
                            <div class="k-portlet__head-toolbar">
                                <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
                                    @foreach($propertyTypes as $key => $value)
                                        <li class="nav-item">
                                            <a class="nav-link @if($key == 0) active @endif" data-toggle="tab" href="#k_portlet_base_demo_2_{{$key}}_tab_content" role="tab">
                                                {{$value->property_type->{'name_'.\App::getLocale()} }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="k-portlet__body">
                            <div class="tab-content">
                                @foreach($propertyTypes as $key => $value)
                                    <div class="tab-pane @if($key == 0) active @endif" id="k_portlet_base_demo_2_{{$key}}_tab_content" role="tabpanel">
                                        @foreach($parameters[$key] as $pKey => $pValue)
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <label>{{$pValue->{'name_'.App::getLocale()} }}</label>
                                                        {!! Form::text('p_'.$pValue->column_name,!empty($pValue->default_value) ? $pValue->default_value: null,['class'=>'form-control','id'=>'p_'.$pValue->column_name.'-form-input','autocomplete'=>'off']) !!}
                                                        <div class="invalid-feedback" id="p_{{$pValue->column_name}}-form-error"></div>
                                                    </div>
                                                </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>




                    <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">


                        <div class="form-group row">

                            <div class="col-md-4">
                                <label>{{__('Status')}}<span class="red-star">*</span></label>
                                @php
                                    $propertyStatusData = [''=>__('Select Status')];
                                    foreach ($property_status as $key => $value){
                                        $propertyStatusData[$value->id] = $value->name;
                                    }
                                @endphp
                                {!! Form::select('property_status_id',$propertyStatusData,isset($result) ? $result->property_status_id: setting('default_property_status'),['class'=>'form-control','id'=>'property_status_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="property_status_id-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Sales')}}<span class="red-star">*</span></label>
                                @php
                                    $salesViewSelect = [''=> __('Select Sales')];
                                    $salesViewSelect = $salesViewSelect+array_column(getSales()->toArray(),'name','id');
                                @endphp
                                {!! Form::select('sales_id',$salesViewSelect,isset($result) ? $result->sales_id: null,['class'=>'form-control','id'=>'sales_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="sales_id-form-error"></div>
                            </div>

                            <div class="col-md-4">
                                <label>{{__('Data Source')}}<span class="red-star">*</span></label>
                                @php
                                    $dataSourcesData = [''=>__('Select Data Source')];
                                    foreach ($data_sources as $key => $value){
                                        $dataSourcesData[$value->id] = $value->name;
                                    }

                                    if($importer_data){
                                        if($importer_data->importer->connector == 'OLX'){
                                            $dataSourceValue = setting('olx_data_data_source_id');
                                        }elseif($importer_data->importer->connector == 'Aqarmap'){
                                            $dataSourceValue = setting('aqarmap_data_data_source_id');
                                        }

                                    }else{
                                        $dataSourceValue = isset($result) ? $result->data_source_id: setting('default_data_source_id');
                                    }
                                @endphp
                                {!! Form::select('data_source_id',$dataSourcesData,$dataSourceValue,['class'=>'form-control','id'=>'data_source_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="data_source_id-form-error"></div>
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
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
            <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
            <script src="{{asset('assets/uploader/jquery.fileuploader.min.js')}}" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>

            <script type="text/javascript">

                ajaxSelect2('.client-select','investor');
                ajaxSelect2('.sales-select','sales');
                ajaxSelect2('.area-select','area',1);

                function submitMainForm(){
                    formSubmit(
                        '{{route('system.property.upload-excel-store')}}',
                        new FormData($('#main-form')[0]),
                        function ($data) {
                            if(!$data.status){
                                $("html, body").animate({ scrollTop: 0 }, "fast");
                                if($data.code == 11000){
                                    $errorLins = '<ul>';
                                    $.each($data.data,function ($key,$value) {
                                        $errorLins+= '<li> {{__('Row')}}: '+$key;
                                        $errorLins+= '<ul>';
                                        $.each($value,function($eKey,$eValue){
                                            $errorLins+= '<li>'+$eValue+'</li>';
                                        });
                                        $errorLins+= '</ul>';
                                        $errorLins+= '</li>';
                                    });
                                    $errorLins+= '</ul>';
                                    pageAlert('#form-alert-message','error',$data.message+'<br />'+$errorLins);
                                }else{
                                    pageAlert('#form-alert-message','error',$data.message);
                                }
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
                function paymentType(){
                    if($('#payment_type-form-input').val() == 'cash'){
                        $('#price_div').attr('class','col-md-6');
                        $('#deposit_div').hide();
                    }else{
                        $('#price_div').attr('class','col-md-3');
                        $('#deposit_div').show();
                    }
                }
                function propertyType(){
                    $('#parameters-html').hide();
                    addLoading();
                    $.get('{{route('system.misc.ajax')}}',{
                        'type':'parameters-form',
                        'property_type_id':$('#property_type_id-form-input').val(),
                        @if(isset($result)) 'property_id': '{{$result->id}}', @endif
                                @if($importer_data)
                                @foreach(explode(',',setting('bed_rooms_names')) as $key => $value)
                        'p_{{$value}}': '{{$importer_data->bed_rooms}}',
                        @endforeach
                                @foreach(explode(',',setting('bath_room_names')) as $key => $value)
                        'p_{{$value}}': '{{$importer_data->bath_room}}',
                        @endforeach
                        @endif
                    },function($data){
                        removeLoading();
                        if($data == false) return false;
                        $('#parameters-html').show().html($data);
                        $('.multiple-select2').select2();
                    });
                }

                $(document).ready(function(){
                    paymentType();
                    propertyType();
                    $('.multiple-select2').select2();

                });

                window.closeModal = function(){
                    $('#modal-iframe').modal('hide');
                };

                $(document).ready(function() {
                    changeStatus();
                    // enable fileupload plugin
                    $('#images-form-input').fileuploader({
                        onSelect: function(item) {
                            // if (!item.html.find('.fileuploader-action-start').length)
                            //     item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-start" title="Upload"><i></i></a>');
                        },
                        upload: {
                            url: '{{route('system.property.image-upload')}}',
                            data: {
                                '_token': '{{csrf_token()}}',
                                'key':  '{{$randKey}}'
                            },
                            type: 'POST',
                            enctype: 'multipart/form-data',
                            start: true,
                            synchron: true,
                            onSuccess: function(result, item) {

                                console.log(result);

                                item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
                            },
                            onError: function(item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {

                                // console.log(jqXHR);


                                item.upload.status != 'cancelled' && item.html.find('.fileuploader-action-retry').length == 0 ? item.html.find('.column-actions').prepend(
                                    '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
                                ) : null;
                            },
                            onComplete: null,
                        },
                        onRemove: function(item) {
                            // send POST request
                            $.post('{{route('system.property.remove-image')}}', {
                                'name': item.name,
                                '_token': '{{csrf_token()}}',
                                'key':  '{{$randKey}}'
                            });
                        }
                    });

                });

                $('#property_status_id-form-input').change(function(){
                    changeStatus();
                });

                function changeStatus(){
                    $value = $('#property_status_id-form-input').val();
                    if(in_array($value,[{{setting('archive_property_status')}}])){
                        $('#hold_until_div').show();
                    }else{
                        $('#hold_until_div').hide();
                    }
                }

                function getPropertyModelSpace($value) {
                    $mainData = new Array;
                    @foreach($property_model as $key => $value)
                        $mainData[{{$value->id}}] = '{{$value->space}}';
                    @endforeach

                    if(isset($mainData[$value])){
                        $('#space-form-input').val($mainData[$value]);
                    }
                }


                function changeAreaEvent($value){
                    // MODEL MODEL MODEL MODEL
                    $mainData = new Array;
                    @foreach(explode(',',setting('show_model_in_area_ids')) as $key => $value)
                        $mainData[{{$value}}] = '{{$value}}';
                    @endforeach

                    if(isset($mainData[$value])){
                        $('#property_model_div').show();
                    }else{
                        $('#property_model_div').hide();
                        $('#property_model_id-form-input').val('');
                    }
                    // MODEL MODEL MODEL MODEL
                }


                function investorDivEvent(){
                    if($('#client_type-form-input').val() == 'investor'){
                        $('#investor-div').show();
                    }else{
                        $('#investor-div').hide();
                    }
                }

                function companyNameDivEvent(){
                    if($('#client_investor_type-form-input').val() == 'company' || $('#client_investor_type-form-input').val() == 'broker'){
                        $('#company-name-div').show();
                    }else{
                        $('#company-name-div').hide();
                    }
                }


                $(document).ready(function(){
                    investorDivEvent();
                    companyNameDivEvent();
                });



                $('#client_type-form-input').change(function(){
                    investorDivEvent();
                });


                $('#client_investor_type-form-input').change(function(){
                    companyNameDivEvent();
                });



            </script>
        @endsection
        @section('header')
            <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />
            <link href="{{asset('assets/uploader/font/font-fileuploader.css')}}" rel="stylesheet">
            <link href="{{asset('assets/uploader/jquery.fileuploader.min.css')}}" media="all" rel="stylesheet">
@endsection