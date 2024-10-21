<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class BankBranch extends Model
{

    protected $table = 'banks_branches';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


//    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'bank_code',
        'branch_code',
        'name_ar',
        'name_en',
    ];

    public function bank(){
        return $this->belongsTo('App\Models\Bank','bank_code','bank_code');
    }




}