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

                    {!! Form::open(['route' =>  'system.sms.store','files'=>true, 'method' =>  'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}
                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>
                        <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Send To')}}</label>
                            {!! Form::select('send_to',['some'=> __('Specific Clients'),'all'=> __('All Clients')],isset($result) ? $result->status : null,['class'=>'form-control','onChange'=>'renderClientSelect();','id'=>'send_to-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="send_to-form-error"></div>
                        </div>
                        </div>
                        <div id="select-clients-div" class="form-group row">
                        <div  class="col-md-12 param-div">
                            {{--@php--}}
                               {{--$salesViewSelect = array();--}}
                               {{--$salesViewSelect = $salesViewSelect+array_column(getStaff()->toArray(),'name','id');--}}
                            {{--@endphp--}}
                            <label>{{__('Select Clients')}}</label>
                            {!! Form::select('client_id[]',array(),null,['style'=>'width: 100%','class'=>'form-control client-select','id'=>'client_id-form-input','autocomplete'=>'off','multiple']) !!}
                            <div class="invalid-feedback" id="client_id-form-error"></div>
                        </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('SMS Content')}}</label>
                                {!! Form::textarea('sms_content',null,['class'=>'form-control','rows'=>'4','id'=>'sms_content-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="sms_content-form-error"></div>
                            </div>
                        </div>

                        <div class="k-portlet__foot">
                            <div class="k-form__actions">
                                <div class="row" style="float: right;">
                                    <button type="submit" class="btn btn-primary">{{__('Send')}}</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>

    <script type="text/javascript">
        simpleAjaxSelect2('.client-select','clients',2,'{{__('Clients')}}');

        renderClientSelect();

        function renderClientSelect() {
          var select_to = $('#send_to-form-input').val();
          if(select_to === 'all'){
              $('#select-clients-div').css('display','none');
          }else{
              $('#select-clients-div').css('display','block');
          }
        }

        function submitMainForm(){
            if($('#send_to-form-input').val() === 'some'){
                if(!confirm("Do you want to send SMS to selected clients ?")){
                    return false;
                }
            }else {
                if (!confirm("Are you sure to send SMS to all active clients ??\nThat will take much time !")) {
                    return false;
                }
            }

            formSubmit(
                '{{route('system.sms.store')}}',
                $('#main-form').serialize(),
                function ($data) {
                    if($data.code === 200){
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','success',$data.message);
                    }else{
                        $("html, body").animate({ scrollTop: 0 }, "fast");
                        pageAlert('#form-alert-message','error',$data.message);
                    }

                    //   window.location = $data.data.url;
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
        .sales-select{
            width: 100% !important;
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

        .select2-container--default .select2-selection--single .select2-selection__clear,.select2-selection__choice__remove{
            font-size: large;
            color: red !important;
            @if( App::getLocale() !== 'ar')  margin-right: -12px; @else margin-left: -12px; @endif
        }
    </style>
@endsection