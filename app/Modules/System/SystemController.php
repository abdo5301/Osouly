<?php

namespace App\Modules\System;

use App\Http\Controllers\Controller;
use App\Models\CallPurpose;
use App\Models\Client;
use App\Models\Importer;
use App\Models\ImporterData;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\Request;
use App\Models\RequestStatus;
use App\Notifications\General;

class SystemController extends Controller{

    protected $viewData = [
        'breadcrumb'=> []
    ];

    public function __construct(){

        $this->middleware(['auth:staff']);

        $this->viewData['call_purpose_menu'] = CallPurpose::get([
            'id',
            'name_ar',
            'name_en'
        ]);

 

    }

    protected function view($file,array $data = []){
        return view('system.'.$file,$data);
    }

    protected function response($status,$code = '200',$message = 'Done',$data = []): array {
        return [
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ];
    }

    public function dashboard(\Illuminate\Http\Request $request){
        
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Dashboard')
        ];

        $this->viewData['pageTitle'] = __('Dashboard');

        $this->viewData['pending_owners'] = number_format(Client::where(['type'=>'owner','status'=>'pending'])->count());
        $this->viewData['pending_renters'] = number_format(Client::where(['type'=>'renter','status'=>'pending'])->count());
        $this->viewData['pending_both'] = number_format(Client::where(['type'=>'both','status'=>'pending'])->count());
        $this->viewData['pending_properties'] = number_format(Property::where(['publish'=>'0','status'=>'for_rent'])->count());


        $this->viewData['owners'] = number_format(Client::where(['type'=>'owner','status'=>'active'])->count());
        $this->viewData['renters'] = number_format(Client::where(['type'=>'renter','status'=>'active'])->count());
        $this->viewData['both'] = number_format(Client::where(['type'=>'both','status'=>'active'])->count());

        $this->viewData['properties'] = number_format(Property::count());
        $this->viewData['requests'] = number_format(Request::count());

        return $this->view('dashboard',$this->viewData);
    }
    
    public function test(){
        
      include app_path('Libs/DataImport/simple_html_dom.php');

            $data = Importer::where('status','pending')->first();
            
            dd($data);
            if(!$data){
                return false;
            }

            $data->update([
                'status'=> 'proccess'
            ]);

            $importer = new \App\Libs\DataImport\Importer($data->connector);
            $importer->setImporterModal($data);
            switch ($data->connector){
                case 'OLX':
                    if($data->area->olx_id) $importer->setArea($data->area->olx_id);
                    if($data->property_type->olx_id) $importer->setType($data->property_type->olx_id);
                    if($data->purpose->olx_id) $importer->setPurpose($data->purpose->olx_id);
                    if($data->space_from || $data->space_to) $importer->setSpace($data->space_from,$data->space_to);
                    if($data->price_from || $data->price_to) $importer->setPrice($data->price_from,$data->price_to);
                    break;
            }

            $importer->getList($data->page_start,$data->page_end)->getProperties();   
        
    
    }

}
