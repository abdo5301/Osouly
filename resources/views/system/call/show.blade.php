<table class="table">
    <tbody>
        <tr>
            <td>{{__('ID')}}</td>
            <td>{{$result->id}}</td>
        </tr>

        <tr>
            <td>{{__('Type')}}</td>
            <td>{{__(strtolower($result->type))}}</td>
        </tr>


        <tr>
            <td>{{__('Client')}}</td>
            <td>
                <a target="_blank" href="{{route('system.client.show',$result->client->id)}}">{{$result->client->fullname}}</a>
            </td>
        </tr>

        <tr>
            <td>{{__('Action')}}</td>
            <td style="color: {{$result->call_purpose->color}}">
                {{$result->call_purpose->{'name_'.App::getLocale()} }}
            </td>
        </tr>


        <tr>
            <td>{{__('Status')}}</td>
            <td style="color: {{$result->call_status->color}}">
                {{$result->call_status->{'name_'.App::getLocale()} }}
            </td>
        </tr>


        <tr>
            <td>{{__('Description')}}</td>
            <td>{{$result->description}}</td>
        </tr>

        @if($result->reminder)
            @foreach($result->reminder as $key => $value)
                <tr style="background: aliceblue;">
                    <td>{{__('Reminder')}}</td>
                    <td>{{$value->date_time->format('Y-m-d h:i A')}}</td>
                </tr>
            @endforeach
        @endif




        @if($result->sign_type)
            {{-- TO BE UPDATED --}}
            {{-- TO BE UPDATED --}}
            {{-- TO BE UPDATED --}}
            {{-- TO BE UPDATED --}}
            {{-- TO BE UPDATED --}}
            {{-- TO BE UPDATED --}}
            {{-- TO BE UPDATED --}}
        @endif

        @if($result->parent_id)
        <tr>
            <td>{{__('Parent Call')}}</td>
            <td>
                <a href="javascript:void(0);" onclick="showCall({{$result->parent_id}});">{{__('#ID: :id',['id'=>$result->parent_id])}}</a>
            </td>
        </tr>
        @endif


        <tr>
            <td>{{__('Created By')}}</td>
            <td>
                <a target="_blank" href="{{route('system.staff.show',$result->staff->id)}}">{{$result->staff->fullname}}</a>
            </td>
        </tr>

        <tr>
            <td>{{__('Created At')}}</td>
            <td>{{$result->created_at->format('Y-m-d h:i A')}}</td>
        </tr>

@if(!$result->sign_type)
        <tr style="background: #f7f8fa;">

            <td colspan="2">

                {!! Form::open(['route' => ['system.call.store'], 'method' => 'POST','class'=> 'k-form','id'=> 'parent-call-form','onsubmit'=> 'submitParentCallForm();return false;']) !!}
                <div class="k-portlet__body">
                    {{Form::hidden('call_id',$result->id)}}
                    <div id="parent-call-form-alert-message"></div>

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
                            <label>{{__('Type')}}<span class="red-star">*</span></label>
                            {!! Form::select('type',['in'=> __('IN'),'out'=> __('OUT')],null,['class'=>'form-control','id'=>'type-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="type-form-error"></div>
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

                        <div class="col-md-4">
                            <label>{{__('Status')}}<span class="red-star">*</span></label>
                            @php
                                $statusData = [''=>__('Select Status')];
                                foreach ($status as $key => $value){
                                    $statusData[$value->id] = $value->{'name_'.App::getLocale()};
                                }
                            @endphp
                            {!! Form::select('call_status_id',$statusData,null,['class'=>'form-control','id'=>'call_status_id-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="call_status_id-form-error"></div>
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <label>{{__('Remind Me')}}</label>
                            {!! Form::select('remind_me',['no'=>__('No'),'yes'=>__('Yes')],null,['class'=>'form-control','id'=>'remind_me_show-form-input','autocomplete'=>'off']) !!}
                            <div class="invalid-feedback" id="remind_me-form-error"></div>
                        </div>
                    </div>

                    <div class="form-group row" id="remind_me_on_div_show" style="display: none;">
                        <div class="col-md-12">
                            <label>{{__('On')}}<span class="red-star">*</span></label>
                            {!! Form::text('remind_me_on',null,['class'=>'form-control k_datetimepicker_3','id'=>'remind_me_on-form-input','autocomplete'=>'off']) !!}
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






                    <div class="form-group row">
                        <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                            </div>
                    </div>



                </div>
                {!! Form::close() !!}

            </td>
        </tr>
@endif
    </tbody>
</table>


<script type="text/javascript">

    function submitParentCallForm(){
        formSubmit(
            '{{route('system.call.store')}}',
            $('#parent-call-form').serialize(),
            function ($data) {
                loadCalls();
                showCall($data.data.id)
            },
            function ($data){
               // console.log($data);
                pageAlert('#parent-call-form-alert-message','error',$data.responseJSON.message);
            }
        );
    }


    $('#remind_me-form-input').change(function () {
        if($(this).val() == 'yes'){
            $('#remind_me_on_div').show();
        }else{
            $('#remind_me_on_div').hide();
        }
    });

    $('#remind_me_show-form-input').change(function () {
        if($(this).val() == 'yes'){
            $('#remind_me_on_div_show').show();
        }else{
            $('#remind_me_on_div_show').hide();
        }
    });




    $(document).ready(function() {

        $('.k_datetimepicker_3').datetimepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss'
        });

        $(".k_datetimepicker_3").click(function(){
            //alert('here');
            $(".datetimepicker-dropdown-bottom-right").css('right',"25%");

        });
    });


</script>
