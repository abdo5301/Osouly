<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Spatie\Activitylog\Traits\LogsActivity;
class Notification extends Model
{

    protected $table = 'notifications';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];



    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'title',
        'read_at',
        'data',
        'client_id',
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }


}