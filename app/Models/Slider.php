<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Slider extends Model
{

    protected $table = 'slider';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'video_url',
        'type',
        'sort',
        'image',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'url',
        'status',
    ];




}