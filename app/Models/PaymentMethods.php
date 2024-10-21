<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class PaymentMethods extends Model
{

    protected $table = 'payment_methods';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'parameters',
    ];


    public function pay(){
        return $this->hasMany('App\Models\Pay','payment_method_id');
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction','payment_method_id');
    }





}