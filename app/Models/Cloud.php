<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AreaType;
use Spatie\Activitylog\Traits\LogsActivity;

class Cloud extends Model
{
    protected $table = 'cloud';
    public $timestamps = true;

    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'name',
        'database_name'
    ];
}
