<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Reminder extends Model
{


    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'reminder';
    public $timestamps = true;


    protected $dates = ['date_time','created_at','updated_at'];
    protected $fillable = [
        'staff_id',
        'sign_type',
        'sign_id',
        'date_time',
        'comment',
        'is_notified'
    ];

    public function staff(){
        return $this->belongsTo('App\Models\Staff','staff_id');
    }


    public function sign(){
        return $this->morphTo();
    }

}