<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class CallStatus extends Model
{

    protected $table = 'call_status';
    public $timestamps = true;

    use LogsActivity;
    protected static $logAttributes = ['*'];

    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name_ar',
        'name_en',
        'color',
        'created_by_staff_id'
    ];

    public function staff(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

}