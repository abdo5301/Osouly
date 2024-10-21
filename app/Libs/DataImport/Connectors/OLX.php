<?php
namespace App\Libs\DataImport\Connectors;

class OLX{

    public static function generateURL($area,$type,$purpose,array $space,array $price,$queryName){
        $URL = ['https://olx.com.eg/properties'];

        // Purpose
    //    if($purpose){
            $URL[] = $type.'-for-'.$purpose;
  //      }
/*
        // Purpose
        if($type){
            $URL[] = $type.'-'.$purpose;
        }*/

        // Area
        if($area){
            $URL[] = $area;
        }


        if($queryName){
            $URL[] = 'q-'.str_replace(' ','-',$queryName);
        }

        $URL = implode('/',$URL);

        $query = [];

        if($price['from']){
            $query[] = 'search[filter_float_price:from]='.$price['from'];
        }

        if($price['to']){
            $query[] = 'search[filter_float_price:to]='.$price['to'];
        }


        if($space['from']){
            $query[] = 'search[filter_float_area:from]='.$space['from'];
        }

        if($space['to']){
            $query[] = 'search[filter_float_area:to]='.$space['to'];
        }

        if(!empty($query)){
            $URL.='?'.implode('&',$query);
        }

        return $URL;

    }

    public static function getList($siteURL,$startPage = 1,$endPage = 1){
        $urls = [];

        for($i = $startPage; $i<=$endPage;$i++){
            if($i == 1){
                $siteHTML = file_get_contents($siteURL);
                $html  = str_get_html($siteHTML);
            }else{
                $siteHTML = file_get_contents($siteURL.'?page='.$i);
                $html  = str_get_html($siteHTML);
            }

            foreach ($html->find('.ads__item') as $key => $value) {
                preg_match("#\'(.*)\'#", $value->onclick,$path);

                $urls[] = $path[1];
            }
        }

        return $urls;

    }
    public static function getOneProperty($url){

        preg_match('#ID(.*)\.html#', $url,$match);
        $AdsID = $match[1];
        try{
            $siteHTML = file_get_contents($url);
            $html  = str_get_html($siteHTML);
        }catch (\Exception $exception){
            return false;
        }



        try{
            $name  = trim($html->find('.lheight26',0)->plaintext);
        }catch (\Exception $e){
            $name = '--';
        }
        try{
            $price = trim(str_replace([',','ج.م'], '', $html->find('.xxxx-large',1)->plaintext));
        }catch (\Exception $e){
            $price = 0;
        }
        try{
            $description  = trim($html->find('.lheight20',0)->plaintext);
        }catch (\Exception $e){
            $description  = '--';
        }
        try{
            $ownerName = trim( $html->find('.user-box__info__name',0)->plaintext);
        }catch (\Exception $e){
            $ownerName = '--';
        }



        // -- Options

        $space    = 0;
        $bedRooms = 0;
        $bathRoom = 0;

        foreach ($html->find('table .item') as $key => $value) {
            $optionName = $value->find('tr',0)->find('th',0)->plaintext;
            $optionValue = trim($value->find('tr',0)->find('td',0)->find('strong',0)->plaintext);
            switch ($optionName) {
                case 'المساحة (م٢)':
                    $space = $optionValue;
                    break;

                case 'غرف نوم':
                    $bedRooms = $optionValue;
                    break;

                case 'الحمامات':
                    $bathRoom = $optionValue;
                    break;
            }

        }

        // -- Options

        // Mobile Number
        $mobile = @json_decode(file_get_contents('https://olx.com.eg/ajax/misc/contact/phone/'.$AdsID.'/'));

        if(!$mobile) return false;

        $mobile = str_replace(' ', '', $mobile->value);
        // Mobile Number

        return [
            'id' => $AdsID,
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'space' => $space,
            'bedRooms' => $bedRooms,
            'bathRoom' => $bathRoom,
            'mobile' => $mobile,
            'ownerName'=> $ownerName,
            'url'=> $url
        ];

    }

}
