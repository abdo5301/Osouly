<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class IncomeReason extends Model
{

    protected $table = 'income_reasons';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
    ];


    function pay(){
      $this->morphMany('App\Models\Pay','sign')->orderByDesc('id');
    }





}