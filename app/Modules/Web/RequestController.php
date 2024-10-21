<?php

namespace App\Modules\Web;

use App\Models\PropertyViews;
use App\Models\Request as RequestModal;
use App\Models\Property;

class RequestController extends WebController
{
    public function request($slug){
        $request = RequestModal::where('sharing_slug',$slug)
            ->where('sharing_until','>',date('Y-m-d H:i:s'))
            ->firstOrFail();

        $request->increment('sharing_views');

        $this->viewData['result'] = $request;

        return $this->view('request',$this->viewData);

    }

    public function requestProperty($slug,$id){
        $request = RequestModal::where('sharing_slug',$slug)
            ->where('sharing_until','>',date('Y-m-d H:i:s'))
            ->firstOrFail();

        $propertiesIDs = array_column($request->property()->get(['properties.id'])->toArray(),'id');

        if(!in_array($id,$propertiesIDs)) abort(404);

        $property = Property::findOrFail($id);

        $checkViews = PropertyViews::where('request_id',$request->id)->where('property_id',$property->id)->first();
        if($checkViews){
            $checkViews->increment('views');
        }else{
            PropertyViews::create([
                'request_id'    => $request->id,
                'property_id'   => $property->id,
                'views'         => 1
            ]);
        }

        $this->viewData['result'] = $property;

        return $this->view('property',$this->viewData);

    }

}
