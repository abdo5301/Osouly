<?php

namespace App\Modules\Api;


use App\Models\Ads;
use App\Models\Area;
use App\Models\Client;
use App\Models\ClientPackages;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Dues;
use App\Models\FacilityCompanies;
use App\Models\Image;
use App\Models\Invoice;
use App\Models\PaymentMethods;
use App\Models\Property;
use App\Models\PropertyAds;
use App\Models\PropertyDues;
use App\Models\PropertyFacilities;
use App\Models\PropertyFavorite;
use App\Models\PropertyFeatures;
use App\Models\PropertyType;
use App\Models\Request as RequestModal;
use App\Models\Transaction;
use App\Modules\Api\Transformers\ClientTransformer;

use Illuminate\Http\Request;
use App\Modules\Api\Transformers\PropertyTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use Spipu\Html2Pdf\Html2Pdf;

class PropertyApiController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:api'])->except([
            'index', 'show','print_contract_file','print_invoice_file'
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $eloquentData = Property::block()->where('status', 'for_rent');

        whereBetween($eloquentData, 'DATE(properties.created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'properties.price', $request->price1, $request->price2);
        whereBetween($eloquentData, 'properties.insurance_price', $request->insurance_price1, $request->insurance_price2);
        whereBetween($eloquentData, 'properties.contract_period', $request->contract_period1, $request->contract_period2);
        whereBetween($eloquentData, 'properties.deposit_rent', $request->deposit_rent1, $request->deposit_rent2);
        whereBetween($eloquentData, 'properties.space', $request->space1, $request->space2);

        if ($request->id) {
            $eloquentData->where('properties.id', $request->id);
        }

        if ($request->property_type_id) {
            $eloquentData->where('properties.property_type_id', $request->property_type_id);
        }

        if ($request->purpose_id) {
            $eloquentData->where('properties.purpose_id', $request->purpose_id);
        }

        if ($request->building_number) {
            $eloquentData->where('properties.building_number', $request->building_number);
        }

        if ($request->flat_number) {
            $eloquentData->where('properties.flat_number', $request->flat_number);
        }

        if ($request->floor) {
            $eloquentData->where('properties.floor', $request->floor);
        }

        if ($request->title) {
            $eloquentData->where(function ($q) use ($request) {
                $q->where('properties.title', 'LIKE', '%' . $request->title . '%');
            });
        }

        if ($request->contract_type) {
            $eloquentData->where('properties.contract_type', $request->contract_type);
        }

        if ($request->address) {
            $eloquentData->where('properties.address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->owner_id) {
            $eloquentData->where('properties.owner_id', $request->owner_id);
        }

        if ($request->government_id) {
            $eloquentData->whereIn('properties.government_id', $request->government_id);
        }

        if ($request->city_id) {
            $eloquentData->whereIn('properties.city_id', $request->city_id);
        }

        if ($request->area_type) {
            $eloquentData->whereIn('properties.area_type', $request->area_type);
        }

        if ($request->local_id) {
            $eloquentData->whereIn('properties.local_id', $request->local_id);
        }

        if ($request->mogawra) {
            $eloquentData->whereIn('properties.mogawra', $request->mogawra);
        }

        if ($request->room_number) {
            $eloquentData->whereIn('properties.room_number', $request->room_number);
        }

        if ($request->bathroom_number) {
            $eloquentData->whereIn('properties.bathroom_number', $request->bathroom_number);
        }

        if ($request->features) {
            $eloquentData->whereRaw('FIND_IN_SET(?,features)', [$request->features]);
        }


        $properties = $eloquentData->orderby('id', 'desc')->paginate();

        $transformer = new PropertyTransformer();
        if ($properties->isEmpty()) {
            $data['property'] = [];
        } else {
            $transformer = new PropertyTransformer();
            $data['property'] = $transformer->transformCollection($properties->toArray(), lang(), 'block');
        }

        $property_ads_ids = PropertyAds::block()->pluck('id');
        if (empty($property_ads_ids)) {
            $data['property_ads'] = [];
        } else {
            $data['property_ads'] = Property::block()->whereIn('id', $property_ads_ids)->limit(5)->get();
        }
        $data['ads'] = Ads::select('title_' . lang() . ' as title', 'type', 'image', 'url', 'page')
            ->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))
            ->where('page', 'property_list')->get();

        return $this->success('Property Data', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $PropertyTransformer = new PropertyTransformer();
        $property_type = PropertyType::select('id', 'name_' . lang() . ' as name', 'image')->get();
        $data['property_type'] = $PropertyTransformer->transformCollection($property_type->toArray(), lang(), 'type');
        $data['property_features'] = PropertyFeatures::select('id', 'name_' . lang() . ' as name')->get();
        $data['countries'] = Area::select('id', 'name_' . lang() . ' as name')->where('area_type_id', 1)->orderBy('name')->get();
        $data['dues'] = Dues::get(['id', 'name']);

        $data['contract_type'] = [
            'day' => __('day'),
            'month' => __('month'),
            'year' => __('year'),
        ];

        $data['dues_duration'] = [
            'one_time' => __('one time'),
            'day' => __('day'),
            'month' => __('month'),
            'year' => __('year'),
        ];

        $data['building_type'] = [
            'tower' => __('tower'),
            'villa' => __('villa'),

        ];

        $data['area_type'] = [
            'hayi' => __('hayi'),
            'markaz' => __('markaz'),
            'qasm' => __('qasm')
        ];

        return $this->success('Property Data', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->only(
            'property_type_id', 'purpose_id', 'area_id', 'building_number', 'flat_number', 'building_type', 'local_id', 'images',
            'area_type', 'title', 'floor', 'features', 'description', 'contract_type', 'contract_period', 'insurance_price', 'deposit_rent', 'price', 'space', 'address',
            'street_name', 'country_id', 'government_id', 'city_id', 'mogawra', 'room_number', 'bathroom_number', 'meta_key', 'meta_description', 'mobile', 'longitude', 'video_url',
            'dues', 'dues_value', 'dues_duration', 'status', 'purpose_id'
        );

        $validator = Validator::make($input, [
            'property_type_id' => 'required|int|exists:property_types,id',
            'purpose_id' => 'int|exists:purposes,id',
            // 'area_id' => 'required|int|exists:areas,id',
            'purpose_id' => 'nullable',
            'local_id' => 'nullable',
            'building_number' => 'required|nullable|string',
            'flat_number' => 'required|nullable|string',
            'building_type' => 'required|string|in:villa,tower',
            'area_type' => 'required|string|in:hayi,markaz,qasm',
            'title' => 'required|nullable|string',
            'floor' => 'required|numeric',
            'features' => 'nullable|array',
            'features.*' => 'nullable|exists:property_features,id',
            'description' => 'nullable',
            'contract_type' => 'nullable',
            'contract_period' => 'nullable',
            'insurance_price' => 'nullable',
            'deposit_rent' => 'nullable',
            'price' => 'nullable',
            'space' => 'required',
            'address' => 'nullable|string',
            'street_name' => 'nullable|nullable|string',
            'country_id' => 'required|int|exists:areas,id',
            'government_id' => 'required|exists:areas,id',
            'city_id' => 'nullable',
            'mogawra' => 'nullable|int',
            'room_number' => 'required|nullable|int',
            'bathroom_number' => 'required|nullable|int',
            'meta_key' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'mobile' => 'required|nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'video_url' => 'nullable|string|url',
            'images.*' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'dues.*' => 'nullable|int|exists:dues,id',
            'dues_value.*' => 'nullable|numeric',
            'dues_duration.*' => 'nullable|string|in:one_time,day,month,year',
            'status' => 'required|string|in:for_rent,rented',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $client_package = ClientPackages::where(['client_id' =>Auth::id(), 'status' => 'active', 'service_type' => 'manage'])
            ->where('rest_count','!=',0)->first();
        if(!$client_package){
            return $this->fail(__('You must subscribe for any collect package first'));
        }
            $client_package->update(['rest_count'=>$client_package->rest_count-1]);


        $input['features'] = $request->features ? implode(',', $request->features) : null;
        $input['owner_id'] = Auth::id();
        $input['slug'] = str_slug($input['title']);

        $dues = isset($input['dues']) ? $input['dues'] : [];
        $dues_value = isset($input['dues_value']) ? $input['dues_value'] : [];
        $dues_duration = isset($input['dues_duration']) ? $input['dues_duration'] : [];

        unset($input['dues']);
        unset($input['dues_value']);
        unset($input['dues_duration']);
        $insertData = Property::create($input);
        if ($insertData) {
            //upload files and images
            $files = $request->allFiles();

            if (!empty($files['images'])) {
                $custom_key = md5(rand() . time());
                foreach ($files['images'] as $key => $val) {

                    $path = $val->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
                    Image::create([
                        'custom_key' => $custom_key,
                        'path' => $path,
                        'sign_id' => $insertData->id,
                        'sign_type' => 'App\Models\Property',
                        'image_name' => $key
                    ]);
                    addWaterMarker($path);
                }
            }
            if (!empty($dues)) {

                foreach ($dues as $key => $value) {
                    $due_data = Dues::find($dues[$key]);
                    $due_data = [
                        'property_id' => $insertData['id'],
                        'due_id' => $dues[$key],
                        'value' => $dues_value[$key],
                        'name' => $due_data->name,
                        'duration' => $dues_duration[$key],
                        'type' => 'renter'
                    ];

                    PropertyDues::create($due_data);
                }
            }


            return $this->success(__('Property added'), ['id' => $insertData['id']]);
        } else {
            return $this->fail(__('Error Please Try Again later'));
        }


    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function my_property_details(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $property = Property::own_details()->where('owner_id', Auth::id())->with('dues.dues')->where('id', $input['id'])->first();
        if (!$property) {
            return $this->fail('Invaild ID');
        }
        $PropertyTransformer = new PropertyTransformer();
        $data['property'] = $PropertyTransformer->my_details($property->toArray());


        // facilities
        $facilities = $property->facilities()->with('company')->get();
        if ($facilities->isEmpty()) {
            $data['facilities'] = (object)[];
        } else {
            $data['facilities'] = $PropertyTransformer->transformCollection($facilities->toArray(), lang(), 'facilities');
        }

        $facility_companies = FacilityCompanies::select('id', 'name')->with('dues')->get();

        $data['facility_companies'] = $PropertyTransformer->transformCollection($facility_companies->toArray(), lang(), 'facility_companies');

        return $this->success('Done', $data);
    }


    public function my_property_requests(Request $request)
    {

        $input = $request->only('property_id');

        $validator = Validator::make($input, [
            'property_id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $property = Auth::user()->property->find($input['property_id']);
        if (!$property) {
            return $this->fail('Invaild Property ID');
        }
        $requests = $property->requests()->with('renter');

        if ($request->id) {
            $requests->where('id', $request->id);
        }

        if ($request->status) {
            $requests->where('status', $request->status);
        }

        if ($request->renter_id) {
            $requests->where('renter_id', $request->renter_id);
        }

        $requests = $requests->paginate();

        if ($requests->isEmpty()) {
            $data['requests'] = (object)[];
        } else {

            $data['status'] = [
                'new' => __('new'),
                'pendding' => __('pendding'),
                'accept' => __('accept'),
                'reject' => __('reject'),
                'cancel' => __('cancel'),
            ];
            $requests_renters_ids = $property->requests()->pluck('renter_id');
            $data['renters'] = Client::select('id', 'first_name', 'second_name')->whereIn('id', $requests_renters_ids)->get();
            $PropertyTransformer = new PropertyTransformer();
            $data['requests'] = $PropertyTransformer->transformCollection($requests->toArray(), lang(), 'requests');
        }

        return $this->success('Done', $data);

    }

    public function my_property_invoices(Request $request)
    {

        $input = $request->only('property_id');

        $validator = Validator::make($input, [
            'property_id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $property = Auth::user()->property->find($input['property_id']);
        if (!$property) {
            return $this->fail('Invaild Property ID');
        }

        // invoices
        if ($property->dues->isEmpty()) {
            $data['invoices'] = (object)[];
        } else {

            $property_dues = $property->dues()->pluck('id');
            $invoices = Invoice::whereIn('property_due_id', $property_dues)->with(['client', 'property_due'])->orderBy('date','desc');
            $invoices_renters = clone $invoices;

            whereBetween($invoices, 'DATE(invoices.date)', $request->date1, $request->date2);

            if ($request->id) {
                $invoices->where('id', $request->id);
            }

            if ($request->status) {
                $invoices->where('status', $request->status);
            }

            if ($request->client_id) {
                $invoices->where('client_id', $request->client_id);
            }

            if ($request->amount) {
                $invoices->where('amount', $request->amount);
            }

            $invoices = $invoices->paginate();
            if ($invoices->isEmpty()) {
                $data['invoices'] = (object)[];
            } else {
                $invoices_renters_ids = $invoices_renters->pluck('client_id');
                $data['renters'] = Client::select('id', 'first_name', 'second_name')->whereIn('id', $invoices_renters_ids)->get();
                $PropertyTransformer = new PropertyTransformer();
                $data['invoices'] = $PropertyTransformer->transformCollection($invoices->toArray(), lang(), 'invoices');
            }
        }

        return $this->success('Done', $data);

    }

    public function my_property_dues(Request $request)
    {

        $input = $request->only('property_id');

        $validator = Validator::make($input, [
            'property_id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $property = Auth::user()->property->find($input['property_id']);
        if (!$property) {
            return $this->fail('Invaild Property ID');
        }

        // dues
        $dues = $property->dues()->with('dues');


        if ($request->id) {
            $dues->where('id', $request->id);
        }

        if ($request->due_id) {
            $dues->where('due_id', $request->due_id);
        }

        if ($request->duration) {
            $dues->where('duration', $request->duration);
        }

        if ($request->value) {
            $dues->where('value', $request->value);
        }

        if ($request->type) {
            $dues->where('type', $request->type);
        }

        $dues = $dues->paginate();

        if ($dues->isEmpty()) {
            $data['property_dues'] = (object)[];
        } else {

            $data['type'] = [
                'renter' => __('renter'),
                'owner' => __('owner')
            ];

            $data['duration'] = [
                'one_time' => __('one_time'),
                'day' => __('day'),
                'month' => __('month'),
                'year' => __('year'),
            ];
            $data['dues'] = Dues::select('id', 'name')->where('status', 'active')->get();

            $PropertyTransformer = new PropertyTransformer();
            $data['property_dues'] = $PropertyTransformer->transformCollection($dues->toArray(), lang(), 'dues');
        }

        return $this->success('Done', $data);

    }


    public function my_property_contracts(Request $request)
    {

        $input = $request->only('property_id');

        $validator = Validator::make($input, [
            'property_id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $property = Auth::user()->property->find($input['property_id']);
        if (!$property) {
            return $this->fail('Invaild Property ID');
        }

        if (!$property) {
            $data['contracts'] = (object)[];
        } else {

            // contracts
            $contracts = $property->contracts()->with('renter');

            if ($contracts->get()->isEmpty()) {
                $data['contracts'] = (object)[];
            } else {

                $contracts_renters = clone $contracts;

                whereBetween($contracts, 'DATE(contracts.date_from)', $request->date_from1, $request->date_from2);
                whereBetween($contracts, 'DATE(contracts.date_to)', $request->date_to1, $request->date_to2);
                whereBetween($contracts, 'price', $request->price1, $request->price2);
                whereBetween($contracts, 'insurance_price', $request->insurance_price1, $request->insurance_price2);
                whereBetween($contracts, 'deposit_rent', $request->deposit_rent1, $request->deposit_rent2);

                if ($request->id) {
                    $contracts->where('id', $request->id);
                }

                if ($request->renter_id) {
                    $contracts->where('renter_id', $request->renter_id);
                }

                if ($request->price) {
                    $contracts->where('price', $request->price);
                }

                if ($request->contract_type) {
                    $contracts->where('contract_type', $request->contract_type);
                }

                if ($request->status) {
                    $contracts->where('status', $request->status);
                }


                $contracts = $contracts->paginate();

                if ($contracts->isEmpty()) {
                    $data['contracts'] = (object)[];
                } else {
                    $data['contract_type'] = ['year' => __('year'), 'month' => __('month'), 'day' => __('day')];
                    $data['status'] = ['pendding' => __('pendding'), 'active' => __('active'), 'cancel' => __('cancel')];
                    $contracts_renters_ids = $contracts_renters->pluck('renter_id');
                    $data['renters'] = Client::select('id', 'first_name', 'second_name')->whereIn('id', $contracts_renters_ids)->get();

                    $PropertyTransformer = new PropertyTransformer();
                    $data['contracts'] = $PropertyTransformer->transformCollection($contracts->toArray(), lang(), 'contracts');
                }
            }
        }
        return $this->success('Done', $data);

    }


    public function contract_change_status(Request $request)
    {
        $input = $request->only('contract_id', 'status');

        $validator = Validator::make($input, [
            'contract_id' => 'int|required|exists:contracts,id',
            'status' => 'string|required|in:active,cancel',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $contract = Contract::whereIn('property_id', Property::where('owner_id', Auth::id())->pluck('id'))->find($input['contract_id']);
        if (!$contract) {
            return $this->fail('Invaild contract ID');
        }

        $contract->update(['status' => $input['status']]);

        return $this->success('Done');


    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $property = Property::details()->where('status', 'for_rent')->where('id', $input['id'])->first();
        $property->increment('views');
        $related_property = Property::block()->limit(10)
            ->where('property_type_id', $property->property_type_id)
            ->where(function ($q) use ($property) {
                $q->where('city_id', $property)->orWhere('government_id', $property->government_id);
            })
            ->where('id', '!=', $property->id)->get();

        $property_ads_ids = PropertyAds::block()->pluck('id');
        if (empty($property_ads_ids)) {
            $property_ads = [];
        } else {
            $property_ads = Property::block()->whereIn('id', $property_ads_ids)->limit(10)->get();
        }
        $PropertyTransformer = new PropertyTransformer();

        $data = [
            'property_ads' => (!empty($property_ads->toArray())) ? $PropertyTransformer->transformCollection($property_ads->toArray(), lang(), 'block') : [],
            'property' => $PropertyTransformer->details($property->toArray()),
            'related_property' => (!empty($related_property->toArray())) ? $PropertyTransformer->transformCollection($related_property->toArray(), lang(), 'block') : []
        ];
        $data['ads'] = Ads::select('title_' . lang() . ' as title', 'type', 'image', 'url', 'page')
            ->where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))
            ->where('page', 'property')->get();
        return $this->success('Done', $data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $input = $request->only(
            'id', 'property_type_id', 'purpose_id', 'area_id', 'building_number', 'flat_number', 'building_type', 'local_id', 'images',
            'area_type', 'title', 'floor', 'features', 'description', 'contract_type', 'contract_period', 'insurance_price', 'deposit_rent', 'price', 'space', 'address',
            'street_name', 'country_id', 'government_id', 'city_id', 'mogawra', 'room_number', 'bathroom_number', 'meta_key', 'meta_description', 'mobile', 'longitude', 'video_url',
            'dues', 'dues_value', 'dues_duration', 'status', 'purpose_id'
        );

        $validator = Validator::make($input, [
            'id' => 'required|int|exists:properties,id',
            'property_type_id' => 'required|int|exists:property_types,id',
            // 'purpose_id' => 'int|exists:purposes,id',
            // 'area_id' => 'required|int|exists:areas,id',
            'local_id' => 'nullable',
            'purpose_id' => 'nullable',
            'building_number' => 'required|nullable',
            'flat_number' => 'required|nullable',
            'building_type' => 'required|string|in:villa,tower',
            'area_type' => 'required|string|in:hayi,markaz,qasm',
            'title' => 'required|nullable',
            'floor' => 'required|numeric',
            'features' => 'nullable|array',
            'features.*' => 'nullable|exists:property_features,id',
            'description' => 'nullable|string',
            'contract_type' => 'nullable|string|in:year,month,day',
            'contract_period' => 'nullable',
            'insurance_price' => 'nullable',
            'deposit_rent' => 'nullable',
            'price' => 'nullable',
            'space' => 'required',
            'address' => 'nullable',
            'street_name' => 'nullable',
            'country_id' => 'required|int|exists:areas,id',
            'government_id' => 'required|int|exists:areas,id',
            'city_id' => 'nullable',
            'mogawra' => 'nullable',
            'room_number' => 'nullable',
            'bathroom_number' => 'nullable',
            'meta_key' => 'nullable',
            'meta_description' => 'nullable',
            'mobile' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'video_url' => 'nullable',
            'images.*' => 'mimes:jpeg,jpg,png,gif|max:10000',
            'dues.*' => 'nullable|int|exists:dues,id',
            'dues_value.*' => 'nullable',
            'dues_duration.*' => 'nullable|string|in:one_time,day,month,year',
            'status' => 'required|string|in:for_rent,rented'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $property = Property::find($input['id']);
        $input['features'] = $request->features ? implode(',', $request->features) : null;
        $input['owner_id'] = Auth::id();
        $input['slug'] = str_slug($input['title']);

        $dues = isset($input['dues']) ? $input['dues'] : [];
        $dues_value = isset($input['dues_value']) ? $input['dues_value'] : [];
        $dues_duration = isset($input['dues_duration']) ? $input['dues_duration'] : [];

        unset($input['dues']);
        unset($input['dues_value']);
        unset($input['dues_duration']);
        $updateData = $property->update($input);
        if ($updateData) {
            //upload files and images
            $files = $request->allFiles();

            if (!empty($files['images'])) {
                $custom_key = md5(rand() . time());
                foreach ($files['images'] as $key => $val) {

                    $path = $val->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
                    Image::create([
                        'custom_key' => $custom_key,
                        'path' => $path,
                        'sign_id' => $input['id'],
                        'sign_type' => 'App\Models\Property',
                        'image_name' => $key
                    ]);
                    addWaterMarker($path);
                }
            }
            if (!empty($dues)) {
                $property->dues()->delete();
                foreach ($dues as $key => $value) {
                    $due_data = [
                        'property_id' => $input['id'],
                        'due_id' => $dues[$key],
                        'value' => $dues_value[$key],
                        'duration' => $dues_duration[$key],
                        'type' => 'renter'
                    ];

                    PropertyDues::create($due_data);
                }
            }


            return $this->success(__('Property updated'));
        } else {
            return $this->fail(__('Error Please Try Again later'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function delete(Request $request)
    {
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $property = Auth::user()->property()->find($input['id']);
        if (!$property) {
            return $this->fail('property Not Exsist');
        }

        $property->dues()->delete();
        $property->images()->delete();
        $property->facilities()->delete();
        $property->contracts()->delete();
        $property->invoices()->delete();
        $property->delete();
        return $this->success(__('Property deleted'));

    }

    public function destroy($id)
    {
        if ((int)$id == 0) {
            return $this->fail('Invalid ID');
        }

        $property = Auth::user()->property()->find($id);
        if (!$property) {
            return $this->fail('property Not Exsist');
        }

        $property->dues()->delete();
        $property->images()->delete();
        $property->facilities()->delete();
        $property->delete();
        return $this->success(__('Property deleted'));

    }

    public function delete_property_image(Request $request)
    {
        $input = $request->only('image_id', 'property_id');

        $validator = Validator::make($input, [
            'image_id' => 'int|required|exists:images,id',
            'property_id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $image = Auth::user()->property->find($input['property_id'])->images()->find($input['image_id']);
        if (!$image) {
            return $this->fail('Image Not found');
        }

        $image->delete();
        return $this->success(__('Image deleted'));

    }


    public function request(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $already_sent = \App\Models\Request::where([
            'property_id' => $input['id'],
            'renter_id' => Auth::id()
        ])->first();
        if ($already_sent) {
            return $this->fail(__('Request Already Sent'));
        }

        $request = \App\Models\Request::create([
            'property_id' => $input['id'],
            'renter_id' => Auth::id()
        ]);

        if ($request) {
            return $this->success(__('Request Sent'));
        } else {
            return $this->fail(__('Request not send please try again later'));
        }
    }

    public function mobile(Request $request)
    {
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $check = \App\Models\Request::where(['property_id' => $input['id'], 'renter_id' => Auth::id()])->first();
        if ($check) {
            $property = Property::select('mobile')->find($input['id']);
            return $this->success('Done', ['mobile' => $property->mobile]);
        } else {
            return $this->fail(__('Please send request first and then try again later'));
        }

    }


    public function favorite_list(Request $request)
    {


        $property_ids = Auth::user()->favorite()->get(['property_id']);
        if ($property_ids->isEmpty()) {
            return $this->success('Property Data', ['property' => []]);
        }
        $ids = array_column($property_ids->toArray(), 'property_id');

        $eloquentData = Property::block()->whereIn('id', $ids)->orderBy('id', 'desc');

        whereBetween($eloquentData, 'DATE(requests.created_at)', $request->created_at1, $request->created_at2);


        $requests = $eloquentData->paginate();
        if ($requests->isEmpty()) {
            $data['property'] = [];
        } else {
            $transformer = new PropertyTransformer();
            $data['property'] = $transformer->transformCollection($requests->toArray(), lang(), 'block');

        }

        return $this->success('Property Data', $data);
    }


    public function favorite(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $check = PropertyFavorite::where([
            'property_id' => $input['id'],
            'client_id' => Auth::id()
        ])->first();
        if ($check) {
            return $this->fail(__('Already Added to favorite'));
        }

        $favorite = PropertyFavorite::create([
            'property_id' => $input['id'],
            'client_id' => Auth::id()
        ]);

        if ($favorite) {
            return $this->success(__('added to favorite'));
        } else {
            return $this->fail(__('Please try again later'));
        }
    }

    public function remove_form_favorite(Request $request)
    {
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $check = PropertyFavorite::where([
            'property_id' => $input['id'],
            'client_id' => Auth::id()
        ])->first();
        if ($check) {
            $check->delete();
            return $this->success(__('Removed from favorite'));
        }

        return $this->fail(__('Property Does not exists in your favorite'));


    }


    public function own(Request $request)
    {


        $eloquentData = Property::owner_table();

        whereBetween($eloquentData, 'DATE(properties.created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'properties.price', $request->price1, $request->price2);
        whereBetween($eloquentData, 'properties.insurance_price', $request->insurance_price1, $request->insurance_price2);
        whereBetween($eloquentData, 'properties.contract_period', $request->contract_period1, $request->contract_period2);
        whereBetween($eloquentData, 'properties.deposit_rent', $request->deposit_rent1, $request->deposit_rent2);
        whereBetween($eloquentData, 'properties.space', $request->space1, $request->space2);

        if ($request->id) {
            $eloquentData->where('properties.id', $request->id);
        }

        if ($request->property_type_id) {
            $eloquentData->where('properties.property_type_id', $request->property_type_id);
        }

        if ($request->purpose_id) {
            $eloquentData->where('properties.purpose_id', $request->purpose_id);
        }

        if ($request->building_number) {
            $eloquentData->where('properties.building_number', $request->building_number);
        }

        if ($request->flat_number) {
            $eloquentData->where('properties.flat_number', $request->flat_number);
        }

        if ($request->floor) {
            $eloquentData->where('properties.floor', $request->floor);
        }

        if ($request->title) {
            $eloquentData->where(function ($q) use ($request) {
                $q->where('properties.title', 'LIKE', '%' . $request->title . '%');
            });
        }

        if ($request->contract_type) {
            $eloquentData->where('properties.contract_type', $request->contract_type);
        }

        if ($request->address) {
            $eloquentData->where('properties.address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->owner_id) {
            $eloquentData->where('properties.owner_id', $request->owner_id);
        }

        if ($request->government_id) {
            $eloquentData->where('properties.government_id', $request->government_id);
        }

        if ($request->city_id) {
            $eloquentData->where('properties.city_id', $request->city_id);
        }

        if ($request->area_type) {
            $eloquentData->where('properties.area_type', $request->area_type);
        }

        if ($request->local_id) {
            $eloquentData->where('properties.local_id', $request->local_id);
        }

        if ($request->mogawra) {
            $eloquentData->where('properties.mogawra', $request->mogawra);
        }

        if ($request->room_number) {
            $eloquentData->where('properties.room_number', $request->room_number);
        }

        if ($request->bathroom_number) {
            $eloquentData->where('properties.bathroom_number', $request->bathroom_number);
        }

        if ($request->features) {
            $eloquentData->whereRaw('FIND_IN_SET(?,features)', [$request->features]);
        }


        $properties = $eloquentData->orderBy('id', 'desc')->paginate();

        if ($properties->isEmpty()) {
            $data['property'] = (object)[];
        } else {
            $transformer = new PropertyTransformer();
            $data['property'] = $transformer->transformCollection($properties->toArray(), lang(), 'own_table');

        }

        return $this->success('Property Data', $data);

    }

    public function own_without_pagination()
    {
        $properties = Property::select('id', 'title')->where('owner_id', Auth::id())->get();
        if ($properties->isEmpty()) {
            $data['property'] = (object)[];
        } else {
            $data['property'] = $properties;
        }
        return $this->success(__('Data'), $data);

    }

    public function rent(Request $request)
    {


        $eloquentData = Property::renter_table();

        whereBetween($eloquentData, 'DATE(properties.created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'properties.price', $request->price1, $request->price2);
        whereBetween($eloquentData, 'properties.insurance_price', $request->insurance_price1, $request->insurance_price2);
        whereBetween($eloquentData, 'properties.contract_period', $request->contract_period1, $request->contract_period2);
        whereBetween($eloquentData, 'properties.deposit_rent', $request->deposit_rent1, $request->deposit_rent2);
        whereBetween($eloquentData, 'properties.space', $request->space1, $request->space2);

        if ($request->id) {
            $eloquentData->where('properties.id', $request->id);
        }

        if ($request->property_type_id) {
            $eloquentData->where('properties.property_type_id', $request->property_type_id);
        }

        if ($request->purpose_id) {
            $eloquentData->where('properties.purpose_id', $request->purpose_id);
        }

        if ($request->building_number) {
            $eloquentData->where('properties.building_number', $request->building_number);
        }

        if ($request->flat_number) {
            $eloquentData->where('properties.flat_number', $request->flat_number);
        }

        if ($request->floor) {
            $eloquentData->where('properties.floor', $request->floor);
        }

        if ($request->title) {
            $eloquentData->where(function ($q) use ($request) {
                $q->where('properties.title', 'LIKE', '%' . $request->title . '%');
            });
        }

        if ($request->contract_type) {
            $eloquentData->where('properties.contract_type', $request->contract_type);
        }

        if ($request->address) {
            $eloquentData->where('properties.address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->owner_id) {
            $eloquentData->where('properties.owner_id', $request->owner_id);
        }

        if ($request->government_id) {
            $eloquentData->whereIn('properties.government_id', $request->government_id);
        }

        if ($request->city_id) {
            $eloquentData->whereIn('properties.city_id', $request->city_id);
        }

        if ($request->area_type) {
            $eloquentData->whereIn('properties.area_type', $request->area_type);
        }

        if ($request->local_id) {
            $eloquentData->whereIn('properties.local_id', $request->local_id);
        }

        if ($request->mogawra) {
            $eloquentData->whereIn('properties.mogawra', $request->mogawra);
        }

        if ($request->room_number) {
            $eloquentData->whereIn('properties.room_number', $request->room_number);
        }

        if ($request->bathroom_number) {
            $eloquentData->whereIn('properties.bathroom_number', $request->bathroom_number);
        }

        if ($request->features) {
            $eloquentData->whereRaw('FIND_IN_SET(?,features)', [$request->features]);
        }

        $properties = $eloquentData->paginate();
        if ($properties->isEmpty()) {
            $data['property'] = (object)[];
        } else {
            $transformer = new PropertyTransformer();
            $data['property'] = $transformer->transformCollection($properties->toArray(), lang(), 'renter_table');

        }

        return $this->success('Property Data', $data);

    }


    public function requests(Request $request)
    {

        $property_ids = Auth::user()->property()->get(['id']);
        if ($property_ids->isEmpty()) {
            return $this->success('Property Data', ['requests' => []]);
        }
        $ids = array_column($property_ids->toArray(), 'id');

        $eloquentData = RequestModal::whereIn('property_id', $ids)->with('renter')->orderBy('id', 'desc');

        whereBetween($eloquentData, 'DATE(requests.created_at)', $request->created_at1, $request->created_at2);


        if ($request->id) {
            $eloquentData->where('requests.id', $request->id);
        }


        if ($request->renter_id) {
            $eloquentData->where('requests.renter_id', $request->renter_id);
        }

        if ($request->property_id) {
            $eloquentData->where('requests.property_id', $request->property_id);
        }

        if ($request->status) {
            $eloquentData->where('requests.status', $request->status);
        }

        $requests = $eloquentData->paginate();
        if ($requests->isEmpty()) {
            $data['requests'] = [];
        } else {
            $transformer = new PropertyTransformer();
            $data['requests'] = $transformer->transformCollection($requests->toArray(), lang(), 'requests');

        }

        return $this->success('Property Data', $data);

    }

    public function request_change_status(Request $request)
    {

        $input = $request->only('request_id', 'status');
        $validator = Validator::make($input, [
            'request_id' => 'int|required|exists:requests,id',
            'status' => 'string|required|in:accept,reject',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $property_ids = Auth::user()->property()->get(['id']);
        if ($property_ids->isEmpty()) {
            return $this->fail('No Property');
        }
        $ids = array_column($property_ids->toArray(), 'id');

        $request_data = RequestModal::whereIn('property_id', $ids)->where('id', $input['request_id'])->first();
        if (!$request_data) {
            return $this->fail('request not exists');
        }

        $request_data->update(['status' => $input['status']]);

        return $this->success('request status changed');

    }

    public function renter_cancel_request(Request $request)
    {

        $input = $request->only('request_id', 'status');
        $validator = Validator::make($input, [
            'request_id' => 'int|required|exists:requests,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $request_data = RequestModal::where('renter_id', Auth::id())->where('id', $input['request_id'])->first();
        if (!$request_data) {
            return $this->fail('request not exists');
        }
        $request_data->update(['status' => 'cancel']);

        return $this->success('request is canceled');

    }

    function renter_invoices(Request $request)
    {
        $invoices = Auth::user()->invoices()->with(['property.area','property.government','property.local','client','property_due.dues' => function ($q) use ($request) {
            if ($request->due_id) {
                $q->where('property_due_id', $request->due_id);
            }
        }])->orderBy('date','desc');

        whereBetween($invoices, 'DATE(date)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $invoices->where('id', $request->id);
        }

        if ($request->status) {
            $invoices->where('status', $request->status);
        }


        if ($request->client_id) {
            $invoices->where('client_id', $request->client_id);
        }

        if ($request->amount) {
            $invoices->where('amount', $request->amount);
        }


        $invoices = $invoices->paginate();
        if ($invoices->isEmpty()) {
            $data['invoices'] = (object)[];
        } else {
            $client_transformer = new PropertyTransformer();
            $data['invoices'] = $client_transformer->transformCollection($invoices->toArray(), lang(), 'renter_invoices');
        }
        return $this->success('Data', $data);
    }

    function owner_invoices(Request $request)
    {
        $invoices = Auth::user()->property->invoices()->with(['property_due' => function ($q) use ($request) {
            if ($request->due_id) {
                $q->where('property_due_id', $request->due_id);
            }
        }, 'installment'])->get();

        if ($invoices->isEmpty()) {
            $data['invoices'] = (object)[];
        } else {
            $client_transformer = new PropertyTransformer();
            $data['invoices'] = $client_transformer->transformCollection($invoices->toArray(), lang(), 'invoices');
        }
        return $this->success('Data', $data);
    }

    function add_facilities(Request $request)
    {
        $input = $request->only('property_id', 'company_id', 'number');
        $validator = Validator::make($input, [
            'property_id' => 'int|required|exists:properties,id',
            'company_id' => 'int|required|exists:facility_companies,id',
            'number' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $data = [
            'facility_company_id' => $input['company_id'],
            'property_id' => $input['property_id'],
            'number' => $input['number'],
        ];

        $add = PropertyFacilities::create($data);
        if ($add) {
            return $this->success(__('data added'));
        } else {
            return $this->fail(__('Please try again later'));
        }
    }

    function update_facilities(Request $request)
    {
        $input = $request->only('id', 'property_id', 'company_id', 'number');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:property_facilities,id',
            'company_id' => 'int|required|exists:facility_companies,id',
            'number' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $id = $input['id'];
        unset($input['id']);
        $PropertyFacilities = PropertyFacilities::find($id);
        if ($PropertyFacilities->update($input)) {
            return $this->success(__('data updated'));
        } else {
            return $this->fail(__('Please try again later'));
        }
    }

    function delete_facilities(Request $request)
    {
        $input = $request->only('id', 'property_id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:property_facilities,id',
            'property_id' => 'int|required|exists:properties,id',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $Property = Auth::user()->property()->find($input['property_id']);
        if (!$Property) {
            return $this->fail(__('invalid property ID'));
        } else {
            $PropertyFacilities = $Property->facilities()->find($input['id']);
            if (!$PropertyFacilities) {
                return $this->fail(__('invalid Facilities ID'));
            } else {
                if ($PropertyFacilities->delete()) {
                    return $this->success(__('deleted'));
                } else {
                    return $this->fail(__('Please try again later'));
                }
            }
        }


    }


    /////////////////////////////all////////////////////////////////////////////


    public function all_requests(Request $request)
    {


        $property_ids = Auth::user()->property()->get(['id']);
        if ($property_ids->isEmpty()) {
            return $this->fail('No Property');
        }
        $ids = array_column($property_ids->toArray(), 'id');

        $requests = RequestModal::whereIn('property_id', $ids)->with('renter');

        if ($request->id) {
            $requests->where('id', $request->id);
        }

        if ($request->property_id) {
            $requests->where('property_id', $request->property_id);
        }

        if ($request->status) {
            $requests->where('status', $request->status);
        }

        if ($request->renter_id) {
            $requests->where('renter_id', $request->renter_id);
        }

        $requests = $requests->paginate();

        if ($requests->isEmpty()) {
            $data['requests'] = (object)[];
        } else {

            $data['status'] = [
                'new' => __('new'),
                'pendding' => __('pendding'),
                'accept' => __('accept'),
                'reject' => __('reject'),
                'cancel' => __('cancel'),
            ];
            $requests_renters_ids = RequestModal::pluck('renter_id');
            $data['renters'] = Client::select('id', 'first_name', 'second_name')->whereIn('id', $requests_renters_ids)->get();
            $PropertyTransformer = new PropertyTransformer();
            $data['requests'] = $PropertyTransformer->transformCollection($requests->toArray(), lang(), 'requests');
        }

        return $this->success('Done', $data);

    }

    public function my_property_ads()
    {

        return $this->success('Data', PropertyAds::select('id', 'property_id', 'start_date', 'end_date')
            ->whereIn('property_id', Property::where('owner_id', Auth::id())->pluck('id'))->get());

    }


    public function all_invoices(Request $request)
    {


        $where = [];
        if ($request->property_id) {
            $where = ['id' => $request->property_id];
        }

        $properties_ids = Property::where('owner_id', Auth::id())->where($where)->pluck('id');


        if (empty($properties_ids)) {
            $data['invoices'] = (object)[];
        } else {
            $properties_dues_ids = PropertyDues::whereIn('property_id', $properties_ids);
            if ($request->due_id) {
                $properties_ids->where('due_id', $request->due_id);
            }
            if ($request->due_type) {
                $properties_dues_ids = $properties_dues_ids->join('dues','dues.id','property_dues.due_id')
                   ->where('dues.type', $request->due_type);

            }
            $properties_dues_ids = $properties_dues_ids->pluck('property_dues.id');

            if (empty($properties_ids)) {
                $data['invoices'] = (object)[];
            } else {
                $invoices = Invoice::whereIn('property_due_id', $properties_dues_ids)->with('client', 'property_due.dues','property.local','property.government')->orderBy('date','desc');


                $invoices_renters = clone $invoices;

                whereBetween($invoices, 'DATE(date)', $request->created_at1, $request->created_at2);

                if ($request->id) {
                    $invoices->where('id', $request->id);
                }

                if ($request->status) {
                    $invoices->where('status', $request->status);
                }


                if ($request->client_id) {
                    $invoices->where('client_id', $request->client_id);
                }

                if ($request->amount) {
                    $invoices->where('amount', $request->amount);
                }

                $invoices = $invoices->paginate();
                if ($invoices->isEmpty()) {
                    $data['invoices'] = (object)[];
                } else {

                    $property_dues = PropertyDues::whereIn('property_id', Property::where('owner_id', Auth::id())->pluck('id'))->with('dues')->get();
                    $invoices_renters_ids = $invoices_renters->pluck('client_id');
                    $data['renters'] = Client::select('id', 'first_name', 'second_name')->whereIn('id', $invoices_renters_ids)->get();
                    $PropertyTransformer = new PropertyTransformer();
                    $data['invoices'] = $PropertyTransformer->transformCollection($invoices->toArray(), lang(), 'invoices');
                    $data['property_dues'] = $PropertyTransformer->transformCollection($property_dues->toArray(), lang(), 'dues');
                }


                return $this->success('Done', $data);
            }
        }


    }

    public function all_dues(Request $request)
    {


        // dues
        $dues = PropertyDues::whereIn('property_id', Property::where('owner_id', Auth::id())->pluck('id'));
        whereBetween($dues, 'date', $request->date1, $request->date2);

        if ($request->id) {
            $dues->where('id', $request->id);
        }

        if ($request->due_id) {
            $dues->where('due_id', $request->due_id);
        }

        if ($request->property_id) {
            $dues->where('property_id', $request->property_id);
        }

        if ($request->duration) {
            $dues->where('duration', $request->duration);
        }

        if ($request->value) {
            $dues->where('value', $request->value);
        }

        if ($request->type) {
            $dues->where('type', $request->type);
        }

        if ($request->due_type) {
            $dues = $dues->with(['dues' => function ($q) use ($request) {
                $q->where('type', $request->due_type);
            }]);
        } else {
            $dues = $dues->with('dues');
        }

        $dues = $dues->paginate();

        if ($dues->isEmpty()) {
            $data['property_dues'] = (object)[];
        } else {

            $data['type'] = [
                'renter' => __('renter'),
                'owner' => __('owner')
            ];

            $data['duration'] = [
                'one_time' => __('one_time'),
                'day' => __('day'),
                'month' => __('month'),
                'year' => __('year'),
            ];
            $data['dues'] = Dues::select('id', 'name')->where('status', 'active')->get();

            $PropertyTransformer = new PropertyTransformer();
            $data['property_dues'] = $PropertyTransformer->transformCollection($dues->toArray(), lang(), 'dues');
        }

        return $this->success('Done', $data);

    }

    public function contract_property_requests(Request $request)
    {
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:properties,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $data['renter'] = (Object)[];

        $requests_renter_ids = RequestModal::where('property_id', $input['id'])->pluck('renter_id');
        if (!empty($requests_renter_ids)) {
            $data['renter'] = Client::whereIn('id', $requests_renter_ids)->get(['first_name', 'second_name', 'id']);
        }

        return $this->success('Data', $data);

    }


    public function print_contract(Request $request)
    {
        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:contracts,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $properties_ids = Property::where('owner_id', Auth::id())->pluck('id');


        $contract =  Contract::where(function($q) use($properties_ids) {
            $q->where('renter_id',Auth::id())->orWhereIN('property_id',$properties_ids);
        })->where('id', $input['id'])->first();
        if (!$contract) {
            return $this->fail('Invaild contract ID');
        }
        $code = str_random(20);
        $contract->update(['print_code'=>$code]);
        return $this->success('Done',['url'=>route('api.contract.print',['print_code'=>$code])]);


    }

    public function print_contract_file(Request $request)
    {
        $input = $request->only('print_code');
        $validator = Validator::make($input, [
            'print_code' => 'required|exists:contracts,print_code'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $contract = Contract::where('print_code', $input['print_code'])->first();
        if (!$contract) {
            return $this->fail('Invaild contract print code');
        }


        $pdf = \PDF::loadView('pdf', ['contract' => $contract]);
        $filename = __('Contract ') . $contract->property_id . '.pdf';
        return $pdf->stream($filename);

    }


    public function all_contracts(Request $request)
    {

        $contracts = Contract::whereIn('property_id', Property::where('owner_id', Auth::id())->pluck('id'))->with('renter','property');
        $contracts_renters = clone $contracts;
        whereBetween($contracts, 'DATE(contracts.date_from)', $request->date_from1, $request->date_from2);
        whereBetween($contracts, 'DATE(contracts.date_to)', $request->date_to1, $request->date_to2);
        whereBetween($contracts, 'price', $request->price1, $request->price2);
        whereBetween($contracts, 'insurance_price', $request->insurance_price1, $request->insurance_price2);
        whereBetween($contracts, 'deposit_rent', $request->deposit_rent1, $request->deposit_rent2);

        if ($request->id) {
            $contracts->where('id', $request->id);
        }

        if ($request->property_id) {
            $contracts->where('property_id', $request->property_id);
        }

        if ($request->renter_id) {
            $contracts->where('renter_id', $request->renter_id);
        }

        if ($request->price) {
            $contracts->where('price', $request->price);
        }

        if ($request->contract_type) {
            $contracts->where('contract_type', $request->contract_type);
        }

        if ($request->status) {
            $contracts->where('status', $request->status);
        }

        $contracts = $contracts->paginate();

        if ($contracts->isEmpty()) {
            $data['contracts'] = (object)[];
        } else {
            $data['contract_type'] = ['year' => __('year'), 'month' => __('month'), 'day' => __('day')];
            $data['status'] = ['pendding' => __('pendding'), 'active' => __('active'), 'cancel' => __('cancel')];
            $contracts_renters_ids = $contracts_renters->pluck('renter_id');
            $data['renters'] = Client::select('id', 'first_name', 'second_name')->whereIn('id', $contracts_renters_ids)->get();

            $PropertyTransformer = new PropertyTransformer();
            $data['contracts'] = $PropertyTransformer->transformCollection($contracts->toArray(), lang(), 'contracts');
        }

        return $this->success('Done', $data);

    }

    public function contract_data()
    {
        $data['contract_templates'] = ContractTemplate::select('id', 'name', 'template_content')->get();
        $data['renter'] = (Object)[];
        $property_ids = Property::where('owner_id', Auth::id())->pluck('id');
        $requests_renter_ids = (!empty($property_ids)) ? RequestModal::whereIn('property_id', $property_ids)->pluck('renter_id') : [];
        if (!empty($requests_renter_ids)) {
            $data['renter'] = Client::whereIn('id', $requests_renter_ids)->get(['first_name', 'second_name', 'id']);
        }
        return $this->success('Contract Data', $data);
    }

    public function add_contract(Request $request)
    {
        $input = $request->only('property_id', 'renter_id', 'contract_template_id', 'date_from', 'date_to', 'price',
            'increase_value', 'increase_percentage', 'increase_from', 'pay_every', 'pay_at',
            'calendar', 'limit_to_pay', 'contract_type', 'insurance_price', 'deposit_rent', 'cut_from_insurance', 'status');
        $validator = Validator::make($input, [
            'property_id' => 'int|required|exists:properties,id',
            'renter_id' => 'int|required|exists:clients,id',
            'contract_template_id' => 'int|required|exists:contract_templates,id',
            'date_from' => 'string|required',
            'date_to' => 'string|required',
            'price' => 'numeric|required',
            'increase_value' => 'numeric|required',
            'increase_percentage' => 'numeric|required',
            'increase_from' => 'string|required',
            'pay_every' => 'numeric|required',
            'pay_at' => 'string|required|in:start,end',
            'calendar' => 'string|required|in:m,h',
            'limit_to_pay' => 'numeric|required',
            'contract_type' => 'string|required|in:year,month,day',
            'insurance_price' => 'numeric|required',
            'deposit_rent' => 'numeric|required',
            'deposit_rent' => 'numeric|required',
            'cut_from_insurance' => 'numeric',
            'status' => 'string|required|in:pendding,active,cancel',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $user_manage_service = Auth::user()->client_packeges()->where('service_type','manage')
            ->where('status','active')->where('date_from','<=',date('Y-m-d'))
            ->where('date_to','>=',date('Y-m-d'))->first();
        if(!$user_manage_service){
            return $this->fail(__('You Should subscribe to manage service first'));
        }


        $date1 = strtotime($input['date_from']);
        $date2 = strtotime($input['date_to']);

// Formulate the Difference between two dates
        $diff = abs($date2 - $date1);


// To get the year divide the resultant date into
// total seconds in a year (365*60*60*24)
        $years = floor($diff / (365*60*60*24));


// To get the month, subtract it with years and
// divide the resultant date into
// total seconds in a month (30*60*60*24)
        $months = floor(($diff - $years * 365*60*60*24)
            / (30*60*60*24));



        $property = Property::where('id', $input['property_id'])->where('owner_id', Auth::id())->first();
        if (!$property) {
            return $this->fail(__('Invalid Property ID'));
        }

        $renter = Client::find($input['renter_id']);
        $input['renter_name'] = $renter->Fullname;
        $input['renter_address'] = $renter->address;
        $input['renter_qysm'] = (!empty($renter->area_id))?$renter->area->name_ar:'';
        $input['renter_gev'] = (!empty($renter->area_id))?$renter->area->name_ar:'';
        $input['renter_id_num'] =$renter->id_number;

        $input['owner_name'] = Auth::user()->Fullname;
        $input['owner_qysm'] = (!empty(Auth::user()->area_id))?Auth::user()->area->name_ar:'';
        $input['owner_gev'] = (!empty(Auth::user()->area_id))?Auth::user()->area->name_ar:'';
        $input['owner_id_num'] =Auth::user()->id_number;
        $input['owner_address'] =Auth::user()->address;

        $input['property_number'] = $input['property_id'];
        $input['property_address'] = $property->address;
        $input['contract_date'] = $input['date_from'];
        $input['contract_period'] = $years.' سنين '.$months.' شهور ';

        $current_contract = $property->contracts()->where('date_from', '<=', date('Y-m-d'))
            ->where('date_to', '>=', date('Y-m-d'))->first();
        if (isset($current_contract['id'])) {
            return $this->fail(__('There are another contract to this property number ' . $current_contract['id']));
        }
        $contract_vars =  ['renter_name'=>'%renter_name%','date_from'=>'%date_from%','date_to'=>'%date_to%','price'=>'%price%',
            'contract_type'=>'%contract_type%','insurance_price'=>'%insurance_price%','deposit_rent'=>'%deposit_rent%',
            'pay_from'=>'%pay_from%','pay_to'=>'%pay_to%','increase_value'=>'%increase_value%','increase_percentage'=>'%increase_percentage%',
            'increase_from'=>'%increase_from%','pay_every'=>'%pay_every%','pay_at'=>'%pay_at%','calendar'=>'%calendar%','limit_to_pay'=>'%limit_to_pay%','contract_date'=>'%contract_date%',
            'owner_name'=>'%owner_name%','owner_qysm'=>'%owner_qysm%','owner_gev'=>'%owner_gev%','owner_id_num'=>'%owner_id_num%','owner_address'=>'%owner_address%','renter_address'=>'%renter_address%',
            'renter_qysm'=>'%renter_qysm%','renter_gev'=>'%renter_gev%','renter_id_num'=>'%renter_id_num%','property_number'=>'%property_number%','property_address'=>'%property_address%','contract_period'=>'%contract_period%'
        ];
        $template = ContractTemplate::find($input['contract_template_id']);
        $template = $template->template_content;

        foreach ($contract_vars as $key => $value) {
            $input_var = str_replace('%', '', str_replace('%', '', $value));

            if (isset($input[$input_var])) {

                $template = str_replace($value, $input[$input_var], $template);

            }
        }

        unset($input['contract_template_id']);
        $input['contract_content'] = $template;
        $input['pay_from'] = $input['date_from'];
        $input['pay_to'] = $input['date_to'];
        $contract = Contract::create($input);
        $property->update(['renter_id' => $input['renter_id'],'status'=>'rented']);
        if ($contract) {
            return $this->success('Contract added successfully');
        } else {
            return $this->fail(__('Please Try Again Later'));
        }

    }

    public function renter_requests(Request $request)
    {

        $requests = RequestModal::where('renter_id', Auth::id());

        if ($request->id) {
            $requests->where('id', $request->id);
        }

        if ($request->property_id) {
            $requests->where('property_id', $request->property_id);
        }

        if ($request->status) {
            $requests->where('status', $request->status);
        }


        $requests = $requests->paginate();

        if ($requests->isEmpty()) {
            $data['requests'] = (object)[];
        } else {


            $PropertyTransformer = new PropertyTransformer();
            $data['requests'] = $PropertyTransformer->transformCollection($requests->toArray(), lang(), 'renter_requests');
        }

        return $this->success('Done', $data);

    }


    public function renter_dues(Request $request)
    {


        // dues
        $dues = PropertyDues::whereIn('property_id', Property::where('renter_id', Auth::id())->pluck('id'))->with('dues');


        if ($request->id) {
            $dues->where('id', $request->id);
        }

        if ($request->due_id) {
            $dues->where('due_id', $request->due_id);
        }

        if ($request->property_id) {
            $dues->where('property_id', $request->property_id);
        }

        if ($request->duration) {
            $dues->where('duration', $request->duration);
        }

        if ($request->value) {
            $dues->where('value', $request->value);
        }

        if ($request->type) {
            $dues->where('type', $request->type);
        }

        $dues = $dues->paginate();

        if ($dues->isEmpty()) {
            $data['property_dues'] = (object)[];
        } else {

            $data['type'] = [
                'renter' => __('renter'),
                'owner' => __('owner')
            ];

            $data['duration'] = [
                'one_time' => __('one_time'),
                'day' => __('day'),
                'month' => __('month'),
                'year' => __('year'),
            ];
            $data['dues'] = Dues::select('id', 'name')->where('status', 'active')->get();

            $PropertyTransformer = new PropertyTransformer();
            $data['property_dues'] = $PropertyTransformer->transformCollection($dues->toArray(), lang(), 'dues');
        }

        return $this->success('Done', $data);

    }


    public function renter_contracts(Request $request)
    {

        $contracts = Contract::where('renter_id', Auth::id());
        whereBetween($contracts, 'DATE(contracts.date_from)', $request->date_from1, $request->date_from2);
        whereBetween($contracts, 'DATE(contracts.date_to)', $request->date_to1, $request->date_to2);
        whereBetween($contracts, 'price', $request->price1, $request->price2);
        whereBetween($contracts, 'insurance_price', $request->insurance_price1, $request->insurance_price2);
        whereBetween($contracts, 'deposit_rent', $request->deposit_rent1, $request->deposit_rent2);

        if ($request->id) {
            $contracts->where('id', $request->id);
        }

        if ($request->property_id) {
            $contracts->where('property_id', $request->property_id);
        }


        if ($request->price) {
            $contracts->where('price', $request->price);
        }

        if ($request->contract_type) {
            $contracts->where('contract_type', $request->contract_type);
        }

        if ($request->status) {
            $contracts->where('status', $request->status);
        }

        $contracts = $contracts->paginate();

        if ($contracts->isEmpty()) {
            $data['contracts'] = (object)[];
        } else {
            $data['contract_type'] = ['year' => __('year'), 'month' => __('month'), 'day' => __('day')];
            $data['status'] = ['pendding' => __('pendding'), 'active' => __('active'), 'cancel' => __('cancel')];

            $PropertyTransformer = new PropertyTransformer();
            $data['contracts'] = $PropertyTransformer->transformCollection($contracts->toArray(), lang(), 'renter_contracts');
        }

        return $this->success('Done', $data);

    }


    public function change_invoice_status(Request $request)
    {

        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'int|required|exists:invoices,id',
            'notes' => 'nullable'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $properties_ids = Property::where('owner_id', Auth::id())->pluck('id');


        if (empty($properties_ids)) {
            return $this->fail('Cannot find invoice');
        } else {
            $properties_dues_ids = PropertyDues::whereIn('property_id', $properties_ids);
            if ($request->due_id) {
                $properties_ids->where('due_id', $request->due_id);
            }
            if ($request->due_type) {
                $properties_dues_ids = $properties_dues_ids->with(['dues' => function ($q) use ($request) {
                    $q->where('type', $request->due_type);
                }]);
            }
            $properties_dues_ids = $properties_dues_ids->pluck('id');

            if (empty($properties_ids)) {
                return $this->fail('Cannot find invoice');
            } else {
                $invoices = Invoice::whereIn('property_due_id', $properties_dues_ids)->with('client', 'property_due')
                    ->where('id', $input['id'])->first();


            }
        }


        if (empty($invoices)) {
            return $this->fail('Cannot find invoice');
        }

        if($invoices->status != 'unpaid'){
            return $this->fail('invoice status is paid');
        }

        if(empty($input['notes'])){
            $input['notes'] = 'تم تحويل حاله الفاتورة الى مدفوع بواسطه المالك ';
        }
        $invoices->update(['status' => 'paid', 'notes' =>$input['notes'] ]);


        return $this->success('done');
    }

    public function print_invoice(Request $request){

        $input = $request->only('id');
        $validator = Validator::make($input, [
            'id' => 'required|exists:invoices,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $invoice = Invoice::find($input['id']);



        if(Auth::id() == $invoice->client_id){

            if($invoice->status == 'unpaid'){
                return $this->fail('invoice status is unpaid');
            }

        }else{
            $properties_ids = Property::where('owner_id', Auth::id())->pluck('id');

            if (empty($properties_ids)) {
                return $this->fail('Cannot find invoice');
            } else {
                $properties_dues_ids = PropertyDues::whereIn('property_id', $properties_ids);
                if ($request->due_id) {
                    $properties_ids->where('due_id', $request->due_id);
                }
                if ($request->due_type) {
                    $properties_dues_ids = $properties_dues_ids->with(['dues' => function ($q) use ($request) {
                        $q->where('type', $request->due_type);
                    }]);
                }
                $properties_dues_ids = $properties_dues_ids->pluck('id');

                if (empty($properties_ids)) {
                    return $this->fail('Cannot find invoice');
                } else {
                    $owner_invoice = Invoice::whereIn('property_due_id', $properties_dues_ids)->with('client', 'property_due')
                        ->where('id', $input['id'])->first();
                    if (empty($owner_invoice)) {
                        return $this->fail('Cannot find invoice');
                    }

                    if($owner_invoice->status == 'unpaid'){
                        return $this->fail('invoice status is unpaid');
                    }

                }
            }

        }

        $code = str_random(20);
        $invoice->update(['print_code'=>$code]);
        return $this->success('Done',['url'=>route('api.invoice.print',['print_code'=>$code])]);


    }


    public function print_invoice_file(Request $request){

        $input = $request->only('print_code');
        $validator = Validator::make($input, [
            'print_code' => 'required|exists:invoices,print_code'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $invoice = Invoice::where('print_code',$input['print_code']) ->where('status','paid')->with('property_due.dues','property.local','property.government')->first();
if(!$invoice){
    abort('404');
}



$transaction = Transaction::join('transaction_invoices','transaction_invoices.transaction_id','transactions.id')
    ->where('transactions.status','=','paid')->where('transaction_invoices.invoice_id',$invoice->id)->first();


if(!$transaction){
    $transaction = [];
}else{
    $transaction->payment_method_name = PaymentMethods::find($transaction->payment_method_id)->name;
}


     //   return view('invoice',['invoice'=>$invoice,'transaction'=>$transaction]);
        $pdf = \PDF::loadView('invoice', ['invoice'=>$invoice,'transaction'=>$transaction]);
        $filename = __('Invoice ') . $invoice->id . '.pdf';
        return $pdf->stream($filename);



    }


}
