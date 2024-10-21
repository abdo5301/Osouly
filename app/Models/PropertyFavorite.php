<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class PropertyFavorite extends Model
{

    protected $table = 'property_favorite';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'property_id',
        'client_id',

    ];

    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }


}