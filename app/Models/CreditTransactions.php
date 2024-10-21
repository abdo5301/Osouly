<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class CreditTransactions extends Model
{

    protected $table = 'credit_transactions';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


//    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'type',
        'client_id',
        'amount',
        'credit_before',
        'credit_after',
        'staff_id',
        'invoice_id',
        'transaction_id'
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client');
    }

    public function invoice(){
        return $this->belongsTo('App\Models\Invoice');
    }

    public function transaction(){
        return $this->belongsTo('App\Models\Transactions');
    }

}