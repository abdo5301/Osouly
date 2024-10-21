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
                <div class="k-portlet__body" style="background: #f7f7fb;">

                    {!! Form::open(['route' => isset($result) ? ['system.facility-companies.update',$result->id]:'system.facility-companies.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body param-div" style="background: #FFF;">
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label>{{__('Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('name',isset($result) ? $result->name: null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>
                            <div class="col-md-6">
                                <label>{{__('Company Pay ID')}}</label>
                                {!! Form::number('company_pay_id',isset($result) ? $result->company_pay_id: null,['class'=>'form-control','id'=>'company_pay_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="company_pay_id-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Select Due Name')}}<span class="red-star">*</span></label>
                                @php
                                     $duesData = [''=>__('Due Name')];
                                     foreach ($dues as $key => $value){
                                         $duesData[$value->id] = $value->name;
                                     }
                                @endphp
                                {!! Form::select('due_id',$duesData,isset($result) ? $result->due_id: null,['class'=>'form-control dues-select','id'=>'due_id-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="due_id-form-error"></div>
                            </div>
                            </div>

                        <div  class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Select Locations')}}<span class="red-star">*</span></label>
                                @php
                                    $areaViewSelect = [];
                                    if(isset($result)){
                                        foreach(explode(',',$result->area_ids) as $key => $value){
                                            $areaViewSelect[$value] = implode(' -> ',\App\Libs\AreasData::getAreasUp($value,true) );
                                        }
                                    }
                                @endphp
                                {!! Form::select('area_ids[]',$areaViewSelect,isset($result) ? explode(',',$result->area_ids): null,['class'=>'form-control area-select','id'=>'area_ids-form-input','autocomplete'=>'off','multiple'=>'multiple']) !!}
                                <div class="invalid-feedback" id="area_ids-form-error"></div>
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
    <script src="{{asset('assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js')}}" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
    <script type="text/javascript">

        simpleAjaxSelect2('.area-select','area',1,'{{__('Locations')}}');
        noAjaxSelect2('.dues-select','{{__('Dues')}}','{{App::getLocale()}}');

        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.facility-companies.update',$result->id):route('system.facility-companies.store')}}',
                formData,
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
@section('header')
    <link href="{{asset('assets/select2.css')}}" rel="stylesheet" />


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

        .select2-container--default .select2-search--inline .select2-search__field{
            padding: 0 10px;
            @if( lang() != 'ar')
direction: ltr;
        @endif
}

    </style>

@endsection
