
    @php
    $data = array_chunk($result->toArray()['data'],3);
    @endphp
    @foreach($data as $chunk)
        <div class="card-deck" style="padding-bottom: 25px;">
            @foreach($chunk as $key => $value)
                <div class="card">
                    <div style="background-color: {{$value['call_status']['color']}} !important" class="k-bg-metal w-100 k-padding-t-5 k-padding-b-5"></div>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{route('system.client.show',$value['client']['id'])}}" target="_blank" style="color: #000 !important;">
                                {{$value['client']['first_name'].' '.$value['client']['second_name']}} ( {{ucfirst($value['client']['type'])}} )
                            </a>
                        </h5>
                        <p class="card-text" style="min-height: 95px;">
                            {{str_limit($value['description'],150)}}
                            <a href="javascript:void(0);" onclick="showCall({{$value['id']}})">{{__('View')}}</a>

                        </p>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td style="background: #f7f8fa;">{{__('Status')}}</td>
                                    <td style="color: {{$value['call_status']['color']}}">
                                        {{$value['call_status']['name_'.App::getLocale()]}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background: #f7f8fa;">{{__('Purpose')}}</td>
                                    <td style="color: {{$value['call_purpose']['color']}}">
                                        {{$value['call_purpose']['name_'.App::getLocale()]}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background: #f7f8fa;">{{__('By')}}</td>
                                    <td>
                                        <a target="_blank" href="{{route('system.staff.show',$value['staff']['id'])}}">
                                            {{$value['staff']['firstname']}} {{$value['staff']['lastname']}}
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <small>{{Carbon::now()->diffForHumans($value['created_at'])}}</small>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
    @if($result->toArray()['next_page_url'])
        @php
        if(!request('page')){
            $page = 2;
        }else{
            $page = request('page')+1;
        }
        @endphp
    <button onclick="loadCalls({{$page}})" type="button" id="load-mode-button" class="btn btn-brand col-md-12">{{__('Load More')}}</button>
    @endif