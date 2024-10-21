<?php

namespace App\Modules\System;

use App\Libs\AreasData;
use App\Models\{Area, Client, PropertyParameter, RequestParameter, Staff,PropertyType,Purpose,PropertyStatus,PermissionGroup,Parameter,Property,ImporterData,Call};
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use App\Http\Requests\StaffFormRequest;
use Form;
use Auth;
use App;

class AjaxController extends SystemController{

    public function index(Request $request){

        switch ($request->type) {

            case 'select_service_packages':
                $service_id = (int)$request->service_id;

                $packages_data = App\Models\Service::where('parent_id',$service_id)->get([
                    'id',
                    \DB::raw('title_' . \App::getLocale() . ' as name')
                ]);

                if(!$service_id || !count($packages_data) > 0){
                    return [];
                }

                $this->viewData['packages_data'] = $packages_data;

                return $this->view('client-package.packages',$this->viewData);

                break;

            case 'service':
                $word = $request->word;

                return App\Models\Service::where(function ($query) use ($word) {
                    $query->where('title_ar', 'LIKE', '%' . $word . '%')
                        ->orWhere('title_en', 'LIKE', '%' . $word . '%');
                })->get([
                    'id',
                    \DB::raw('title_' . \App::getLocale() . ' as value')
                ]);

                break;

            case 'sharingWhatsAppImporterRequest':
                if ($request->sharingWhatsAppImporter) {
                    $s_w_i_ids = $request->sharingWhatsAppImporter;
                    $import_text_render = [];
                    $im_i = 0;
                    foreach ($s_w_i_ids as $im_key => $im_value) {
                        $import = ImporterData::find($im_value);
                        if ($import) {
                            $import_text = implode("\n", ImporterToText($import));
                            $import_text_render[] = $import_text;
                            $im_i++;
                            if ($im_i < count($s_w_i_ids)) {
                                $import_text_render[] = '------------------------------------------------------';
                            }
                        }
                    }

                    return urlencode(implode("\n", $import_text_render));

                }
                break;

            case 'sharingWhatsAppPropertiesRequest':
                if ($request->sharingWhatsAppProperties) {
                    $s_w_p_ids = $request->sharingWhatsAppProperties;
                    $prop_text_render = [];
                    $p_i = 0;
                    foreach ($s_w_p_ids as $p_key => $p_value) {
                        $prop = Property::find($p_value);
                        if ($prop) {
                            $prop_text = implode("\n", propertyToText($prop));
                            $prop_text_render[] = $prop_text;
                            $p_i++;
                            if ($p_i < count($s_w_p_ids)) {
                                $prop_text_render[] = '------------------------------------------------------';
                            }
                        }
                    }

                    return urlencode(implode("\n", $prop_text_render));

                }
                break;

            case 'sharingPropertiesRequest':
                if ($request->id) {
                    if ($request->sharingProperties) {
                        $s_p_ids = implode(',', $request->sharingProperties);
                    } else {
                        $s_p_ids = NULL;
                    }

                    RequestModel::where('id', $request->id)->update([
                        'sharing_properties_ids' => $s_p_ids
                    ]);
                }
                break;
            case 'saveLog':
                if (!empty($request->desc) && !empty($request->model) && !empty($request->id)) {
                    save_log(__($request->desc), $request->model, $request->id);
                    return [];
                }
                return [];
                break;

            case 'readNotification':
                foreach (Auth::user()->unreadNotifications as $notification) {
                    $notification->markAsRead();
                }
                break;

            case 'getNextAreas':
                return AreasData::getNextAreas($request->id);
                break;


            case 'dropdownMenuArea':
                $id = $request->id;


                if ($id == 0) {
                    return [
                        'area_type_id' => 1,
                        'areas' => Area::where('area_type_id', 1)->get(['id', 'name_' . \App::getLocale() . ' as name'])
                    ];
                }

                $data = AreasData::getNextAreas($id);

                $returnData = [];
                if (!empty($data['areas'])) {
                    foreach ($data['areas'] as $key => $value) {
                        $returnData[] = [
                            'id' => $value['id'],
                            'name' => $value['name_' . \App::getLocale()]
                        ];
                    }

                    return [
                        'area_type_id' => $data['type']->id,
                        'areas' => $returnData
                    ];
                }

                return [];

                break;

            case 'property':
                $word = $request->word;


                $data = Property::where('id', $word)->get(['id',
                        \DB::raw('CONCAT(" شقة رقم ",flat_number," -  الدور ",floor," -  رقم المبنى ", building_number, " -   الشارع ", street_name) as name')]);

                if(!$data) return [];

                $returnData = [];
                foreach ($data as $key => $value){
                    $returnData[] =  ['id'=> $value->id, 'value'=> $value->name];
                }

                return $returnData;

                break;

            case 'clients':
                $word = $request->word;


                $data = Client::where('status', 'active')
                    ->where(function ($query) use ($word) {
                        $query->where('first_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('second_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('third_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('mobile', 'LIKE', '%' . $word . '%')
                            ->orWhere('phone', 'LIKE', '%' . $word . '%');
                    })
                    ->get(['id',
                        \DB::raw('CONCAT(first_name," ",second_name," ", third_name, " ", last_name) as name'),
                        'type', 'mobile']);

                if(!$data) return [];

                $returnData = [];
                foreach ($data as $key => $value){
                    $returnData[] =  ['id'=> $value->id, 'value'=> $value->name.' ( '.__(ucfirst($value->type)).' ) ( '.$value->mobile.' )'];
                }

                return $returnData;

                break;

            case 'owner':
                $word = $request->word;

                return Client::where('status', 'active')
                    ->where('type','owner')
                    ->where(function ($query) use ($word) {
                        $query->where('first_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('second_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('third_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('mobile', 'LIKE', '%' . $word . '%')
                            ->orWhere('phone', 'LIKE', '%' . $word . '%');

                    })
                    ->get([
                        'id',
                         \DB::raw('CONCAT(first_name," ",second_name," ", third_name, " ", last_name, " (",mobile,") ") as value')
                    ]);

                break;

            case 'renter':
                $word = $request->word;

                return Client::where('status', 'active')
                      ->where('type','renter')
                    ->where(function ($query) use ($word) {
                        $query->where('first_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('second_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('third_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $word . '%')
                            ->orWhere('mobile', 'LIKE', '%' . $word . '%')
                            ->orWhere('phone', 'LIKE', '%' . $word . '%');

                    })
                    ->get([
                        'id',
                        \DB::raw('CONCAT(first_name," ",second_name," ", third_name, " ", last_name, " (",mobile,") ") as value')
                    ]);

                break;
            case 'property-type':
                $word = $request->word;

                return PropertyType::where(function ($query) use ($word) {
                    $query->where('name_ar', 'LIKE', '%' . $word . '%')
                        ->orWhere('name_en', 'LIKE', '%' . $word . '%');
                })->get([
                    'id',
                    \DB::raw('name_' . \App::getLocale() . ' as value')
                ]);

                break;

            case 'purpose':
                $word = $request->word;

                return Purpose::where(function ($query) use ($word) {
                    $query->where('name_ar', 'LIKE', '%' . $word . '%')
                        ->orWhere('name_en', 'LIKE', '%' . $word . '%');
                })->get([
                    'id',
                    \DB::raw('name_' . \App::getLocale() . ' as value')
                ]);

                break;
            case 'staff':
                $word = $request->word;


                $data = Staff::where('status', 'active')
                    ->where(function ($query) use ($word) {
                        $query->where('firstname', 'LIKE', '%' . $word . '%')
                            ->orWhere('lastname', 'LIKE', '%' . $word . '%')
                            ->orWhere('mobile', 'LIKE', '%' . $word . '%');
                    })
                    ->get(['id',
                        \DB::raw('CONCAT(firstname," ",lastname) as value')
                    ]);

                if(!$data) return [];
                return $data;

//                $returnData = [];
//                foreach ($data as $key => $value){
//                    $returnData[] =  ['id'=> $value->id, 'value'=> $value->firstname.' '.$value->lastname];
//                }
//
//                return $returnData;

                break;
            case 'area':
                $word = $request->word;

                $data = Area::where(function($query) use ($word) {
                    $query->where('name_ar','LIKE','%'.$word.'%')
                        ->orWhere('name_en','LIKE','%'.$word.'%');
                })->get([
                    'id'
                ]);

                if($data->isEmpty()){
                    return [];
                }

                $result = [];

                foreach ($data as $key => $value){
                    $result[] = [
                        'id'=> $value->id,
                        'value'=> str_replace($word,'<b>'.$word.'</b>',implode(' -> ',AreasData::getAreasUp($value->id,true) ))
                    ];
                    
                    if(setting('area_select_type') == '2'){
                        $areaDown = AreasData::getAreasDown($value->id);
                        if(count($areaDown) > 1){
                            array_shift($areaDown);
                            foreach ($areaDown as $aK => $aV){
                                $result[] = [
                                    'id'=> $aV,
                                    'value'=> str_replace($word,'<b>'.$word.'</b>',implode(' -> ',AreasData::getAreasUp($aV,true) ))
                                ];
                            }
                        }
                    }

                }

                return $result;

                break;


        }

    }

}
