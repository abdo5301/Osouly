<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyViews extends Model
{

    protected $table = 'property_views';
    public $timestamps = true;

    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'request_id',
        'property_id',
        'views'
    ];

    public function request(){
        return $this->belongsTo('App\Models\Request','request_id');
    }

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

}