<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Contactus extends Model
{

    protected $table = 'contactus';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'subject',
        'message',
        'replay',
        'is_read',
        'client_id',
        'ticket_id',
    ];


    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function Ticket(){
        return $this->belongsTo('App\Models\Ticket','ticket_id');
    }



}