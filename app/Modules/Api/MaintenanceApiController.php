<?php

namespace App\Modules\Api;



use App\Models\Maintenance;
use App\Models\MaintenanceCategory;
use App\Modules\Api\Transformers\HomeTransformer;

use App\Modules\Api\Transformers\MaintenanceTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MaintenanceApiController extends ApiController
{

    public function __construct(){
        $this->middleware(['auth:api']);

    }

    public function index(Request $request){

        $main = Auth::user()->maintenance()->with(['category']);

        whereBetween($main, 'DATE(maintenance.created_at)', $request->date1, $request->date2);

        if($request->id){
            $main->where('id',$request->id);
        }
        if($request->property_id){
            $main->where('property_id',$request->property_id);
        }
        if($request->category_id){
            $main->where('maintenance_category_id',$request->category_id);
        }

        $main = $main->paginate();

        if($main->isEmpty()){
            $data['maintenance'] = (object)[];
        }else {
            $mainTransformer = new MaintenanceTransformer();
            $data['maintenance'] = $mainTransformer->transformCollection($main->toArray(),lang());

        }
        return $this->success('Done',$data);



    }

    public function category(Request $request){

        $categories = MaintenanceCategory::select('id','name_'.lang().' as name');
        if($request->id){
            $categories->where('parent_id',$request->id);
        }else{
            $categories->where('parent_id',0);
        }
        $categories = $categories->get();
        if($categories->isEmpty()){
            $data['categories'] = (object)[];
        }else{
            $data['categories'] = $categories;
        }

        return $this->success('Done',$data);
    }

    public function add(Request $request){

        $input = $request->only('property_id','category_id','priority','notes','type',
            'date','total_work','total_item','work_details','item_details');
        $validator = Validator::make($input, [
            'property_id' => 'required|exists:properties,id',
            'category_id' => 'required|exists:maintenance_categories,id',
            'priority' => 'required|in:sample,medium,urgent',
            'notes' => 'required',
            'date' => 'date',
            'type' => 'required|in:total,details',
            'total_work' => 'nullable',
            'total_item' => 'nullable',
            'work_details' => 'nullable',
            'item_details' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $property = Auth::user()->property()->where('id',$input['property_id'])->first();

        if(!$property){
            return $this->fail(__('Not Exists'));
        }

        $input['client_id'] = Auth::id();
        $input['maintenance_category_id'] = $input['category_id'];
        $main = Maintenance::create($input);

        if($main){
            return $this->success(__('Done'));
        }else{
            return $this->fail('Cannot add maintenance now, please try again later');
        }

    }

    public function show(Request $request){

        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:maintenance,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $main = Auth::user()->maintenance()->select('maintenance.*','maintenance_categories.name_'.lang().' as category_name')
            ->where('maintenance.id',$input['id'])
            ->join('maintenance_categories','maintenance_categories.id',
                'maintenance.maintenance_category_id')->first();

        if(!$main){
            return $this->fail(__('Not Exists'));
        }else{

                $parent_category = MaintenanceCategory::find($main->maintenance_category_id);
                if(!$parent_category->parent){
                    $main['parent_id']=0;
                    $main['parent_name']='';
                }else{
                    $main['parent_id']=$parent_category->parent->id;
                    $main['parent_name']=$parent_category->parent->{'name_'.lang()};
                }
            return $this->success('Done',$main);
        }

    }

    public function update(Request $request){
        $input = $request->only('id','property_id','category_id','priority','notes','status','type',
            'date','total_work','total_item','work_details','item_details');
        $validator = Validator::make($input, [
            'id' => 'required|exists:maintenance,id',
            'category_id' => 'required|exists:maintenance_categories,id',
            'status' => 'required|in:open,inprogress,done,cancel',
            'priority' => 'required|in:sample,medium,urgent',
            'notes' => 'required',
            'date' => 'date',
            'type' => 'required|in:total,details',
            'total_work' => 'nullable',
            'total_item' => 'nullable',
            'work_details' => 'nullable',
            'item_details' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $main = Auth::user()->maintenance()->where('id',$input['id'])->with(['category'])->first();

        $input['maintenance_category_id'] = $input['category_id'];

        if(!$main){
            return $this->fail(__('Not Exists'));
        }else{
           if($main->update($input)) {
               return $this->success('Updated');
           }else{
               return $this->fail(__('Cannot update Now,please try again later'));
           }
        }

    }

    public function delete(Request $request){
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:maintenance,id',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

          $main = Auth::user()->maintenance()->where('id',$input['id'])->first();

        if(!$main){
            return $this->fail(__('Not Exists'));
        }else{
            $main->delete();
            return $this->success(__('Removed from maintenance'));
        }




    }





}