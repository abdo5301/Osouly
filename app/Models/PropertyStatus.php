<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyStatus extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'property_status';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name_ar',
        'name_en',
        'created_by_staff_id'
    ];

    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

}