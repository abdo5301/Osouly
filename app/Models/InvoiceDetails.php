<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class InvoiceDetails extends Model
{

    protected $table = 'invoice_details';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'invoice_id',
        'property_due_id',
        'value',
        'date',
        'paid_date',
        'paid_transaction_id',
        'paid_client_id',
    ];


    public function invoice(){
        return $this->belongsTo('App\Models\Invoice','invoice_id');
    }

    public function property_dues(){
        return $this->belongsTo('App\Models\PropertyDues','property_due_id');
    }


    public function paid_client(){
        return $this->belongsTo('App\Models\Client','paid_client_id');
    }

    public function comments(){
        return $this->hasMany('App\Models\InvoiceDetailsComments','invoice_detail_id');
    }

    public function payments(){
        return $this->morphMany('App\Models\Payments','sign');
    }



}