<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class ClientJob extends Model
{

    protected $table = 'client_jobs';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'job_title',
        'company_name',
        'from_date',
        'to_date',
        'present',
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }


}