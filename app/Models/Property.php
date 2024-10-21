<?php

namespace App\Models;

use App\Libs\AreasData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use function foo\func;

class Property extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'properties';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['call_update','created_at','updated_at','deleted_at'];
    protected $fillable = [
        'status',
        'publish',
        'property_type_id',
        'purpose_id',
        'data_source_id',
        'building_number',
        'flat_number',
        'floor',
        'description',
        'contract_period',
        'contract_type',
        'insurance_price',
        'deposit_rent',
        'price',
        'space',
        'address',
        'owner_id',
        'renter_id',
        'features',
        'build_type',
        'mobile',
        'street_name',
        'country_id',
        'city_id',
        'government_id',
        'area_type',
        'local_id',//instead of area_id
        'mogawra',
        'room_number',
        'bathroom_number',
        'title',
        'views',
        'latitude',
        'longitude',
        'video_url',
        'importer_data_id',
        'meta_key',
        'meta_description',
        'slug',

    ];


    public static function owner_table(){

        return static::where('owner_id',Auth::id())->with(['images','property_type',
                'contracts'=>function($q){
                $q->where('status','active')
                    ->where('date_from','<=',date('Y-m-d'))
                    ->where('date_to','>=',date('Y-m-d'))->with('renter');
                },'government'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'city'=>function($q){
                $q->select('id','name_'.lang().' as name');
            }]);
    }

    public static function renter_table(){
        return static::where('renter_id',Auth::id())->with(['images','owner',
          'government'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'city'=>function($q){
                $q->select('id','name_'.lang().' as name');
            }]);
    }




    public static function block(){

       return static::select('id','title','room_number','bathroom_number','space','price','created_at','slug','government_id','city_id','purpose_id')
             ->with(['property_type'=>function($q){
                 $q->select('id','name_'.lang().' as name');
             },'government'=>function($q){
                 $q->select('id','name_'.lang().' as name');
             },'city'=>function($q){
                 $q->select('id','name_'.lang().' as name');
             },'purpose'=>function($q){
                 $q->select('id','name_'.lang().' as name');
             },'images'])->where('publish',1)->where('status','for_rent');
    }

    public static function details(){
        return static::select('*')
            ->with(['images','property_type'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'government'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'country'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'area'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'city'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'local'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'area'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'owner'=>function($q){
                $q->select('id','first_name','second_name');
            },'property_type'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'purpose'=>function($q){
                $q->select('id','name_'.lang().' as name');
            }])->where('publish',1)->where('status','for_rent')->with('owner');
    }

    public static function own_details(){
        return static::select('*')
            ->with(['images','property_type'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'government'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'country'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'area'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'city'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'local'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'area'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'owner'=>function($q){
                $q->select('id','first_name','second_name');
            },'property_type'=>function($q){
                $q->select('id','name_'.lang().' as name');
            },'purpose'=>function($q){
                $q->select('id','name_'.lang().' as name');
            }]);
    }



    public function country(){
        return $this->belongsTo('App\Models\Area','country_id');
    }

    public function city(){
        return $this->belongsTo('App\Models\Area','city_id');
    }

    public function local(){
        return $this->belongsTo('App\Models\Area','local_id');
    }

    public function government(){
        return $this->belongsTo('App\Models\Area','government_id');
    }

    public function dues(){
        return $this->hasMany('App\Models\PropertyDues','property_id');
    }

    public function ads(){
        return $this->hasMany('App\Models\PropertyAds','property_id');
    }

    public function rate(){
        return $this->morphMany('App\Models\Rate','sign')->orderByDesc('id');
    }


    public function facilities(){
        return $this->hasMany('App\Models\PropertyFacilities','property_id');
    }


    public function features(){
        return PropertyFeatures::select('name_'.lang().' as name')->whereIN('id',explode(',',$this->features));
    }


    public function contracts(){
        return $this->hasMany('App\Models\Contract','property_id')->orderBy('id','desc');;
    }

    public function requests(){
        return $this->hasMany('App\Models\Request','property_id');
    }


    public function images(){
        return $this->morphMany('App\Models\Image','sign')->orderByDesc('id');
    }

    public function property_type(){
        return $this->belongsTo('App\Models\PropertyType','property_type_id');
    }

    public function purpose(){
        return $this->belongsTo('App\Models\Purpose','purpose_id');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Invoice','property_id');
    }

    public function data_source(){
        return $this->belongsTo('App\Models\DataSource','data_source_id');
    }


    public function owner(){
       return $this->belongsTo('App\Models\Client','owner_id');
    }

    public function renter(){
        return $this->belongsTo('App\Models\Client','renter_id');
    }

    public function area(){
        return $this->belongsTo('App\Models\Area','local_id');
    }


    public function importer(){
        return $this->belongsTo('App\Models\Importer','importer_data_id');
    }


    public function calls(){
        return $this->morphMany('App\Models\Call','sign')
            ->select([
                'id',
                'client_id',
                'call_purpose_id',
                'call_status_id',
                'type',
                'description',
                'created_by_staff_id',
                'created_at'
            ])
            ->orderByDesc('id')
            ->with([
                'client',
                'call_purpose',
                'call_status',
                'staff'
            ]);
    }

    public function reminders(){
        return $this->morphMany('App\Models\Reminder','sign')
            ->orderByDesc('date_time')
            ->with([
                'staff'
            ]);
    }


}