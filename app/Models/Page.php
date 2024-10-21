<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Page extends Model
{

    protected $table = 'pages';
    public $timestamps = true;


    use LogsActivity;
    protected static $logAttributes = ['*'];


    use SoftDeletes;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'video_url',
        'added_paragraphs',
        'sort',
        'meta_key_ar',
        'meta_key_en',
        'meta_description_ar',
        'meta_description_en',
    ];


    public static function block(){
        return static::select('id', 'title_' . lang().' as title', 'content_' . lang().' as content','added_paragraphs',
            'slug_' . lang().' as slug', 'video_url', 'meta_key_' . lang().' as meta_key',
            'meta_description_' . lang().' as meta_description')->with('images');
    }



    public function images(){
        return $this->morphMany('App\Models\Image','sign')->orderByDesc('id');
    }



}