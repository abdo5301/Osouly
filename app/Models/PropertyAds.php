<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class PropertyAds extends Model
{

    protected $table = 'property_ads';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_id',
        'start_date',
        'end_date',
        'client_package_id',
        'created_by',
    ];

    public static function block(){
        return static::with('property')->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'));
    }



    public function created_by_client(){
        return $this->belongsTo('App\Models\Client','created_by');
    }

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

    public function client_package(){
        return $this->belongsTo('App\Models\ClientPackages','client_package_id');
    }




}