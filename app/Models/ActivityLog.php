<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{

    protected $table = 'activity_log';
    public $timestamps = true;


    protected static $logAttributes = ['*'];

    protected $dates = ['created_at','updated_at'/*,'deleted_at'*/];
    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
        'ip',
        'user_agent',
        'url',
        'method',

    ];

    


}