<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even){background-color: #f2f2f2}
</style>
<div  style="overflow-x:auto;">
    <table border="1">
        <thead>
        <tr>
            <th>#</th>
            <th>{{__('Value')}}</th>
        </tr>
        </thead>
        <tbody>

        <tr>
            <td>{{__('ID')}}</td>
            <td>{{$result->id}}</td>
        </tr>


        <tr>
            <td>{{__('Log Name')}}</td>
            <td>{{$result->log_name}}</td>
        </tr>

        <tr>
            <td>{{__('Status')}}</td>
            <td>{{$result->description}}</td>
        </tr>

        <tr>
            <td>{{__('Model')}}</td>
            <td>{{last(explode('\\',$result->subject_type))}} ({{$result->subject_id}})</td>
        </tr>
@if($result->causer_id)
        <tr>
            <td>{{__('User')}}</td>
            <td><a target="_blank" href="{{route('system.staff.show',$result->causer->id)}}">{{$result->causer->fullname}}</a></td>
        </tr>
@endif



        <tr>
            <td>{{__('Device')}}</td>
            <td>
                {{$result->agent->device()}}
                @if($result->agent->isDesktop())
                    {{__('Desktop')}}
                @elseif($result->agent->isMobile())
                    {{__('Mobile')}}
                @elseif($result->agent->isTablet())
                    {{__('Tablet')}}
                @else
                    --
                @endif
            </td>
        </tr>


        <tr>
            <td>{{__('Platform')}}</td>
            <td>{{$result->agent->platform()}} {{$result->agent->version($result->agent->platform())}}</td>
        </tr>


        <tr>
            <td>{{__('IP')}}</td>
            <td>{{$result->ip}}</td>
        </tr>


        <tr>
            <td>{{__('Browser')}}</td>
            <td>{{$result->agent->browser()}}</td>
        </tr>

        <tr>
            <td>{{__('Languages')}}</td>
            <td>{{implode(',',$result->agent->languages())}}</td>
        </tr>







        @if(isset($result->location))
        <tr>
            <td>{{__('Country')}}</td>
            <td>{{$result->location->country}} ({{$result->location->countryCode}})</td>
        </tr>
        <tr>
            <td>{{__('city')}}</td>
            <td>{{$result->location->city}}</td>
        </tr>
        <tr>
            <td>{{__('Region Name')}}</td>
            <td>{{$result->location->regionName}}</td>
        </tr>
        <tr>
            <td>{{__('ISP')}}</td>
            <td>{{$result->location->isp}}</td>
        </tr>
        <tr>
            <td>{{__('Latitude')}}</td>
            <td>{{$result->location->lat}}</td>
        </tr>
        <tr>
            <td>{{__('Longitude')}}</td>
            <td>{{$result->location->lon}}</td>
        </tr>
        @endif


        <tr>
            <td>{{__('URL')}}</td>
            @if($result->url)
            <td>{{$result->method}} <a href="{{$result->url}}" target="_blank">{{$result->url}}</a> </td>
            @elseif(last(explode('\\',$result->subject_type)) == 'ImporterData')
                @php
                $importer = App\Models\ImporterData::where('id',$result->subject_id)->first();
                @endphp
                <td><a href="{{route('system.importer.show',$importer->importer_id.'?id='.$result->subject_id)}}" target="_blank">{{route('system.importer.show',$importer->importer_id.'?id='.$result->subject_id)}}</a> </td>

            @elseif(last(explode('\\',$result->subject_type)) == 'LeadData')
{{--                @php--}}
{{--                    $lead = App\Models\LeadData::where('id',$result->subject_id)->first();--}}
{{--                @endphp--}}
                <td><a href="{{route('system.lead-data.show',$result->subject_id)}}" target="_blank">{{route('system.lead-data.show',$result->subject_id)}}</a> </td>

            @elseif(last(explode('\\',$result->subject_type)) == 'PropertyParameter')
                @php
                    $property = App\Models\PropertyParameter::where('id',$result->subject_id)->first();
                @endphp
                <td><a href="{{route('system.property.show',$property->property_id)}}" target="_blank">{{route('system.property.show',$property->property_id)}}</a> </td>
            @elseif(last(explode('\\',$result->subject_type)) == 'RequestParameter')
                @php
                    $request = App\Models\RequestParameter::where('id',$result->subject_id)->first();
                @endphp
                <td><a href="{{route('system.request.show',$request->request_id)}}" target="_blank">{{route('system.request.show',$request->request_id)}}</a> </td>
            @elseif(in_array(last(explode('\\',$result->subject_type)),['RequestStatus','LeadStatus','PropertyStatus','PropertyType','PropertyModel','PermissionGroups','DataSource','CallPurpose','CallStatus']))
                @php
                              $subject = last(explode('\\',$result->subject_type));
                    preg_match( '/[A-Z]/', lcfirst($subject), $matches, PREG_OFFSET_CAPTURE );
                             $first_word_subject_length =  $matches[0][1];
                             $dash_subject = substr_replace(strtolower($subject),"-",$first_word_subject_length,0);
                @endphp
            @if(\Route::has(('system.'.strtolower($dash_subject).'.index')))
                    <td><a href="{{route('system.'.$dash_subject.'.index')}}" target="_blank">{{route('system.'.$dash_subject.'.index')}}</a> </td>
                @else
                <td>--</td>
            @endif

            @else
                @if(\Route::has(('system.'.strtolower(last(explode('\\',$result->subject_type))).'.show')))
            <td><a href="{{route('system.'.strtolower(last(explode('\\',$result->subject_type))).'.show',$result->subject_id)}}" target="_blank">{{route('system.'.strtolower(last(explode('\\',$result->subject_type))).'.show',$result->subject_id)}}</a> </td>
                @else
                    <td>--</td>
            @endif
            @endif
        </tr>

        <tr>
            <td>{{__('Created At')}}</td>
            <td>
                @if($result->created_at == null)
                    --
                @else
                    {{$result->created_at->diffForHumans()}}
                @endif
            </td>
        </tr>

        </tbody>
    </table>
@if(isset($result->properties['attributes']))
    <hr>

    <h3 style="text-align: center;">{{__('Data')}}</h3>

    <table border="1">
        <thead>
        <tr>
            <th>Key</th>
            @if(isset($result->properties['old']))
                <th>{{__('Old')}}</th>
                <th>{{__('New')}}</th>
            @else
                <th>{{__('Attributes')}}</th>
            @endif

        </tr>
        </thead>
        <tbody>

        @php
        $keys = array_keys($result->properties['attributes']);
        @endphp

        @foreach($keys as $value)
            <tr>
                <td>{{$value}}</td>

                @if(isset($result->properties['old']))
                    <td>
                        @if(is_array($result->properties['old'][$value]))
                            <pre>
                        {{print_r($result->properties['old'][$value])}}
                    </pre>
                        @else
                            {{$result->properties['old'][$value]}}
                        @endif



                    </td>
                @endif
                <td>
                    @if(is_array($result->properties['attributes'][$value]))
                    <pre>
                        {{print_r($result->properties['attributes'][$value])}}
                    </pre>
                    @else
                        {{$result->properties['attributes'][$value]}}
                    @endif
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
@endif

</div>
