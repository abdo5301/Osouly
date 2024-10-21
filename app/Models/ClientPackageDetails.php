<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class  ClientPackageDetails extends Model
{

    protected $table = 'client_package_details';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_package_id',
        'service_id',
        'count',
        'used_count',
        'from_date',
        'to_date',
    ];

    public function package(){
        return $this->belongsTo('App\Models\ClientPackages','client_package_id');
    }

    public function service(){
        return $this->belongsTo('App\Models\Service','service_id');
    }


}