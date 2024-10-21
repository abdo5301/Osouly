<?php

namespace App\Modules\Api\Transformers;


use App\Models\PropertyFavorite;
use App\Models\PropertyFeatures;
use Illuminate\Support\Facades\Auth;

class PropertyTransformer extends Transformer
{
    public function transform($item,$opt)
    {

    }


    public function renter_requests($item,$lang){
        return [
            'id'=>$item['id'],
            'property_id'=>$item['property_id'],
            'status'=>$item['status'],
            'created_at'=>$item['created_at']
        ];
    }
    public function requests($item,$lang){
        return [
          'id'=>$item['id'],
          'property_id'=>$item['property_id'],
          'status'=>$item['status'],
          'created_at'=>$item['created_at'],
          'renter_name'=>$item['renter']['first_name'].' '.$item['renter']['last_name'],
        ];
    }


    public function own_table($item,$lang){

        return[
            "id"=>$item['id'],
            "title"=>$item['title'],
            "short_address"=>@$item['government']['name'].'/'.@$item['city']['name'],
            "created_at"=> $this->formate_date($item['created_at']),
            "contract_date_from"=>(isset($item['contracts'][0]['date_from']))?$item['contracts'][0]['date_from']:'--',
            "contract_date_to"=>(isset($item['contracts'][0]['date_to']))?$item['contracts'][0]['date_to']:'--',
            "contract_price"=>(isset($item['contracts'][0]['price']))?$item['contracts'][0]['price']:'--',
            "renter_name"=>(isset($item['contracts'][0]['renter_id']))?$item['contracts'][0]['renter']['first_name'].$item['contracts'][0]['renter']['second_name']:'--',
            "renter_mobile"=>(isset($item['contracts'][0]['renter_id']))?$item['contracts'][0]['renter']['mobile']:'--',
            "property_type"=>(isset($item['property_type']['name_'.$lang]))?$item['property_type']['name_'.$lang]:'--',
            "property_status"=>(isset($item['status']))?__($item['status']):'--',
            "contract_type"=>(isset($item['contracts'][0]['contract_type']))?$item['contracts'][0]['contract_type']:'--',
            'images'=>$this->image($item['images'])
        ];

    }

    public function renter_table($item,$lang){

        return[
            "id"=>$item['id'],
            "title"=>$item['title'],
            "short_address"=>@$item['government']['name'].'/'.@$item['city']['name'],
            "created_at"=> $this->formate_date($item['created_at']),
            "contract_date_from"=>(isset($item['contracts'][0]['date_from']))?$item['contracts'][0]['date_from']:'--',
            "contract_date_to"=>(isset($item['contracts'][0]['date_to']))?$item['contracts'][0]['date_to']:'--',
            "contract_price"=>(isset($item['contracts'][0]['price']))?$item['contracts'][0]['price']:'--',
            "owner_name"=>$item['owner']['first_name'].' '.$item['owner']['second_name'].' '.$item['owner']['third_name'].' '.$item['owner']['last_name'],
            "owner_mobile"=>$item['owner']['mobile'],
            'images'=>$this->image($item['images'])
        ];

    }

    function is_favorite($property_id){
        $is_favorite = 'no';
        if (request()->user('api')) {
            $fav = PropertyFavorite::where('property_id',$property_id)->where('client_id',Auth('api')->id())->first();
            if($fav){
                $is_favorite = 'yes';
            }
        }
        return $is_favorite;
    }

    public function block($item,$lang){



        return[
                "id"=>$item['id'],
                "title"=>$item['title'],
                "room_number"=>$item['room_number'],
                "bathroom_number"=>$item['bathroom_number'],
                "space"=>space($item['space']),
                "price"=>amount($item['price'],1),
                "created_at"=> $this->formate_date($item['created_at']),
                "purpose"=>$item['purpose']['name'],
                "slug"=>$item['slug'],
                "short_addree"=>@$item['government']['name'].'/'.@$item['city']['name'],
                "is_favorite"=>$this->is_favorite($item['id']),
                'images'=>$this->image($item['images'])
        ];

    }

    public function details($item){

        return[
            "id"=>$item['id'],

            "property_type"=>$item['property_type']['name'],
            "purpose"=>$item['purpose']['name'],
            "address"=>[
                "short_addree"=>@$item['government']['name'].'/'.@$item['city']['name'],
                 "country"=>$item['country']['name'],
                "country_id"=>$item['country_id'],
                "government"=>@$item['government']['name'],
                "government_id"=>$item['government_id'],
                "city"=>@$item['city']['name'],
                "city_id"=>$item['city_id'],
                "local_id"=>$item['local_id'],
                "local"=>$item['local']['name'],
                "area_type"=>__($item['area_type']),
                "mogawra"=>$item['mogawra'],
                "address"=>$item['address'],
                "street_name"=>$item['street_name'],
                "building_number"=>$item['building_number'],
                "build_type"=>$item['build_type'],
                "floor"=>$item['floor'],
                "flat_number"=>$item['flat_number'],
                "latitude"=>$item['latitude'],
                "longitude"=>$item['longitude'],
                        ],

            "contract_period"=>$item['contract_period'],
            "contract_type"=>$item['contract_type'],
            "insurance_price"=>amount($item['insurance_price'],1),
            "deposit_rent"=>amount($item['deposit_rent'],true),
            "price"=>amount($item['price'],1),
            "space"=>space($item['space']),
            'owner_name'=>$item['owner']['first_name'].' '.$item['owner']['second_name'].' '.$item['owner']['third_name'].' '.$item['owner']['last_name'],
            'owner_id'=>$item['owner']['id'],
            'owner_mobile'=>'*******'.substr($item['mobile'], -3),
           'features'=>$this->features($item['features']),
            "room_number"=>$item['room_number'],
            "bathroom_number"=>$item['bathroom_number'],
            "title"=>$item['title'],
            "description"=>$item['description'],
            "views"=>$item['views'],
            "created_at"=> $this->formate_date($item['created_at']),
            "slug"=>$item['slug'],
            "meta_key"=>$item['meta_key'],
            "meta_description"=>$item['meta_description'],
            "video_url"=>$item['video_url'],
            'images'=>$this->image($item['images']),
            "is_favorite"=>$this->is_favorite($item['id'])

        ];

    }


    public function features($features){
        if(empty($features)){
            return (object)[];
        }
        $features = PropertyFeatures::whereIn('id',explode(',',$features))->get(['id','name_'.lang().' as name']);
        return $features;
    }

    public function my_details($item){

        return[
            "id"=>$item['id'],
            "property_type_id"=>$item['property_type']['id'],
            "property_type"=>$item['property_type']['name'],
            "purpose"=>$item['purpose']['name'],
            "address"=>[
                "short_addree"=>@$item['government']['name'].'/'.@$item['city']['name'],
                "country"=>@$item['country']['name'],
                "country_id"=>$item['country_id'],
                "local_id"=>$item['local_id'],
                "local"=>@$item['local']['name'],
                "government"=>@$item['government']['name'],
                "government_id"=>$item['government_id'],
                "city"=>@$item['city']['name'],
                "city_id"=>$item['city_id'],
                "area_type"=>$item['area_type'],
                "mogawra"=>$item['mogawra'],
                "address"=>$item['address'],
                "street_name"=>$item['street_name'],
                "building_number"=>$item['building_number'],
                "build_type"=>$item['build_type'],
                "floor"=>$item['floor'],
                "flat_number"=>$item['flat_number'],
                "latitude"=>$item['latitude'],
                "longitude"=>$item['longitude'],
            ],

            "contract_period"=>$item['contract_period'],
            "contract_type"=>$item['contract_type'],
            "insurance_price"=>$item['insurance_price'],
            "deposit_rent"=>$item['deposit_rent'],
            "price"=>$item['price'],
            "space"=>$item['space'],
            'owner_name'=>$item['owner']['first_name'].' '.$item['owner']['second_name'],
            'owner_id'=>$item['owner']['id'],
            'owner_mobile'=>$item['mobile'],
            'features'=>$this->features($item['features']),
            "room_number"=>$item['room_number'],
            "bathroom_number"=>$item['bathroom_number'],
            "title"=>$item['title'],
            "description"=>$item['description'],
            "views"=>$item['views'],
            "created_at"=> $this->formate_date($item['created_at']),
            "slug"=>$item['slug'],
            "meta_key"=>$item['meta_key'],
            "meta_description"=>$item['meta_description'],
            "video_url"=>$item['video_url'],
            "status"=>$item['status'],
            "dues"=>$this->property_dues($item['dues']),
            'images'=>$this->image($item['images']),

        ];

    }


    public function type($item,$op){

        return[
          'id' =>$item['id'],
          'name' =>$item['name'],
          'image' =>(!empty($item['image']))?asset($item['image']):''
        ];
    }


    function invoices($item){

        return [
            'id'=>$item['id'],
            'property_id'=>$item['property_id'],
            'address'=> " الشقة ".$item['property']['flat_number']."  ,المنزل ".$item['property']['building_number']."  ,الشارع ".$item['property']['street_name']." ,المنطقة ".$item['property']['local']['name_'.lang()].", المحافظة ".$item['property']['government']['name_'.lang()].' ,'.$item['property']['address'], //(isset($item['property']))?$item['property']['title']:'',
            'amount'=>$item['amount'],
            'due'=>$item['property_due']['dues']['name'],
            'installment'=>$item['installment_id'],
            'date'=>$item['date'],
            'notes'=>$item['notes'],
            'renter'=>$item['client']['first_name'].' '.$item['client']['second_name'].' '.$item['client']['third_name'].' '.$item['client']['last_name'],
            'status'=>$item['status']
        ];
    }

    function renter_invoices($item){

        return [
            'id'=>$item['id'],
            'renter'=>$item['client']['first_name'].' '.$item['client']['second_name'].' '.$item['client']['third_name'].' '.$item['client']['last_name'],
            'property_id'=>$item['property_id'],
            'address'=> " الشقة ".@$item['property']['flat_number']."  ,المنزل ".@$item['property']['building_number']."  ,الشارع ".@$item['property']['street_name']." ,المنطقة ".@$item['property']['local']['name_'.lang()].", المحافظة ".@$item['property']['government']['name_'.lang()].' ,'.@$item['property']['address'], //(isset($item['property']))?$item['property']['title']:'',
             'due'=>isset($item['property_due']['dues']['name'])?$item['property_due']['dues']['name']:'',
            'installment'=>$item['installment_id'],
            'amount'=>$item['amount'],
            'date'=>$item['date'],
            'notes'=>$item['notes'],
            'status'=>$item['status']
        ];
    }


    function invoices_dashboard($item){

        return [
            'id'=>$item['id'],
            'renter'=>$item['client']['first_name'].' '.$item['client']['second_name'].' '.$item['client']['third_name'].' '.$item['client']['last_name'],
            'property_id'=>$item['property_due']['property_id'],
            'mobile'=>$item['client']['mobile'],
            'date'=>$item['date'],
            'amount'=>$item['amount'],
            'status'=>$item['status'],
            'notes'=>$item['notes'],
            'due'=>$item['property_due']['name'].' - '.__('Property No ').$item['property_id']
        ];
    }

    function dues($item){

            return   [
                'id' => $item['id'],
                'due' => $item['dues']['name'],
                'value' => $item['value'],
                'duration' => __($item['duration']),
                'type' => __($item['type'])
            ];

    }

    function property_dues($items){
        $arr = [];
        foreach ($items as $item) {

            $arr[] = [
                'id' => $item['id'],
                'due_id' => $item['due_id'],
                'due' => $item['dues']['name'],
                'value' => $item['value'],
                'duration' => $item['duration'],
                'type' => $item['type']
            ];
        }
        return $arr;
    }

    function facilities($item){

        return [
            'id'=>$item['id'],
            'company'=>$item['company']['name'],
            'company_id'=>$item['facility_company_id'],
            'number'=>$item['number']
        ];
    }

    function facility_companies($item){
        if(empty($item)){
            return [];
        }
        return [
            'id'=>$item['id'],
            'name'=>$item['name'],
            'due_name'=>(isset($item['dues']['name']))?$item['dues']['name']:''
        ];
    }



    function renter_contracts($item){

        return [
            'id'=>$item['id'],
            'date_from'=>$item['date_from'],
            'date_to'=>$item['date_to'],
            'price'=>$item['price'],
            'contract_type'=>$item['contract_type'],
            'insurance_price'=>$item['insurance_price'],
            'status'=>$item['status']

        ];
    }

    function contracts($item){

        return [
            'id'=>$item['id'],
            'property_id'=>$item['property_id'],
            'property_title'=>'',//$item['property']['title'],
            'renter'=>($item['renter_id'])?$item['renter']['first_name'].' '.$item['renter']['second_name'].' '.$item['renter']['third_name'].' '.$item['renter']['last_name']: '',
            'date_from'=>$item['date_from'],
            'date_to'=>$item['date_to'],
            'price'=>$item['price'],
            'contract_type'=>$item['contract_type'],
            'increase_from'=>$item['increase_from'],
            'increase_value'=>$item['increase_value'],
            'insurance_price'=>$item['insurance_price'],
            'created_at'=>date('Y-m-d',strtotime($item['created_at'])),
            'status'=>$item['status']

        ];
    }

    function increases($item){

        return [
            'property_id'=>$item['property_id'],
            'renter'=>($item['renter_id'])?$item['renter']['first_name'].' '.$item['renter']['second_name'].' '.$item['renter']['third_name'].' '.$item['renter']['last_name']: '',
            'mobile'=>($item['renter_id'])?$item['renter']['mobile']: '',
            'increase_from'=>$item['increase_from'],
            'increase_value'=>$item['increase_value'],
            'price_after_increase'=>$item['price'] + $item['increase_value']

        ];
    }


}