<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Importer extends Model
{


    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'importer';

    public $timestamps = true;

    protected $dates = ['created_at','updated_at'];

    protected $fillable = [
        'connector',
        'area_id',
        'property_type_id',
        'purpose_id',
        'query_name',
        'space_from',
        'space_to',
        'price_from',
        'price_to',
        'page_start',
        'page_end',
        'status',
        'success',
        'created_by_staff_id'
    ];


    public function property_type(){
        return $this->belongsTo('App\Models\PropertyType','property_type_id');
    }

    public function purpose(){
        return $this->belongsTo('App\Models\Purpose','purpose_id');
    }

    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function area(){
        return $this->belongsTo('App\Models\Area','area_id');
    }

    public function data(){
        return $this->hasMany('App\Models\ImporterData','importer_id');
    }



}