<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Call extends Model
{

    protected $table = 'calls';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'call_purpose_id',
        'call_status_id',
        'type',
        'description',
        'sign_type',
        'sign_id',
        'parent_id',
        'created_by_staff_id'
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function call_purpose(){
        return $this->belongsTo('App\Models\CallPurpose','call_purpose_id');
    }

    public function call_status(){
        return $this->belongsTo('App\Models\CallStatus','call_status_id');
    }

    public function sign(){
        return $this->morphTo();
    }

    public function parent(){
        return $this->belongsTo('App\Models\Call','parent_id');
    }

    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function reminder(){
        return $this->morphMany('App\Models\Reminder','sign');
    }

}