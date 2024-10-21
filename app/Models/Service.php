<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Package;

class  Service extends Model
{

    protected $table = 'services';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'parent_id',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'price',
        'offer',
        'duration',
        'status',
        'slug_ar',
        'slug_en',
        'meta_key_ar',
        'meta_key_en',
        'discount_type',
        'discount_value',
        'discount_from',
        'discount_to',
        'type',
        'type_count',
        'properties_count',
        'discount_code',
        'discount_code_value',
        'discount_code_from',
        'discount_code_to',
        'percentage',
//        'subscribers_count',
//        'unsubscribers_count',
//        'subscribe_monthly',
//        'subscribe_from',
//        'subscribe_to',
    ];

    public static function block(){
        return static::select('id', 'title_' . lang().' as title', 'content_' . lang().' as content', 'slug_' . lang().' as slug',
            'meta_key_' . lang().' as meta_key', 'meta_description_' . lang().' as meta_description','price','offer','duration','type_count as count')
            ->with('images')->where('status','active');
    }




    public function images(){
        return $this->morphMany('App\Models\Image','sign');
    }

    public function packages(){
        return $this->hasMany('App\Models\Service','parent_id');
    }



}