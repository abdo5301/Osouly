<?php
namespace App\Libs\DataImport\Connectors;

class Aqarmap{

    public static function getList($siteURL,$startPage = 1,$endPage = 1){
        $urls = [];

        for($i = $startPage; $i<=$endPage;$i++){
            if($i == 1){
                $html  = str_get_html(file_get_contents($siteURL));
            }else{
                $html  = str_get_html(file_get_contents($siteURL.'?page='.$i));
            }

            foreach ($html->find('.listingItem') as $key => $value) {
                $path = $value->find('div',0)->find('div',0)->find('div',0)->find('div',0)->find('a',0)->href;
                if(strpos($path, '/ar/user/') === 0){
                    $path = $value->find('div',0)->find('div',0)->find('div',0)->find('div',0)->find('a',1)->href;
                }

                $urls[] = 'https://egypt.aqarmap.com'.$path;
            }
        }

        return $urls;

    }

    public static function getOneProperty($url){

        $html  = str_get_html(file_get_contents($url));
        $name  = trim($html->find('.titleAndAddress ',0)->find('div',0)->find('h1',0)->plaintext);
        $price = trim(str_replace([','], '', $html->find('.listing-price-content',0)->find('span',0)->plaintext));
        $description  = trim($html->find('#listingText',0)->plaintext);
        $ownerName = trim( $html->find('.sellerProperties',0)->find('h4',0)->plaintext);
        $mobile = urldecode(trim( $html->find('.phoneNumber',0)->{'data-number'}));




        // -- Options

        $space    = 0;
        $bedRooms = 0;
        $bathRoom = 0;

        foreach ($html->find('.listing_attributes',0)->find('label') as $key => $value) {


            $optionName  = trim(str_replace([0,1,2,3,4,5,6,7,8,9], '', $value->plaintext));
            $optionValue = (int) trim($value->plaintext);

            switch ($optionName) {
                case 'متر²':
                    $space = $optionValue;
                    break;

                case 'غرف':
                    $bedRooms = $optionValue;
                    break;

                case 'حمام':
                    $bathRoom = $optionValue;
                    break;
            }

        }

        // -- Options

        return [
            'id' => explode('-', end(explode('/', $url)))[0],
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'space' => $space,
            'bedRooms' => $bedRooms,
            'bathRoom' => $bathRoom,
            'mobile' => $mobile,
            'ownerName'=> $ownerName
        ];

    }

}