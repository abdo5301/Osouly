<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class TransactionInvoices extends Model
{

    protected $table = 'transaction_invoices';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'transaction_id',
        'invoice_id'
    ];

    public function transaction(){
        return $this->belongsTo('App\Models\Transaction');
    }

    public function invoice(){
        return $this->belongsTo('App\Models\Invoice','invoice_id');
    }

}