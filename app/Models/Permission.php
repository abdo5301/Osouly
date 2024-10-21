<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'permissions';
    public $timestamps = true;

    //use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('route_name', 'permission_group_id');



    public function permission_group(){
        return $this->hasOne('App\Models\PermissionGroup');
    }

}