<?php

namespace App\Modules\Api;


use App\Libs\AreasData;
use App\Models\Ads;
use App\Models\Area;
use App\Models\Bank;
use App\Models\ClientPackages;
use App\Models\Contactus;
use App\Models\Contract;
use App\Models\CreditTransactions;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Newsletter;
use App\Models\Page;
use App\Models\Pay;
use App\Models\Property;
use App\Models\PropertyAds;
use App\Models\PropertyDues;
use App\Models\PropertyFeatures;
use App\Models\PropertyType;
use App\Models\Purpose;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Transaction;
use App\Models\TransactionInvoices;
use App\Modules\Api\Transformers\HomeTransformer;
use App\Modules\Api\Transformers\PropertyTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spipu\Html2Pdf\Html2Pdf;


class HomeApiController extends ApiController
{

    public function __construct(){
        $this->middleware(['auth:api'])->except([
            'home','data','page','services','service_details','contactus','area','subscribe','pdf','check_promocode'
        ]);

    }

    function dashboard(Request $request)
    {

        $PropertyTransformer = new PropertyTransformer();


        // الاستقطاعات
        $dues_gov_date1 = date('Y-m-01');
        $dues_gov_date2 = date('Y-m-31');
        if ($request->dues_gov_date1)
            $dues_gov_date1 = $request->dues_gov_date1;
        if ($request->dues_gov_date2)
            $dues_gov_date2 = $request->dues_gov_date2;


        $properties_ids = Property::where('owner_id', Auth::id())->pluck('id');


        if (empty($properties_ids)) {
            $main_dues =  [];
        } else {
            $properties_dues_ids = PropertyDues::whereIn('property_id', $properties_ids)->pluck('id');


            if (empty($properties_ids)) {
                $main_dues = [];
            } else {
                $main_dues = Invoice::whereIn('property_due_id', $properties_dues_ids)
                    ->join('property_dues','property_dues.id','invoices.property_due_id')
                    ->join('dues','dues.id','property_dues.due_id');;
            }

        }

        if(empty($main_dues)){

            $dues_gov_total = 0;
            $dues_gov_paid = 0;
            $dues_gov_un_paid = 0;
            $dues_total =0;
            $dues_paid = 0;
            $dues_un_paid = 0;
        }else {


            // الاستقطاعات
            $dues_gov = clone $main_dues;
            $dues_gov = $dues_gov->where('dues.type', 'government');

            whereBetween($dues_gov, 'date', $dues_gov_date1, $dues_gov_date2);
            $dues_gov1 = clone $dues_gov;
            $dues_gov2 = clone $dues_gov;
            $dues_gov3 = clone $dues_gov;

            $dues_gov_total = $dues_gov1->sum('amount');
            $dues_gov_paid = $dues_gov2->where('invoices.status', 'paid')->sum('amount');
            $dues_gov_un_paid = $dues_gov3->where('invoices.status', 'unpaid')->sum('amount');

            //  الاستحقاقات
            $dues_date1 = date('Y-m-01');
            $dues_date2 = date('Y-m-31');
            if ($request->dues_date1){
                $dues_date1 = $request->dues_date1;
            }
            if ($request->dues_date2){
                $dues_date2 = $request->dues_date2;
            }



            $dues = clone $main_dues;
            $dues = $dues->where('dues.type', 'service');

             whereBetween($dues, 'date', $dues_date1, $dues_date2);

            $dues1 = clone $dues;
            $dues2 = clone $dues;
            $dues3 = clone $dues;
             $dues_total = $dues1->sum('amount');

            $dues_paid = $dues2->where('invoices.status', 'paid')->sum('amount');
            $dues_un_paid = $dues3->where('invoices.status', 'unpaid')->sum('amount');


        }

        ////////////////////////////صافى الايراد/////////////////////////////

        // الاستقطاعات للايرادات
        $profit_date1 = date('Y-m-01');
        $profit_date2 = date('Y-m-31');
        if($request->profit_date1)
            $profit_date1 = $request->profit_date1;
        if($request->profit_date2)
            $profit_date2 = $request->profit_date2;


        $dues_gov_profit = Auth::user()->invoices()->with(['property_due'=>function($q){
            $q->with(['dues'=>function($qq){
                $qq->where('type','government');
            }]);
        }]);
        whereBetween($dues_gov_profit,'date',$profit_date1,$profit_date2);
        $dues_gov_profit_total = $dues_gov_profit->sum('amount');
        $dues_gov_profit_paid = $dues_gov_profit->where('status','paid')->sum('amount');
        $dues_gov_profit_un_paid = $dues_gov_profit->where('status','unpaid')->sum('amount');

        //  الاستحقاقات


        $dues_profit = Auth::user()->invoices()->with(['property_due'=>function($q){
            $q->with(['dues']);
        }]);
        whereBetween($dues_profit,'date',$profit_date1,$profit_date2);
        $dues_profit_total = $dues_profit->sum('amount');
        $dues_profit_paid = $dues_profit->where('status','paid')->sum('amount');
        $dues_profit_un_paid = $dues_profit->where('status','unpaid')->sum('amount');

        $profit_total = $dues_profit_total - $dues_gov_profit_total;
        $profit_paid = $dues_gov_profit_paid - $dues_profit_paid;
        $profit_unpaid = $dues_gov_profit_un_paid - $dues_profit_un_paid;





        ////////////////////////////////صافى الايراد//////////////////////////


        $contracts_count = 0;

        $contracts = Contract::whereIn('property_id',Property::where('owner_id',Auth::id())->pluck('id'))->with('renter','property');
        whereBetween($contracts, 'DATE(contracts.date_to)', $request->contract_date_to1, $request->contract_date_to2);
        $contracts = $contracts->get();
        if($contracts->isEmpty()){
            $contracts = (Object)[];
        }else{
            $contracts_count = count($contracts->toArray());
            $contracts = $PropertyTransformer->transformCollection($contracts->toArray(),lang(),'contracts');
        }


        $prperty_count = Auth::user()->property()->count();
        $prpoerty_used = Auth::user()->property()->whereNotNull('renter_id')->count();
        if($prperty_count == 0)
            $property_perc = 0;
            else
             $property_perc = $prpoerty_used / $prperty_count * 100;

        //  late invoices
        $properties_ids = Property::where('owner_id',Auth::id())->pluck('id');


        $renter_not_paid = 0;
        $invoices_paid = 0;
        $paid_rent = 0;

        $invoices_not_paid_count = 0;
        if (empty($properties_ids)) {
            $invoices = (object)[];
        } else {
            $properties_dues_ids = PropertyDues::whereIn('property_id', $properties_ids);
            if ($request->due_id) {
                $properties_ids->where('due_id', $request->due_id);
            }
            $properties_dues_ids = $properties_dues_ids->pluck('id');

            if (empty($properties_ids)) {
                $invoices = (object)[];
            } else {
                $invoices = Invoice::whereIn('property_due_id', $properties_dues_ids)->with('client', 'property_due');
                $renter_not_paid = clone $invoices;
                $invoices_not_paid = clone $invoices;
                $invoices_paid = clone $invoices;
                $invoices_not_paid = $invoices_not_paid->where('status','unpaid')->orderBy('date','desc')->get();
                $invoices_not_paid_count = count($invoices_not_paid);
                $renter_not_paid = count($renter_not_paid->select('client_id')->where('status','unpaid')
                    ->groupBy('client_id')->get());

                $invoices_paid = $invoices_paid->where('status','paid')->count();
                $invoices = $invoices->get();
                if ($invoices->isEmpty()) {
                    $invoices = (object)[];
                } else {

                    $paid_rent = round($invoices_paid / count($invoices) * 100) ;
//                    $invoices = $PropertyTransformer->transformCollection($invoices->toArray(),lang(),'invoices_dashboard');
                }
                if ($invoices_not_paid->isEmpty()) {
                    $invoices_not_paid = (object)[];
                } else {

                    $invoices_not_paid = $PropertyTransformer->transformCollection($invoices_not_paid->toArray(),lang(),'invoices_dashboard');
                }

            }
        }

        //////////////////// الزياده السنويه //////////////////////
        $increases_count = 0;
        $increases = Contract::select( 'property_id', 'renter_id','increase_from','increase_value','price')
            ->whereIn('property_id',Property::where('owner_id',Auth::id())->pluck('id'))->where('status','active')
            ->with('renter');
        if($request->increase_date1){
            $increases->where('increase_from','>=',date('Y-m-d',strtotime('+'.$request->increase_date1.' months',strtotime(date('Y-m-d')))));
        }
        $increases = $increases->get();

        if($increases->isNotEmpty()){
            $increases_count = count($increases->toArray());
            $increases = $PropertyTransformer->transformCollection($increases->toArray(),lang(),'increases');
        }else{
            $increases = (Object)[];
        }




                $data = [
            'properties_perc' => $property_perc,
            'properties' => $prperty_count,
            'renters'=>     $prpoerty_used,
            'renter_not_paid'=>$renter_not_paid,
            'paid_rent'=>$paid_rent,
            'dues_gov'=>    ['total'=>$dues_gov_total,'paid'=>$dues_gov_paid,'unpaid'=>$dues_gov_un_paid],
            'dues'=>    ['total'=>$dues_total,'paid'=>$dues_paid,'unpaid'=>$dues_un_paid],
            'profit'=>    ['total'=>$profit_total,'paid'=>$profit_paid,'unpaid'=>$profit_unpaid],
            'contracts' =>$contracts,
            'contracts_counts' =>$contracts_count,
            'invoices' =>$invoices_not_paid,
            'late_invoices' =>$invoices_not_paid_count,
            'increases'=>$increases,
            'increases_count'=>$increases_count


        ];

        return $this->success(__('Data'),$data);

    }


    public function search_area(Request $request){






        $word = $request->word;

        $data = Area::where(function($query) use ($word) {
            $query->where('name_ar','LIKE','%'.$word.'%')
                ->orWhere('name_en','LIKE','%'.$word.'%');
        })->get([
            'id'
        ]);

        if($data->isEmpty()){
            return [];
        }

        $result = [];

        foreach ($data as $key => $value){
            $result[] = [
                'id'=> $value->id,
                'value'=> str_replace($word,$word,implode(' -> ',AreasData::getAreasUp($value->id,true) ))
            ];

            if(setting('area_select_type') == '2'){
                $areaDown = AreasData::getAreasDown($value->id);
                if(count($areaDown) > 1){
                    array_shift($areaDown);
                    foreach ($areaDown as $aK => $aV){
                        $result[] = [
                            'id'=> $aV,
                            'value'=> str_replace($word,$word,implode(' -> ',AreasData::getAreasUp($aV,true) ))
                        ];
                    }
                }
            }

        }

        return $this->success('Done', $result);
    }

    public function home()
    {



        $PropertyTransformer = new PropertyTransformer();
        $HomeTransformer = new HomeTransformer();

        $slider_mob = Slider::select('video_url', 'title_' . lang().' as title', 'description_' . lang().' as description', 'url','image')
             ->where('status', 'active')->where('type','main_mob')->get();
        $slider_web = Slider::select('video_url', 'title_' . lang().' as title', 'description_' . lang().' as description', 'url','image')
            ->where('status', 'active')->where('type','main_web')->get();

        $property_ads_ids = PropertyAds::block()->pluck('id');
        if(empty($property_ads_ids)){
            $property_ads = [];
        }else {
            $property_ads = Property::block()->whereIn('id', $property_ads_ids)->limit(10)->get();
        }

        $property = Property::block()->whereNotIn('id', $property_ads_ids)->limit(10)->get();
        $services = Service::block()->where('parent_id',0)->where('status','active')->limit(10)->get();


        $data = [
             'slider_mob'=>$HomeTransformer->transformCollection($slider_mob->toArray(),lang(),'slider'),
             'slider_web'=>$HomeTransformer->transformCollection($slider_web->toArray(),lang(),'slider'),
            'property'=>!empty($property->toArray())?$PropertyTransformer->transformCollection($property->toArray(),lang(),'block'):[],
            'property_ads'=>(!empty($property_ads->toArray()))?$PropertyTransformer->transformCollection($property_ads->toArray(),lang(),'block'):[],
            'services'=>$HomeTransformer->transformCollection($services->toArray(),lang(),'services'),
        ];
        $data['ads'] = Ads::select('title_'.lang().' as title', 'type', 'image', 'url', 'page')
            ->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'))
            ->where('page', 'home')->get();
        $data['about'] = Page::select('id', 'title_' . lang().' as title','content_' . lang().' as content',
            'meta_key_' . lang().' as meta_key', 'meta_description_' . lang().' as meta_description','video_url')->find(1);

        return $this->success('Done', $data);

    }

    public function data()
    {
        $HomeTransformer = new HomeTransformer();
        $PropertyTransformer = new PropertyTransformer();
        $property_type = PropertyType::select('id', 'name_' . lang().' as name','image')->get();
        $data['property_type'] =  $PropertyTransformer->transformCollection($property_type->toArray(),lang(),'type');
        $data['property_purpose'] =  Purpose::select('id','name_'.lang().' as name')->get();
        $data['services'] = Service::where('parent_id',0)->get(['id', 'title_' . lang().' as title', 'slug_' . lang().' as slug']);
        $data['pages'] = Page::orderBy('sort','asc')->get(['id', 'title_' . lang().' as title', 'slug_' . lang().' as slug']);
         $data['countries'] = Area::select('id','name_'.lang().' as name')->where('area_type_id',1)->orderBy('name')->get() ;
         $data['governments'] = Area::select('id','name_'.lang().' as name')->where('area_type_id',2)->orderBy('name')->get() ;
         $data['user_permissions'] = user_permission_data();;

        $data['social_links'] = [
            'facebook'=>setting('facebook'),
            'youtube'=>setting('youtube'),
            'twitter'=>setting('twitter'),
            'email'=>setting('company_email'),
            'instagram'=>setting('instagram'),
            'linkedin'=>setting('linkedin'),
            'address'=>setting('company_address'),
            'mobile'=>setting('company_mobile'),
            'location'=>setting('location')
        ];
        $data['bank'] = Bank::select('bank_code','name_'.lang().' as name')->get();
        $data['property_features'] = PropertyFeatures::select('id','name_'.lang().' as name')->get();
        $slider = Slider::select( 'title_' . lang().' as title', 'description_' . lang().' as description','image')
            ->where('status', 'active')->where('type','board')->get();
        $data['board']  = [];
        if($slider->isNotEmpty()){
            $data['board'] = $HomeTransformer->transformCollection($slider->toArray(),lang(),'board') ;
        }

        $data['footer_about'] = setting('footer_about_'.lang()) ;
        return $this->success('Done', $data);
    }

    public function bank_branchs(Request $request){
        $input = $request->only('bank_code');

        $validator = Validator::make($input, [
            'bank_code' => 'string|required|exists:banks,bank_code'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $bank = Bank::where('bank_code',$input['bank_code'])->first();
        $branches = $bank->branches()->select('branch_code','name_'.lang().' as name')->get();

        if($branches->isEmpty()){
            $data['branches'] = (object)[];
        }

        return $this->success('Data',$branches);

    }



    public function page(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:pages,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $HomeTransformer = new HomeTransformer();
        $page = Page::block()->find($request->id);
        $data['page'] = $HomeTransformer->page($page,lang());
        if($request->id == 1 && !empty($page->added_paragraphs)){
            $added_paragraphs = json_decode($page->added_paragraphs);
            $data['about_list'] = [
                [
                'title' =>$added_paragraphs[0]->{'title_'.lang()},
                 'image' =>'http://test.osouly.com/public/osouly/images/trust.svg',
                 'description' =>$added_paragraphs[0]->{'content_'.lang()},
                    ],
                [
                    'title' =>$added_paragraphs[1]->{'title_'.lang()},
                    'image' =>'http://test.osouly.com/public/osouly/images/rental.svg',
                    'description' =>$added_paragraphs[1]->{'content_'.lang()},
                ],
                [
                    'title' =>$added_paragraphs[2]->{'title_'.lang()},
                    'image' =>'http://test.osouly.com/public/osouly/images/rent.svg',
                    'description' =>$added_paragraphs[2]->{'content_'.lang()},
                ]
            ];
        }
        return $this->success('Done', $data);
    }

    public function services()
    {

        $HomeTransformer = new HomeTransformer();
        $data= $HomeTransformer->transformCollection(Service::block()->paginate()->toArray(),lang(),'services');
        return $this->success('Done', $data);
    }

    public function service_details(Request $request)
    {

        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:services,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $service = Service::with('packages','images')->find($request->id);

        $HomeTransformer = new HomeTransformer();

        return $this->success('Done',$HomeTransformer->service_details($service->toArray(),lang()) );

    }


    public function promocode(Request $request)
    {
        $input = $request->only('service_id', 'promocode');

        $validator = Validator::make($input, [
            'service_id' => 'int|required|exists:services,id',
            'promocode' => 'int|required|exists:services,discount_code'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $service = Service::where('discount_code', $input['promocode'])->where('id', $input['service_id'])->first();
        if (!$service) {
            return $this->fail(__('Code is Invaild'));
        }

        if ((strtotime(date('Y-m-d')) > strtotime($service['discount_code_from']) && strtotime(date('Y-m-d')) < strtotime($service['discount_code_to']))) {
            return $this->success(__('valid Code'));
        } else {
            return $this->fail(__('Code is Expire'));
        }
    }
    public function contactus(Request $request)
    {
        $input = $request->only('name','email','mobile','subject','message');

        $validator = Validator::make($input, [
            'name' => 'string|required',
            'email' => 'string|email|required',
            'mobile' => 'string|required',
            'subject' => 'string|required',
            'message' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $send =Contactus::create($input);
        if($send){
            return $this->success(__('Message Sent'));
        }else{
            return $this->fail(__('Please Try Again later'));
        }

    }

    public function area(Request $request){
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:areas,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $child_areas = Area::where('parent_id',$input['id'])->select('id','name_'.lang().' as name')->get();

        return $this->success('Done', $child_areas);
    }

    public function subscribe(Request $request){

        $input = $request->only('email');

        $validator = Validator::make($input, [
            'email' =>  'string|email|required',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $check = Newsletter::where('email',$input['email'])->first();

        if($check){
            return $this->fail('Email Already Subscribed');
        }

        $Subscribe = Newsletter::create(['email'=>$input['email']]);
        if($Subscribe){
            return $this->success('Email subscribe successfully');
        }else{
            return $this->fail('Please Try again later');
        }

    }

    public function update_token(Request $request){

        $input = $request->only('token');

        $validator = Validator::make($input, [
            'token' =>  'string|required',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        Auth::user()->update(['token'=>$input['token']]);
        return $this->success('Done');

    }

    function checkout(Request $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'type' => 'string|required|in:service,invoice',
            'id' => 'string|required'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        if($request->type == 'service'){
            $service = Service::find($request->id);

            if(!$service){
                return $this->fail('Invalid Service ID');
            }
            $service_price = $this->calc_service_price($service,null);
            $data[]= ['id'=>$service->id,'title'=>$service->title_ar,'price'=>amount($service_price)];
            if(!empty(setting('plus_to_pay'))) {
                $data[] = ['id' => '', 'title' => 'مصاريف تحويل', 'price' => amount(setting('plus_to_pay'))];
            }

            $total = $this->total_amount($service_price);
            return $this->success('data',['total'=>amount($total),'data'=>$data]);

        }else{
            $invoice_ids = explode(',',$request->id);
            $data = [];
            $total=0;
            foreach ($invoice_ids as $invoice_id){
                $invoice = Invoice::where('id',$invoice_id)->where('client_id',Auth::id())->first();
                if(!$invoice){
                    continue;
                }
                $data[]=['id'=>$invoice_id,'title'=>@$invoice->property_due->name,'price'=>amount($invoice->amount),'property_id'=>$invoice->property_id,'date'=>$invoice->date];
                $total += $invoice->amount;
            }
            if(!empty(setting('plus_to_pay'))) {
                $data[] = ['id' => '', 'title' => 'مصاريف تحويل', 'price' => amount(setting('plus_to_pay'))];
            }
            $total = $this->total_amount($total);
            return $this->success('data',['total'=>amount($total),'data'=>$data]);
        }

    }

        function init_pay(Request $request){

        $input = $request->all();

        $validator = Validator::make($input, [
            'type' =>  'string|required|in:service,invoice',
            'id' =>  'string|required',
            'promocode' => 'string|nullable|exists:services,discount_code'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }



        if($request->type == 'invoice'){
            $invoice_ids = explode(',',$request->id);
            $total_invoices = 0;
            $vaild_invoices = [];
            foreach ($invoice_ids as $invoice_id){
                $invoice = Invoice::where('id',$invoice_id)->where('client_id',Auth::id())->first();
                if(!$invoice){
                    continue;
                }
                $vaild_invoices[] = $invoice;
                $total_amount = $invoice->amount;
                $total_invoices +=$total_amount;
            }

            $transaction = Transaction::create([
                'payment_method_id'=>1,
                'client_id'=>Auth::id(),
                'invoice_id'=>'',
                'type'=>'invoice',
                'status'=>'pending',
                'amount'=> $total_invoices,
                'total_amount'=> $this->total_amount($total_invoices),
                'notes'=> 'دفع فواتير'
            ]);

            foreach ($vaild_invoices as $key=> $one ){
            TransactionInvoices::create(['transaction_id'=>$transaction->id,'invoice_id'=>$one->id]);
            }
            $total_amount = $this->total_amount($total_invoices);
         }else{
            $service = Service::find($request->id);

            if(!$service){
                return $this->fail('Invalid Service ID');
            }
                if(!empty($input['promocode'])){
                    $service_price = $this->calc_service_price($service,$input['promocode']);
                }else{
                    $service_price = $this->calc_service_price($service,null);
                }
                $total_amount = $this->total_amount($service_price);
             $transaction = Transaction::create([
                'payment_method_id'=>1,
                'client_id'=>Auth::id(),
                'service_id'=>$service->id,
                 'type'=>'service',
                'status'=>'pending',
                'amount'=>$total_amount,
                'total_amount'=> $service->price,
                'notes'=>$service->title_ar
            ]);
        }

        $result = $this->startSession('osouly'.$transaction->id,$total_amount);
        $result = (object)$result;
        $transaction->update(['session_id'=>$result->id,'version'=>$result->version]);

        return $this->success('Done',['order_id'=>'osouly'.$transaction->id]);

    }

    function calc_service_price($service,$promocode=null){


        $price = $service->price;

        if(!empty($service->offer)){
            $price = $service->offer;
        }

        if(  (strtotime(date('Y-m-d')) > strtotime($service['discount_from']) &&  strtotime(date('Y-m-d')) < strtotime($service['discount_to']) ) && !empty($service['discount_value'])){
            $price = ($service['discount_type'] == 'fixed')?$service['price']-$service['discount_value']:$service['price'] -  (double)($service['price']/100 *$service['discount_value']);
        }


        if(!empty($promocode) && $service->discount_code ==  $promocode){

            if ((strtotime(date('Y-m-d')) > strtotime($service['discount_code_from']) && strtotime(date('Y-m-d')) < strtotime($service['discount_code_to']))) {
            $price -= $service->discount_code_value;
            }
            }


        return $price;



    }

    function total_amount($amount){
        return $amount + setting('plus_to_pay');
    }

    public function startSession($id,$amount){


        $client = new \GuzzleHttp\Client();
        $result = $client->request('POST', 'https://test-nbe.gateway.mastercard.com/api/rest/version/57/merchant/EGPTEST1/session',[
            'auth'=> [
                'merchant.EGPTEST1',
                '61422445f6c0f954e24c7bd8216ceedf'
            ],
            'json'=> [
                'apiOperation'  => 'CREATE_CHECKOUT_SESSION',
                'interaction'   => [
                    'operation'=> 'PURCHASE'
                ],
                'order'         => [
                    'id'=> $id,
                    'amount'=> $amount,
                    'currency'=> 'EGP'
                ]
            ]
        ]);


        if($result->getBody()){
            $response = json_decode($result->getBody());
            if($response->result == 'SUCCESS'){
                return [
                    'status'=> true,
                    'id'=> $response->session->id,
                    'version'=> $response->session->version
                ];
            }
        }

        return [
            'status'=> false
        ];

    }




    public function pdf(){
        $html2pdf = new Html2Pdf('L','A4');
//        return  view('invoice');
        $view = view('invoice');
        $html2pdf->writeHTML($view);
           $html2pdf->output('myPdf.pdf'); // Generate and load the PDF in the browser.

      // $html2pdf->output('myPdf.pdf', 'D'); // Generate the PDF execution and force download immediately.
    }


    public function subscribes(){

        $data = Auth::user()->client_packeges()->select(
            'client_packages.id',
            'client_packages.service_id',
            'services.title_'.lang().' as title',
            'client_packages.transaction_id',
            'client_packages.service_type',
            'client_packages.service_count',
             'client_packages.status',
             'client_packages.rest_count',
            'client_packages.date_from',
            'client_packages.date_to',
            'client_packages.count_per_day')
            ->join('services','services.id','client_packages.service_id')
            ->get();

        return $this->success('data',$data);


    }

    public function add_property_to_ads(Request $request){
        $input = $request->only('client_package_id','property_id');

        $validator = Validator::make($input, [
            'client_package_id' =>  'int|required|exists:client_packages,id',
            'property_id' =>  'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $packege = Auth::user()->client_packeges()->where('id',$input['client_package_id'])->first();
        if(!$packege){
            return $this->fail(__('Invalid Package'));
        }

        $property = Auth::user()->property()->where('id',$input['property_id'])->first();
        if(!$property){
            return $this->fail(__('Invalid property'));
        }

        if($packege->status != 'active'){
            return $this->fail(__('Invalid Package status'));
        }
        if($packege->date_to < date('Y-m-d')){
            return $this->fail(__('Package Expired'));
        }

        if($packege->rest_count <= 0){
            return $this->fail(__('Package is finish'));
        }

        PropertyAds::create([
            'property_id'=>$input['property_id'],
            'start_date'=>date('Y-m-d'),
            'end_date'=>date('Y-m-d',strtotime(date('Y-m-d'). ' + '.$packege->rest_count.' days')),
            'client_package_id'=>Auth::id(),
            'created_by'=>0,
        ]);

        $packege->update(['rest_count'=>$packege->rest_count - 1]);

        return $this->success(__('Property Added To Ads Successfully'));
    }

    public function check_promocode(Request $request)
    {
        $input = $request->only('promocode','service_id');

        $validator = Validator::make($input, [
            'service_id' =>  'int|required|exists:services,id',
            'promocode' => 'string|nullable|exists:services,discount_code'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $service = Service::find($input['service_id']);
     //   pda($service);
        if(!empty($input['promocode']) && $service->discount_code ==  $input['promocode']){
            if ((strtotime(date('Y-m-d')) > strtotime($service['discount_code_from']) && strtotime(date('Y-m-d')) < strtotime($service['discount_code_to']))) {

                 return $this->success('Vaild Promocode',['discount_value'=>amount($service->discount_code_value)]);
            }
        }
        return $this->fail('Invalid promocode');
    }


}