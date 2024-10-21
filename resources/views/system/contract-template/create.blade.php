@extends('system.layout')
@section('content')
    <div class="modal fade" id="vars-modal"  role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{__('Variables')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" style="width: 100%">
                        <thead>
                        <tr>
                            <th>{{__('Name')}}</th>
                            <th>{{__('Value')}}</th>
                            <th>{{__('Copy')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vars as $key=>$var)
                        <tr>
                            <td>{{__(ucwords(str_replace('_',' ',$key)))}}</td>
                            <td>{{$var}}</td>
                            <td class="text-center"><i onclick="copyVar('{{$var}}')" title="{{__('Copy')}}" class="la la-copy" style="color: dimgray;cursor: pointer;"></i><input class="copy-input" id="{{$var}}" value="{{$var}}"></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <input type="reset" class="btn btn-outline-secondary btn-md" data-dismiss="modal" value="{{__('Close')}}">
                </div>
            </div>
        </div>
    </div>

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
            <div class="k-content__head-toolbar">
                <div class="k-content__head-wrapper">
                    <a href="#" data-toggle="modal" data-target="#vars-modal" class="btn btn-sm btn-elevate btn-brand" data-toggle="k-tooltip" title="{{__('Variables')}}" data-placement="left">
                        <span class="k-font-bold" id="k_dashboard_daterangepicker_date">{{__('Variables')}}</span>
                        <i class="flaticon-search k-padding-l-5 k-padding-r-0"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- end:: Content Head -->


        <!-- begin:: Content Body -->
        <div class="k-content__body	k-grid__item k-grid__item--fluid" id="k_content_body">
            <div class="k-portlet k-portlet--mobile">
                <div class="k-portlet__body" style="background: #f7f7fb;">

                    {!! Form::open(['route' => isset($result) ? ['system.contract-template.update',$result->id]:'system.contract-template.store','files'=>true, 'method' => isset($result) ?  'PATCH' : 'POST','class'=> 'k-form','id'=> 'main-form','onsubmit'=> 'submitMainForm();return false;']) !!}

                    <div class="k-portlet__body" style="background: #FFF;">
                        <div id="form-alert-message"></div>


                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Name')}}<span class="red-star">*</span></label>
                                {!! Form::text('name',isset($result) ? $result->name: null,['class'=>'form-control','id'=>'name-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="name-form-error"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>{{__('Content')}}<span class="red-star">*</span></label>
                                {!! Form::textarea('temp_content',isset($result) ? $result->template_content: null,['class'=>'form-control temp_text_editor','id'=>'temp_content-form-input','autocomplete'=>'off']) !!}
                                <div class="invalid-feedback" id="temp_content-form-error"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.8/inputmask/inputmask.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            $('.temp_text_editor').summernote({
                height:200,
            });
        });


        function submitMainForm(){
            var form = $('#main-form')[0];
            var formData = new FormData(form);
            formSubmit(
                '{{isset($result) ? route('system.contract-template.update',$result->id):route('system.contract-template.store')}}',
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

        // handel copy vars here
        function copyVar(element) {
            var justForCopy = document.getElementById(element);
            justForCopy.select();
            document.execCommand("copy");
           // alert("تم نسخ : " + justForCopy.value);
        }


    </script>
@endsection
@section('header')
<style>
    .copy-input{
        position: absolute;
        width: 10px !important;
        height: 10px !important;
        right: -3000px;
    }
</style>
@endsection
