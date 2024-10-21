<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class ClientPackages extends Model
{

    protected $table = 'client_packages';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'service_id',
        'client_id',
        'transaction_id',
        'service_type',
        'service_count',
        'status',
        'rest_count',
        'service_details',
        'date_from',
        'date_to',
        'count_per_day',
    ];


    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function service(){
        return $this->belongsTo('App\Models\Service','service_id');
    }

    public function transaction(){
        return $this->belongsTo('App\Models\Transaction','transaction_id');
    }


}