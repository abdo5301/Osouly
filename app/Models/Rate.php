<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Rate extends Model
{

    protected $table = 'rate';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'sign_type',
        'sign_id',
        'comment',
        'rate',
        'status',
    ];


    public function sign(){
        return $this->morphTo();
    }




}