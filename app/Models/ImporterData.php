<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ImporterData extends Model
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'importer_data';

    public $timestamps = true;

    protected $dates = ['created_at','updated_at'];

    protected $fillable = [
        'importer_id',
        'connector_id',
        'name',
        'price',
        'description',
        'space',
        'bed_rooms',
        'bath_room',
        'mobile',
        'owner_name',
        'property_id',
        'url',
        'staff_id',
        'status'
    ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff','staff_id');
    }

    public function importer()
    {
        return $this->belongsTo('App\Models\Importer','importer_id');
    }

    public static function requests($propertyTypeID,$purposeID,$areaID,$spaceFrom,$spaceTo,$priceFrom,$priceTo){
        return self::join('importer','importer.id','=','importer_data.importer_id')
            ->where('importer.property_type_id',$propertyTypeID)
            ->where('importer.purpose_id',$purposeID)
            ->whereIn('importer.area_id',$areaID)
            ->whereBetween('importer_data.space',[$spaceFrom,$spaceTo])
            ->whereBetween('importer_data.price',[$priceFrom,$priceTo])
            ->select([
                'importer_data.id',
                'importer_data.name',
                'importer_data.price',
                'importer_data.space',
                'importer_data.bed_rooms',
                'importer_data.bath_room',
                'importer_data.owner_name',
                'importer_data.mobile',
                'importer_data.importer_id',
            ]);
    }

}