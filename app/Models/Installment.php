<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Installment extends Model
{

    protected $table = 'installments';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'owner_id',
        'renter_id',
        'amount',
        'invoice_id',
        'due_date',
    ];


    public function owner(){
        return $this->belongsTo('App\Models\Client','owner_id');
    }

    public function renter(){
        return $this->belongsTo('App\Models\Client','renter_id');
    }

    public function invoice(){
        return $this->hasMany('App\Models\Invoice','installment_id');
    }





}