<?php

namespace App\Libs;

class CheckKeyInArray{

    private static $arrayData = [];

    public static function setArray($array){
        self::$arrayData = $array;
    }

    public static function check($key){
        if(isset(self::$arrayData[$key])){
            return self::$arrayData[$key];
        }

        return null;

    }

}