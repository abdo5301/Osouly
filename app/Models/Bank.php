<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Bank extends Model
{

    protected $table = 'banks';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


//    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'bank_code',
        'name_ar',
        'name_en',
    ];

    public function branches(){
        return $this->hasMany('App\Models\BankBranch','bank_code','bank_code');
    }

}