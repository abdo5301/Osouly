<?php

namespace App\Libs\DataImport;

use App\Libs\DataImport\Connectors\OLX;
use App\Libs\DataImport\Connectors\Aqarmap;
use App\Models\ImporterData;
use App\Models\Property;

class Importer{

    private $connector;

    private $area       = null,
            $type       = null,
            $purpose    = null,
            $queryName  = null,
            $importer = null,
            $list       = [];

    private $space = [
        'from'=> null,
        'to'=> null
    ];

    private $price = [
        'from'=> null,
        'to'=> null
    ];


    public function setImporterModal($importer){
        $this->importer = $importer;
    }

    public function __construct($connector){
        if(!in_array($connector,['OLX','Aqarmap'])){
            throw new \Exception('Undefined Connector');
        }

        $this->connector = $connector;
    }


    public function setQueryName($queryName){
        $this->queryName = $queryName;
        return $this;
    }

    public function setArea($area){
        $this->area = $area;
        return $this;
    }
    public function setType($type){
        $this->type = $type;
        return $this;
    }
    public function setPurpose($purpose){
        $this->purpose = $purpose;
        return $this;
    }
    public function setSpace($from = null,$to = null){
        $this->space = [
            'from'  => $from,
            'to'    => $to
        ];
        return $this;
    }
    public function setPrice($from = null,$to = null){
        $this->price = [
            'from'  => $from,
            'to'    => $to
        ];
        return $this;
    }

    public function getList($startPage = 1,$endPage = 1){

        switch ($this->connector){
            case 'OLX':
                $connector = OLX::class;
                break;

            case 'Aqarmap':
                $connector = Aqarmap::class;
                break;
        }

        $URL = $connector::generateURL($this->area,$this->type,$this->purpose,$this->space,$this->price,$this->queryName);
        $this->list = $connector::getList($URL,$startPage,$endPage);
        return $this;
    }

    public function getProperties(){
        if(!$this->list) return ['status'=> false,'message'=> __('Unable to Get Properties')];

        switch ($this->connector){
            case 'OLX':
                $connector = OLX::class;
                break;

            case 'Aqarmap':
                $connector = Aqarmap::class;
                break;
        }


        foreach ($this->list as $value){
            $propertyDataFromConnector = $connector::getOneProperty($value);
            if($propertyDataFromConnector){

                if(ImporterData::where('connector_id',$propertyDataFromConnector['id'])->exists()) continue;

                $insertImporterData = ImporterData::create([
                    'importer_id'=> $this->importer->id,
                    'connector_id'=> $propertyDataFromConnector['id'],
                    'name'=> $propertyDataFromConnector['name'],
                    'price'=> $propertyDataFromConnector['price'],
                    'description'=> $propertyDataFromConnector['description'],
                    'space'=> $propertyDataFromConnector['space'],
                    'bed_rooms'=> $this->convertArabicInt($propertyDataFromConnector['bedRooms']),
                    'bath_room'=> $this->convertArabicInt($propertyDataFromConnector['bathRoom']),
                    'mobile'=> $propertyDataFromConnector['mobile'],
                    'owner_name'=> $propertyDataFromConnector['ownerName'],
                    'url'=> $propertyDataFromConnector['url']
                ]);
                
                if($insertImporterData){

                    $this->importer->increment('success');
                }
            }

        }


        $this->importer->update([
            'status'=> 'done'
        ]);

        return true;

    }


    private function convertArabicInt($int){
        return str_replace([
            '٠',
            '١',
            '٢',
            '٣',
            '٤',
            '٥',
            '٦',
            '٧',
            '٨',
            '٩'
        ],[
            0,1,2,3,4,5,6,7,8,9
        ],$int);
    }

}