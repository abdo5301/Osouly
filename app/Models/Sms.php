<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Sms extends Model
{

    protected $table = 'sms';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'status',
        'response',
        'content'
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }


}