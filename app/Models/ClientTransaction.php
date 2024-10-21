<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class  ClientTransaction extends Model
{

    protected $table = 'client_transactions';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'type',
        'client_id',
        'transaction_id',
        'amount',
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function transaction(){
        return $this->belongsTo('App\Models\Transaction','transaction_id');
    }



}