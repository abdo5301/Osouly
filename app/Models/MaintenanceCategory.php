<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class MaintenanceCategory extends Model
{

    protected $table = 'maintenance_categories';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'parent_id',
        'name_ar',
        'name_en',
        'image',
        'status',
    ];


    public function parent(){
        return $this->belongsTo('App\Models\MaintenanceCategory','parent_id');
    }


}