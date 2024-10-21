<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class Transaction extends Model
{

    protected $table = 'transactions';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'payment_method_id',
        'client_id',
        'invoice_id',
        'service_id',
        'type',
        'notes',
        'request',
        'response',
        'status',
        'amount',
        'total_amount',
        'session_id',
        'version',
    ];


    public function payment_method(){
        return $this->belongsTo('App\Models\PaymentMethods','payment_method_id');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function invoices(){
        return $this->hasMany('App\Models\TransactionInvoices','id','transaction_ic');
    }

    public function service(){
        return $this->belongsTo('App\Models\Service','service_id');
    }

    public function clientPackages(){
        $this->hasMany('App\Models\ClientPackages','transaction_id');
    }

}