<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Contract extends Model
{

    protected $table = 'contracts';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_id',
        'renter_id',
        'contract_content',
        'date_from',
        'date_to',
        'price',
        'pay_from',
        'pay_to',
        'increase_value',
        'increase_percentage',
        'increase_from',
        'pay_every',
        'pay_at',
        'print_code',
        'calendar',
        'limit_to_pay',
        'contract_type',
        'insurance_price',
        'deposit_rent',
        'cut_from_insurance',
        'status',
        'canceled_by_client_id',
        'canceled_reason',
    ];

    public function renter(){
        return $this->belongsTo('App\Models\Client','renter_id');
    }


    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }


    public function canceled_by(){
        return $this->belongsTo('App\Models\Client','canceled_by_client_id');
    }




}