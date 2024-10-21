<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class PropertyFacilities extends Model
{

    protected $table = 'property_facilities';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_id',
        'facility_company_id',
        'number',
    ];

    public function property(){
        return $this->belongsTo('App\Models\Property','property_id');
    }

    public function company(){
        return $this->belongsTo('App\Models\FacilityCompanies','facility_company_id');
    }



}