<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class ContractTemplate extends Model
{

    protected $table = 'contract_templates';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'staff_id',
        'template_content',
    ];

    public function owner(){
        return $this->belongsTo('App\Models\Client','owner_id');
    }


    public function staff(){
        return $this->belongsTo('App\Models\Staff','staff_id');
    }





}