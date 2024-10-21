@if(isset($packages_data) && !empty($packages_data))
<div  class="col-md-12 param-div">
    <label>{{__('Select Package')}}<span class="red-star">*</span></label>
    @php
        $packagesData = [''=>__('Packages')];
        foreach ($packages_data as $key => $value){
            $packagesData[$value->id] = $value->name;
        }
    @endphp
    {!! Form::select('package_id',$packagesData,null,['style'=>'width: 100%','class'=>'form-control packages-select','id'=>'package_id-form-input','autocomplete'=>'off']) !!}
    <div class="invalid-feedback" id="package_id-form-error"></div>
</div>

    <script>
        noAjaxSelect2('.packages-select','{{__('Packages')}}','{{App::getLocale()}}');
    </script>
@endif

