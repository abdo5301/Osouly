<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class PropertyDues extends Model
{

    protected $table = 'property_dues';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_id',
        'due_id',
        'name',
        'value',
        'duration',
        'type',
    ];

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

    public function dues(){
        return $this->belongsTo('App\Models\Dues','due_id');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Invoice','property_due_id');
    }



}