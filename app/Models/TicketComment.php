<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class TicketComment extends Model
{

    protected $table = 'ticket_comment';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'ticket_id',
        'comment',
        'client_id',
        'staff_id',
        'image',
    ];

    public function ticket(){
        return $this->belongsTo('App\Models\Ticket','ticket_id');
    }

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function staff(){
        return $this->belongsTo('App\Models\Staff','staff_id');
    }


}