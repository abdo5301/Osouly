<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Maintenance extends Model
{

    protected $table = 'maintenance';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'property_id',
        'type',
        'maintenance_category_id',
        'priority',
        'notes',
        'date',
        'status',
        'total_work',
        'total_item',
        'work_details',
        'item_details'
    ];



    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

    public function category(){
        return $this->belongsTo('App\Models\MaintenanceCategory','maintenance_category_id');
    }

    public function getWorkDetailsAttribute($value)
    {
        return json_decode($value,1);
    }

    public function setWorkDetailsAttribute($value)
    {
        $this->attributes['work_details'] = json_encode($value);
    }

    public function getItemDetailsAttribute($value)
    {
        return json_decode($value,1);
    }

    public function setItemDetailsAttribute($value)
    {
        $this->attributes['item_details'] = json_encode($value);
    }


}