<table class="table table-striped">
    <thead>
    <tr>
        <th>{{__('Key')}}</th>
        <th>{{__('Value')}}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{__('ID')}}</td>
        <td>{{$result->id}}</td>
    </tr>
    <tr>
        <td>{{__('Type')}}</td>
        <td>{{$result->property_type->{'name_'.\App::getLocale()} }}</td>
    </tr>
    <tr>
        <td>{{__('Purpose')}}</td>
        <td>{{$result->purpose->{'name_'.\App::getLocale()} }}</td>
    </tr>
    <tr>
        <td>{{__('Data Source')}}</td>
        <td>{{$result->data_source->{'name_'.\App::getLocale()} }}</td>
    </tr>
    <tr>
        <td>{{__('Area')}}</td>
        <td>{{implode(' -> ',\App\Libs\AreasData::getAreasUp($result->area_id,true))}}</td>
    </tr>
    <tr>
        <td>{{__('Status')}}</td>
        <td>{{$result->property_status->{'name_'.\App::getLocale()} }}</td>
    </tr>
    <tr>
        <td>{{__('Description')}}</td>
        <td>{{$result->description}}</td>
    </tr>



    @if($result->payment_type == 'cash')
        <tr>
            <td>{{__('Payment Type')}}</td>
            <td>{{__('Cash')}}</td>
        </tr>
        <tr>
            <td>{{__('Price')}}</td>
            <td>{{amount($result->price,true)}}</td>
        </tr>
    @elseif($result->payment_type == 'installment')
        <tr>
            <td>{{__('Payment Type')}}</td>
            <td>{{__('Installment')}}</td>
        </tr>

        <tr>
            <td>{{__('Years Of Installment')}}</td>
            <td>{{$result->years_of_installment}}</td>
        </tr>

        <tr>
            <td>{{__('Deposit')}}</td>
            <td>{{amount($result->deposit,true)}}</td>
        </tr>

        <tr>
            <td>{{__('Price')}}</td>
            <td>{{amount($result->price,true)}}</td>
        </tr>
    @elseif($result->payment_type == 'cash_installment')
        <tr>
            <td>{{__('Payment Type')}}</td>
            <td>{{__('Cash & Installment')}}</td>
        </tr>

        <tr>
            <td>{{__('Years Of Installment')}}</td>
            <td>{{$result->years_of_installment}}</td>
        </tr>

        <tr>
            <td>{{__('Deposit')}}</td>
            <td>{{amount($result->deposit,true)}}</td>
        </tr>

        <tr>
            <td>{{__('Price')}}</td>
            <td>{{amount($result->price,true)}}</td>
        </tr>
    @endif
    <tr>
        <td>{{__('Currency')}}</td>
        <td>{{$result->currency}}</td>
    </tr>
    <tr>
        <td>{{__('Negotiable')}}</td>
        <td>{{__(ucfirst($result->negotiable))}}</td>
    </tr>

    <tr>
        <td>{{__('Space')}}</td>
        <td>{{number_format($result->space)}} {{__(ucfirst($result->space_type))}}</td>
    </tr>

    <tr>
        <td>{{__('Video')}}</td>
        <td>
            <a target="_blank" href="{{$result->video_url}}">{{$result->video_url}}</a>
        </td>
    </tr>




    </tbody>
</table>
