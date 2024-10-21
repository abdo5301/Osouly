<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Invoice extends Model
{

    protected $table = 'invoices';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'property_id',
        'contract_id',
        'property_due_id',
        'installment_id',
        'transaction_id',
        'amount',
        'commission',
        'date',
        'notes',
        'status',
        'print_code',
    ];


    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function property_due(){
        return $this->belongsTo('App\Models\PropertyDues','property_due_id');
    }

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

    public function installment(){
        return $this->belongsTo('App\Models\Installment','installment_id');
    }

    public function Transaction(){
        return $this->hasMany('App\Models\TransactionInvoices','invoice_id');
    }



}