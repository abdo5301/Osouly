<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Newsletter extends Model
{

    protected $table = 'newsletters';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    protected $dates = ['created_at,updated_at'];
    protected $fillable = array('email');

}