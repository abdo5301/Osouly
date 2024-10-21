<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class FacilityCompanies extends Model
{

    protected $table = 'facility_companies';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'area_ids',
        'due_id',
        'company_pay_id',
    ];

    public function dues(){
        return $this->belongsTo('App\Models\Dues','due_id');
    }


    public function facilities(){
        return $this->hasMany('App\Models\PropertyFacilities','facility_company_id');
    }



}