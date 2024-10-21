<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Locker extends Model
{

    protected $table = 'lockers';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'amount',
    ];

    function pay(){
        $this->hasMany('App\Models\Pay','locker_id')->orderByDesc('id');
    }

    function income(){
        $this->hasMany('App\Models\Pay','locker_id')->where('sign_type','App\Models\IncomeReason')->orderByDesc('id');
    }

    function outcome(){
        $this->hasMany('App\Models\Pay','locker_id')->where('sign_type','App\Models\OutcomeReason')->orderByDesc('id');
    }



}