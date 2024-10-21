<?php

namespace App\Libs;

use App\Models\AreaType;
use App\Models\Area;
use App;

class AreasData{
    public static function getAllTypes(){
        return AreaType::orderBy('id','ASC')->get();
    }

    public static function getNextAreas($id){
        $lang = App::getLocale();

        if(!$id){
            return ['type'=> false,'areas'=> false];
        }

        $areaData = Area::where('parent_id',$id)->select(['*',\DB::raw("name_$lang as name")])->get()->toArray();
        if(!empty($areaData)){
            return [
                'old_type'=> AreaType::select(['*',\DB::raw("name_$lang as name")])->find($id),
                'type'=> AreaType::select(['*',\DB::raw("name_$lang as name")])->find($areaData[0]['area_type_id']),
                'areas'=> $areaData,
            ];
        }else{
            return ['type'=> false,'areas'=> false];
        }
    }


    public static function getAreasDown($areaID,$firstRequest = true,$areaNames = false){
        $lang = App::getLocale();

        static $arrayOfData;
        if($firstRequest == true){
            $arrayOfData = null;
        }

        if($arrayOfData === null){
            if(is_array($areaID)){
                $areaID = getLastNotEmptyItem($areaID);
                if(!$areaID){
                    return [];
                }
            }
            $arrayOfData = [$areaID];
        }

        $result = Area::where('parent_id',$areaID)->get();
        if(!$result->isEmpty()){
            foreach ($result as $value){

                if(!$areaNames){
                    $arrayOfData[$value->id] = $value->id;
                }else{
                     $arrayOfData[$value->id] = $value->{'name_'.$lang};
                }
                //$arrayOfData[] = $value->id;
                self::getAreasDown($value->id,false,$areaNames);
            }
        }

        return $arrayOfData;
    }

    public static function getAreasUp($areaID,$areaNames = false,$firstRequest = true){

        $lang = App::getLocale();

        static $arrayOfData;
        if($firstRequest == true){
            $arrayOfData = [];
        }
        $area = Area::find($areaID);

        if($area){
            if(!$areaNames){
                $arrayOfData[$area->area_type_id] = $area->id;
            }else{
                $arrayOfData[$area->area_type_id] = $area->{'name_'.$lang};
            }

            self::getAreasUp($area->parent_id,$areaNames,false);
        }

        return array_reverse($arrayOfData,true);

    }


    public static function getAreaTypesUp($areaTypeID,$firstRequest = true){

        $lang = App::getLocale();

        static $arrayOfData;
        if($firstRequest == true){
            $arrayOfData = [];
        }

        if(empty($arrayOfData)){
            $data = AreaType::find($areaTypeID);
            $arrayOfData[$data->id] = $data->{'name_'.$lang};
            return self::getAreaTypesUp($areaTypeID,false);
        }else{
            $data = AreaType::where('id','<',$areaTypeID)->orderByDesc('id')->first();
            if($data){
                $arrayOfData[$data->id] = $data->{'name_'.$lang};
                return self::getAreaTypesUp($data->id,false);
            }
        }

        return array_reverse($arrayOfData,true);

    }





}