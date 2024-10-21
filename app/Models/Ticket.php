<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Ticket extends Model
{

    protected $table = 'tickets';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'title',
        'status',
        'client_id',
    ];

    public function comments(){
        return $this->hasMany('App\Models\TicketComment','ticket_id')->orderByDesc('id');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function contact_us()
    {
        return $this->hasOne('App\Models\Contactus', 'ticket_id');

    }

    }