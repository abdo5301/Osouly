<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class InvoiceDetailsComments extends Model
{

    protected $table = 'invoice_details_comments';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'invoice_detail_id',
        'client_id',
        'comment',
    ];

    public function invoice_details(){
        return $this->belongsTo('App\Models\InvoiceDetails','invoice_detail_id');
    }

    public function added_by(){
        return $this->belongsTo('App\Models\Client','client_id');
    }



}