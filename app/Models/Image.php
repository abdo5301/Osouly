<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Image extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'images';
    public $timestamps = true;


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'sign_type',
        'sign_id',
        'custom_key',
        'path',
        'image_name',
        'notes',
    ];

    public function sign(){
        return $this->morphTo();
    }


}