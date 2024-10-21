<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Request extends Model
{

    protected $table = 'requests';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'renter_id',
        'property_id',
        'status',
    ];

    public function renter(){
        return $this->belongsTo('App\Models\Client','renter_id');
    }

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

    public function calls(){
        return $this->morphMany('App\Models\Call','sign')
            ->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'description',
                'created_by_staff_id',
                'created_at'
            ])
            ->orderByDesc('id')
            ->with([
                'client',
                'call_purpose',
                'call_status',
                'staff'
            ]);
    }


}