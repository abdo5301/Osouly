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

                {!! Form::open(['route' => isset($result) ? ['system.property.update',$result->id]:'system.property.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                {!! Form::hidden('key',$randKey) !!}

                @if($importer_data)
                    {!! Form::hidden('importer_data_id',$importer_data->id) !!}
                @endif

                <div class="k-portlet__body" style="background: #FFF;">
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Main Data')}}</h3>

                        <div id="form-alert-message"></div>

                    <div class="form-group row" id="client-select-information">

                        @if($importer_data)
                            <div class="col-md-12">
                                <label>{{__('Owner')}}<span class="red-star">*</span></label>
                                <input type="text" class="form-control" disabled="disabled" value="{{$importer_data->owner_name}}" />
                                <div class="invalid-feedback" id="client_id-form-error"></div>
                            </div>
                        @else

                            <div class="col-md-10">
                                <label>{{__('Owner')}}<span class="red-star">*</span></label>
                                @php
                                    $ownerViewSelect = [''=> __('Select Owner')];
                                    if(isset($result)){
                                        $ownerViewSelect[$result->owner_id] = $result->owner->Fullname;
                                    }
                                @endphp
                                {!! Form::select('owner_id',$ownerViewSelect,isset($result) ? $result->owner_id: null,['class'=>'form-control client-select','id'=>'owner_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="owner_id-form-error"></div>
                            </div>
                            <div class="col-md-2">
                                <label style="color: #FFF;">*</label>
                                <a style="background: aliceblue; text-align: center;" href="javascript:void(0)" onclick="urlIframe('{{route('system.owner.create',['addClientFromProperty'=>'true','type'=>'owner'])}}');" class="form-control">
                                    <i class="la la-plus"></i>
                                </a>
                            </div>

                        @endif
                    </div>
                    <div class="form-group row">

                                <div class="col-md-6">
                                    <label>{{__('Type')}}<span class="red-star">*</span></label>
                                    @php
                                    $typesData = [''=>__('Select Type')];
                                    foreach ($property_types as $key => $value){
                                        $typesData[$value->id] = $value->name;
                                    }

                                    if($importer_data){
                                        $typesDataValue = $importer_data->importer->property_type_id;
                                    }else{
                                        $typesDataValue = isset($result) ? $result->property_type_id: null;
                                    }

                                    @endphp
                                    {!! Form::select('property_type_id',$typesData,$typesDataValue,['class'=>'form-control type-select','id'=>'property_type_id-form-input','onchange'=>"floorSelect();",'autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="property_type_id-form-error"></div>
                                </div>

                                <div class="col-md-6">
                                    <label>{{__('Purpose')}}<span class="red-star">*</span></label>
                                    @php
                                        $purposesData = [''=>__('Select Purpose')];
                                        foreach ($purposes as $key => $value){
                                            $purposesData[$value->id] = $value->name;
                                        }

                                        if($importer_data){
                                            $purposesDataValue = $importer_data->importer->purpose_id;
                                        }else{
                                            $purposesDataValue = isset($result) ? $result->purpose_id: null;
                                        }
                                    @endphp
                                    {!! Form::select('purpose_id',$purposesData,$purposesDataValue,['class'=>'form-control purpose-select','id'=>'purpose_id-form-input','onChange'=>'purposeData();','autocomplete'=>'off']) !!}
                                    <div class="invalid-feedback" id="purpose_id-form-error"></div>
                                </div>

                            </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Property Mobile')}}</label>
                            {!! Form::text('mobile',isset($result) ? $result->mobile: null,['class'=>'form-control','id'=>'mobile-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="mobile-form-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label>{{__('Property Title')}}</label>
                            @php
                                if($importer_data){
                                    $nameValue = $importer_data->title;
                                }else{
                                    $nameValue = isset($result) ? $result->title: null;
                                }
                            @endphp
                            {!! Form::text('title',$nameValue,['class'=>'form-control','id'=>'title-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="title-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Select Status')}}</label>
                            {!! Form::select('status',['for_rent'=>__('For Rent'),'rented'=>__('Rented')], isset($result) ? $result->status: null,['id'=>'status-form-input','class'=>'form-control status-select']) !!}
                            <div class="invalid-feedback" id="status-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Publish Status')}}</label>
                            {!! Form::select('publish',['1'=>__('Active'),'0'=>__('In-Active')], isset($result) ? $result->publish: null,['id'=>'publish-form-input','class'=>'form-control publish-select']) !!}
                            <div class="invalid-feedback" id="publish-form-error"></div>
                        </div>
                    </div>



                    </div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Address')}}</h3>
                    <div class="form-group row">

                        <div class="col-md-12">
                            <label>{{__('Area')}}<span class="red-star">*</span></label>
                            @php
                                $areaViewSelect = [''=> __('Select Area')];
                                if(isset($result)){
                                    $areaViewSelect[$result->local_id] = implode(' -> ',\App\Libs\AreasData::getAreasUp($result->local_id,true) );
                                }

                                if($importer_data){
                                    $areaValue = $importer_data->importer->area_id;
                                    $areaViewSelect[$areaValue] = implode(' -> ',\App\Libs\AreasData::getAreasUp($areaValue,true) );
                                }else{
                                    $areaValue = isset($result) ? $result->local_id: null;
                                }
                            @endphp
                            {!! Form::select('area_id',$areaViewSelect,$areaValue,['class'=>'form-control area-select','id'=>'area_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="area_id-form-error"></div>
                        </div>

                    </div>
                    <div class="form-group row">

                        <div   class="col-md-6">
                            <label>{{__('Building Number')}}</label>
                            {!! Form::text('building_number',isset($result) ? $result->building_number: null,['class'=>'form-control','id'=>'building_number-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="building_number-form-error"></div>
                        </div>
                        <div   class="col-md-6">
                            <label>{{__('Flat Number')}}</label>
                            {!! Form::text('flat_number',isset($result) ? $result->flat_number: null,['class'=>'form-control','id'=>'flat_number-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="flat_number-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div id="floor_div"  class="col-md-6">
                            <label>{{__('Floor')}}</label>
                            @php
                                $propertyFloors = [/*''=>__('Select Floor'),*/'basement'=>__('Basement'),'ground'=>__('Ground')];
                                $floors = range(1,100);
                                foreach ($floors as $key => $value){
                                    $propertyFloors[$value] = $value;
                                }
                                $propertyFloorsValue = isset($result)  ? explode(',',$result->floor): null;

                            @endphp
                            {!! Form::select('floor[]',$propertyFloors,$propertyFloorsValue,['class'=>'form-control floor-select','id'=>'floor-form-input','autocomplete'=>'off','multiple']) !!}
                            <div class="invalid-feedback" id="floor-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Street Name')}}</label>
                            {!! Form::text('street_name',isset($result) ? $result->street_name: null,['class'=>'form-control','id'=>'street_name-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="street_name-form-error"></div>
                        </div>
                        </div>

                        <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Country')}}<span class="red-star">*</span></label>
                            @php
                                // $countryData = [''=>__('Select Country')];
                                 foreach ($countries as $key => $value){
                                     $countryData[$value->id] = $value->name;
                                 }
                            @endphp
                            {!! Form::select('country_id',$countryData, isset($result) ? $result->country_id: null,['class'=>'form-control','id'=>'country_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="country_id-form-error"></div>
                        </div>
                            <div class="col-md-6">
                                <label>{{__('Governorate')}}<span class="red-star">*</span></label>
                                @php
                                    // $governmentData = [''=>__('Select Government')];
                                     foreach ($governments as $key => $value){
                                         $governmentData[$value->id] = $value->name;
                                     }
                                @endphp
                                {!! Form::select('government_id',$governmentData, isset($result) ? $result->government_id: null,['class'=>'form-control government-select','id'=>'government_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="government_id-form-error"></div>
                            </div>

                    </div>

                    <div class="form-group row">

                        <div class="col-md-6">
                            <label>{{__('City')}}<span class="red-star">*</span></label>
                            @php
                                // $cityData = [''=>__('Select City')];
                                 foreach ($cities as $key => $value){
                                     $cityData[$value->id] = $value->name;
                                 }
                            @endphp
                            {!! Form::select('city_id',$cityData, isset($result) ? $result->city_id: null,['class'=>'form-control city-select','id'=>'city_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="city_id-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Area Type')}}<span class="red-star">*</span></label>
                            {!! Form::select('area_type',['hayi'=>__('Hayi'),'markaz'=>__('Markaz'),'qasm'=>__('Qasm')], isset($result) ? $result->area_type: null,['class'=>'form-control','id'=>'area_type-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="area_type-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Mogawra')}}</label>
                            {!! Form::text('mogawra',isset($result) ? $result->mogawra: null,['class'=>'form-control','id'=>'mogawra-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="mogawra-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Detailed address')}}</label>
                            {!! Form::textarea('address',isset($result) ? $result->address: null,['class'=>'form-control','id'=>'address-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="address-form-error"></div>
                        </div>

                    </div>
                </div>



                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Rental terms')}}</h3>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Contract Type')}}<span class="red-star">*</span></label>
                            {!! Form::select('contract_type',[/*''=>__('Select Contract Type'),*/'year'=>__('Year'),'month'=>__('Month'),'day'=>__('Day')],isset($result) ? $result->contract_type: null,['class'=>'form-control','id'=>'contract_type-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="contract_type-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Contract Period')}}</label>
                            {!! Form::text('contract_period',isset($result) ? $result->contract_period: null,['class'=>'form-control','id'=>'contract_period-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="contract_period-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Price')}}<span class="red-star">*</span></label>
                            @php
                                if($importer_data){
                                    $priceValue = $importer_data->price;
                                }else{
                                    $priceValue = isset($result) ? $result->price: null;
                                }
                            @endphp
                            {!! Form::text('price',$priceValue,['class'=>'form-control','id'=>'price-form-input','onChange'=>'installmentPrice();commissionType();','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="price-form-error"></div>
                        </div>

                        <div class="col-md-3">
                            <label>{{__('Deposit Rent')}}</label>
                            {!! Form::text('deposit_rent',isset($result) ? $result->deposit_rent: null,['class'=>'form-control','id'=>'deposit_rent-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="deposit_rent-form-error"></div>
                        </div>
                        <div class="col-md-3">
                            <label>{{__('Insurance Price')}}</label>
                            {!! Form::text('insurance_price',isset($result) ? $result->insurance_price: null,['class'=>'form-control','id'=>'insurance_price-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="insurance_price-form-error"></div>
                        </div>

                    </div>

            </div>


                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Property Data')}}</h3>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Space')}}<span class="red-star">*</span></label>
                            @php
                                if($importer_data){
                                    $spaceValue = $importer_data->space;
                                }else{
                                    $spaceValue = isset($result) ? $result->space: null;
                                }
                            @endphp
                            {!! Form::text('space',$spaceValue,['class'=>'form-control','id'=>'space-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="space-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Building Type')}}<span class="red-star">*</span></label>
                            {!! Form::select('building_type',['villa'=>__('Villa'),'tower'=>__('Tower')], isset($result) ? $result->building_type: null,['class'=>'form-control','id'=>'building_type-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="building_type-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Room Number')}}</label>
                            {!! Form::text('room_number',isset($result) ? $result->room_number: null,['class'=>'form-control','id'=>'room_number-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="room_number-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Bathroom Number')}}</label>
                            {!! Form::text('bathroom_number',isset($result) ? $result->bathroom_number: null,['class'=>'form-control','id'=>'bathroom_number-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="room_number-form-error"></div>
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Features')}}</label>
                            @php
                                $featuresData = array();
                                    // $featuresData = [''=>__('Select City')];
                                     foreach ($features as $key => $value){
                                         $featuresData[$value->id] = $value->name;
                                     }
                                     if(isset($result) && !empty($result->features)){
                                          $propertyFeaturesValue = array();
                                          //$features_arr = explode(',',$result->features);
                                         // foreach($features_arr as $k => $v){
                                         //    $feature_info =  getPropertyFeature_byId($v);
                                       //      $propertyFeaturesValue[$v] = $feature_info->name;
                                     //     }

                                          $propertyFeaturesValue = explode(',',$result->features);
                                     }else{
                                     $propertyFeaturesValue = null;
                                     }


                            @endphp
                            {!! Form::select('features[]',$featuresData,$propertyFeaturesValue,['class'=>'form-control features-select','id'=>'features-form-input','autocomplete'=>'off','multiple']) !!}
                            <div class="invalid-feedback" id="features-form-error"></div>
                        </div>
                    </div>
                    </div>
                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Images')}}</h3>
                    <div class="form-group row">
                        <div class="col-md-12">
                            {{--<label>{{__('Images')}}</label>--}}
                            <input type="file" class="form-control" id="images-form-input" autocomplete="off" name="images" multiple />
                            <div class="invalid-feedback" id="images-form-error"></div>
                        </div>
                    </div>
                    </div>

                <div class="k-portlet__body" style="background: #FFF;margin-top:30px;">
                    <h3 class="k-portlet__head-title" style="color: #00A79D;margin-bottom: 25px">{{__('Other items')}}</h3>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Description')}}</label>
                            @php
                                if($importer_data){
                                    $descriptionValue = $importer_data->description;
                                }else{
                                    $descriptionValue = isset($result) ? $result->description: null;
                                }
                            @endphp
                            {!! Form::textarea('description',$descriptionValue,['class'=>'form-control','id'=>'description-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="description-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Meta Key')}}</label>
                            {!! Form::textarea('meta_key',isset($result) ? $result->meta_key: null,['class'=>'form-control','id'=>'meta_key-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="meta_key-form-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label>{{__('Meta Description')}}</label>
                            {!! Form::textarea('meta_description',isset($result) ? $result->meta_description: null,['class'=>'form-control','id'=>'meta_description-form-input','rows'=>'3','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="meta_description-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>{{__('Video URL')}}</label>
                            {!! Form::url('video_url',isset($result) ? $result->video_url: null,['class'=>'form-control','id'=>'video_url-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="video_url-form-error"></div>
                        </div>
                        <div class="col-md-6">
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
</div>
<!-- end:: Content -->
@endsection
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>

    <script src="{{asset('assets/uploader/jquery.fileuploader.min.js')}}" type="text/javascript"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>

    <script type="text/javascript">

        simpleAjaxSelect2('.client-select','clients',2,'{{__('Owner')}}');
        simpleAjaxSelect2('.area-select','area',1,'{{__('Select Area')}}');
        noAjaxSelect2('.floor-select','{{__('Select Floor')}}','{{App::getLocale()}}');
        noAjaxSelect2('.features-select','{{__('Select Features')}}','{{App::getLocale()}}');

        noAjaxSelect2('.status-select','{{__('Status')}}','{{App::getLocale()}}');
        noAjaxSelect2('.publish-select','{{__('Publish')}}','{{App::getLocale()}}');
        noAjaxSelect2('.purpose-select','{{__('Select Purpose')}}','{{App::getLocale()}}');
        noAjaxSelect2('.type-select','{{__('Select type')}}','{{App::getLocale()}}');
        noAjaxSelect2('.government-select','{{__('Government')}}','{{App::getLocale()}}');
        noAjaxSelect2('.city-select','{{__('City')}}','{{App::getLocale()}}');

        function submitMainForm(){
            formSubmit(
                '{{isset($result) ? route('system.property.update',$result->id):route('system.property.store')}}',
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


        function floorSelect(){
            if(!$('#property_type_id-form-input').val()){
                return false;
            }
            addLoading();

            $('#floor_div').show();
            $('#building_div').attr('class','col-md-4');
            $('#flat_div').attr('class','col-md-4');

            var prop_type = $('#property_type_id-form-input').val();
            var prop_type_array = ['6','7','12','16'];

            if(jQuery.inArray(prop_type, prop_type_array) !== -1){
                $('#floor_div').hide();
                $('#building_div').attr('class','col-md-6');
                $('#flat_div').attr('class','col-md-6');
            }
            removeLoading();
        }




        $(document).ready(function(){
            floorSelect();
            $('.multiple-select2').select2();

        });

        window.closeModal = function(){
            $('#modal-iframe').modal('hide');
        };

        $(document).ready(function() {
            function customImageThumb() {
                var listThumbs = $(".fileuploader-items-list li");
                for (let li of listThumbs) {
                    let imgThumb = $(li);
                    let imgUrl = imgThumb.find('.fileuploader-action-download').attr("href");
                    imgThumb.find('.fileuploader-item-image').html('<img width="60" height="60" src="'+ imgUrl +'" ">');
                }
            }

            function getImageSizeInBytes(imgURL) {
                var request = new XMLHttpRequest();
                request.open("HEAD", imgURL, false);
                request.send(null);
                var headerText = request.getAllResponseHeaders();
                var re = /Content\-Length\s*:\s*(\d+)/i;
                re.exec(headerText);
                return parseInt(RegExp.$1);
            }

            var oldImages = [];

                @if( isset($result) && $result->images->isNotEmpty())

                @foreach($result->images as $key => $value)
            var file = '{{asset($value->path)}}';
            var fileSize =  getImageSizeInBytes(file);
            //console.log(fileSize);
                    oldImages.push({
                    type: 'image',
                    name: '{{$value->image_name}}',//'file.txt', // file name
                    size: fileSize,//1024, // file size in bytes
                    file: file,//'uploads/file.txt', // file path
                    local: file,
                        data: {
                        thumbnail:  '<img width="60" height="60" src="{{asset($value->path)}}">',
                        readerCrossOrigin: 'anonymous', // fix image cross-origin issue (optional)
                        readerForce: true, // prevent the browser cache of the image (optional)
                        readerSkip: true, // skip file from reading by rendering a thumbnail (optional)
                        popup: true, // remove the popup for this file (optional)
                        custom_key : '{{$value->custom_key}}',
                        image_name : '{{$value->image_name}}',
                    }
                });
             @endforeach

                @else
                 oldImages = null;

            @endif

            // enable fileupload plugin
            $('#images-form-input').fileuploader({
                onSelect: function(item) {
                   // if (!item.html.find('.fileuploader-action-start').length)
                      //  item.html.find('.fileuploader-action-remove').before('<a class="fileuploader-action fileuploader-action-start" title="Upload"><i></i></a>');
                },
                upload: {
                    url: '{{route('system.property.image-upload')}}',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'key':  '{{$randKey}}',
                        'property_id': '{{ isset($result) ? $result->id : NULL }}'
                    },
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    start: true,
                    synchron: true,
                    onSuccess: function(result, item) {

                        //console.log(item);
                        //console.log(result);

                        item.html.find('.fileuploader-action-remove').addClass('fileuploader-action-success');
                        item.html.find('.fileuploader-item-image').html('<img width="60" height="60" src="'+ result.path +'" ">');
                    },
                    onError: function(item, listEl, parentEl, newInputEl, inputEl, jqXHR, textStatus, errorThrown) {

                        // console.log(jqXHR);


                        item.upload.status != 'cancelled' && item.html.find('.fileuploader-action-retry').length == 0 ? item.html.find('.column-actions').prepend(
                            '<a class="fileuploader-action fileuploader-action-retry" title="Retry"><i></i></a>'
                        ) : null;
                    },
                    onComplete: null,
                },
                files: oldImages,
                addMore: true,


                onRemove: function(item) {
                    //console.log(oldImages[0]);
                    //console.log(item);
                    // send POST request
                    var imageName = item.name;
                    var randKey = '{{$randKey}}';
                    if(item.data.image_name){
                        imageName = item.data.image_name;
                    }
                    if(item.data.custom_key){
                        randKey = item.data.custom_key;
                    }

                    $.post('{{route('system.property.remove-image')}}', {
                        'name': imageName,
                        '_token': '{{csrf_token()}}',
                        'key':  randKey
                    });
                }
            });
            customImageThumb();
        });



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
                width: 100%;;
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

        </style>
@endsection