<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Pay extends Model
{

    protected $table = 'pay';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'sign_type',
        'sign_id',
        'client_id',
        'locker_id',
        'invoice_id',
        'price',
        'note',
        'date',
        'staff_id',
        'payment_method_id',
    ];


    public function sign(){
        return $this->morphTo();
    }

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function locker(){
        return $this->belongsTo('App\Models\Locker','locker_id');
    }

    public function staff(){
        return $this->belongsTo('App\Models\Staff','staff_id');
    }

    public function paymentMethod(){
        return $this->belongsTo('App\Models\PaymentMethods','payment_method_id');
    }



}