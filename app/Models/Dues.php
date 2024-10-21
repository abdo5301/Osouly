<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Dues extends Model
{

    protected $table = 'dues';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'type',
        'description',
        'image',
        'status',
    ];


    public function propertyDues(){
        return $this->hasMany('App\Models\PropertyDues','due_id');
    }


    public function companies(){
        return $this->hasMany('App\Models\FacilityCompanies','due_id');
    }

    public function image(){
        return $this->morphOne('App\Models\Image','sign');
    }



}