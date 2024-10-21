<?php

namespace App\Modules\Api\Transformers;

use Carbon\Carbon;

abstract class Transformer
{
    public static $lang;

    public function transformCollection(array $items,$systemLang,$method='transform'){
        if(is_array($systemLang))
            self::$lang = ((end($systemLang) == 'en')?'en':'ar');
        else
            self::$lang = $systemLang;
        if(isset($items['current_page'])){
            return $this->transformPaginate($items,$systemLang,$method);
        }
        return array_map([$this,$method],$items,[$systemLang]);
    }


    public function transformPaginate($item,$opt,$method){
        return array_merge([
            'current_page'      =>$item['current_page'],
            'from'              =>((isset($item['from']))?$item['from']:""),
            'last_page'         =>$item['last_page'],
            'next_page_url'     =>((!$item['next_page_url'])?'':self::NextPageUrl($item['next_page_url'])),
            'path'              =>self::NextPageUrl($item['path']),
            'per_page'          =>$item['per_page'],
            'prev_page_url'     =>(isset($item['prev_page_url'])?$item['prev_page_url']:""),
            'to'                =>((isset($item['to']))?$item['to']:""),
            'total'             =>$item['total'],
        ],['items'=>$this->transformCollection($item['data'],$opt,$method)]);
    }

    public abstract function transform($item,$opt);

    private static function NextPageUrl($url){
        return $url;
        return trim(substr($url,strpos($url,'/',43)),'/');
    }


    public static function trans($item,$column,$lang){
        if(!isset(self::$lang)){
            if(isset($lang))
                self::$lang = ((is_array($lang))? end($lang):$lang);
        }
        if(isset($item[$column]))
            return $item[$column];
        else if(isset($item[$column.'_'.self::$lang])){
                return $item[$column.'_'.self::$lang];
        } else
            return null;
    }


    public static function formate_date($date){
        return date('Y-m-d',strtotime($date));
    }

    public static function Round($number){
        return number_format($number,2);
    }


    public static function TransformerDate($datetime){
        return Carbon::createFromFormat('Y-m-d H:i:s',$datetime);
    }

    public static function image($images){
        $images_url = [];
        if(!empty($images)){
        foreach ($images as $image){
            $images_url[] = [
                'id'=>$image['id'],
                'path'=>asset($image['path']),
                'name'=>$image['image_name'],
                'notes'=>$image['notes']
            ];
        }
        }
        return $images_url;
    }

}