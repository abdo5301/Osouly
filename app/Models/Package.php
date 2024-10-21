<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Package extends Model
{

    protected $table = 'packages';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'services_details',//services ids with (,) like {1,2,3}
    ];

    public function image(){
        return $this->morphOne('App\Models\Image','sign');
    }


    public function clientPackages(){
        return $this->hasMany('App\Models\ClientPackages','package_id');
    }


}