<?php

namespace App\Modules\Api;

use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\ClientJob;
use App\Models\Image;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Slider;
use App\Models\Sms;
use App\Models\Ticket;
use App\Modules\Api\Transformers\ClientTransformer;
use App\Modules\Api\Transformers\HomeTransformer;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ClientPermissions;
use App\Models\Request as RequestModal;

class ClientApiController extends ApiController
{

    public function __construct()
    {
        $this->middleware(['auth:api'])->except([
            'login', 'register', 'forgot_password','resend_forgot_code', 'resend_activation_code', 'verify_account','reset_password'
        ]);
    }

    public function register(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'mobile' => 'required|unique:clients',
            'email' => 'nullable|email|unique:clients',
            'password' => 'required|confirmed|min:6',
            'type' => 'required|in:owner,renter,both'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $activation_code = rand(1000, 9999);;
        $input['password'] = bcrypt($input['password']);
        unset($input['password_confirmation']);
        $input['activation_code'] = $activation_code;
        $client = Client::create($input);
        if ($client) {
            $sms_content = __('Activation code is ' . $activation_code);
            send_sms($client->mobile, $sms_content);
            return $this->success(__('Your account is created successfully, Please verify your mobile number by type code that send to you via sms ' . $sms_content), ['client_id' => $client->id]);
        } else {
            return $this->fail(__('Please try again later'));
        }
    }


    function get_permissions(){
         
        return $this->success('Done',ClientPermissions::get());
    }

    function resend_activation_code(Request $request)
    {
        $input = $request->only('mobile');
        $validator = Validator::make($input, [
            'mobile' => 'required|exists:clients,mobile',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $client = Client::select('activation_code','mobile')->where('mobile',$input['mobile'])->first();

        $sms_content = __('Activation code is ' . $client->activation_code);
//             $client->update(['activation_code_expire_code'=>date('Y-m-d H:i:s')]);
        if (send_sms($client->mobile, $sms_content)) {
            return $this->success(__('activation code resent successfully ' . $sms_content));
        } else {
            return $this->fail(__('Can not send code, please try again later'));
        }

    }


    function verify_account(Request $request)
    {
        $input = $request->only('mobile', 'code');
        $validator = Validator::make($input, [
            'mobile' => 'required|exists:clients,mobile',
            'code' => 'required|exists:clients,activation_code',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $client = Client::where('mobile',$input['mobile'])->first();
        if (!empty($client->verified_at)) {
            return $this->fail(__('Account Already verified'));
        }
        if ($client->activation_code == $input['code']) {
            $client->update(['verified_at' => date('Y-m-d H:i:s')]);
            return $this->success(__('Account is active now, please try to login'));
        } else {
            return $this->fail(__('Invalid activation code'));
        }
    }


    public function login(Request $request)
    {

        $input = $request->only(['mobile', 'password','firebase_token']);
        $validator = Validator::make($input, [
            'mobile' => 'required|exists:clients,mobile',
            'password' => 'required',
            'firebase_token' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        if ($this->actualLogin($input['mobile'], $input['password'])) {
            $token = $this->GenerateToken($input['mobile'], $input['password']);

            if ($token['status']) {

                $client = Client::block()->find(Auth::id());
                if($client->verified_at == null){
                    return response()->json([
                        'status' => false,
                        'msg' => __('Account not verified yet'),
                        'code' => 308,
                        'data'=>false
                    ],200);
                }
                if($client->status == 'in-active'){
                    return response()->json([
                        'status' => false,
                        'msg' => __('Account is inActive'),
                        'code' => 308,
                        'data'=>false
                    ],200);
                }
                if(!empty($input['firebase_token'])){
                    $client->update(['firebase_token'=>$input['firebase_token']]);
                }
                $token['data']->client = $client;
                $token['data']->notifications_count = $client->notification()->whereNull('read_at')->count();
                return $this->success(__('Successfully logged in'), $token['data']);
            } else {
                return $this->fail($token['msg']);
            }
        } else {
            return $this->fail(__('Wrong username OR password'));
        }

    }



    public function actualLogin($username, $password)
    {
        if (Auth('web')->attempt(['mobile' => $username, 'password' => $password])) {
            return true;
        } else {
            return false;
        }
    }

    public function GenerateToken($username, $password)
    {
        $http = new \GuzzleHttp\Client;

        try {

            $response = $http->post(getenv('APP_URL') . 'public/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => '2',
                    'client_secret' => 'brfIOS9vCUw2HuLOvLdyrjh9iA3gtxPV2xWVvw9w',
                    'username' => $username,
                    'password' => $password,
                    'scope' => '',
                ],
            ]);
            return ['status' => true, 'data' => json_decode((string)$response->getBody())];
        } catch (RequestException $e) {

            return ['status' => false, 'msg' => $e->getMessage()];
        }
    }

    public function forgot_password(Request $request)
    {
        $input = $request->only(['mobile']);
        $validator = Validator::make($input, [
            'mobile' => 'required|exists:clients,mobile'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $client = Client::where('mobile', $input['mobile'])->first();
        $forgot_code = rand(1000, 9999);;
        $client->update(['forgot_password_code' => $forgot_code]);
        $sms_content = __('Code is ' . $forgot_code);
        if (send_sms($client->mobile, $sms_content)) {
            return $this->success(__('Forgot code sent successfully ' . $sms_content));
        } else {
            return $this->fail(__('Can not send code, please try again later'));
        }
    }

    public function reset_password(Request $request)
    {
        $input = $request->only(['mobile', 'code', 'password', 'password_confirmation']);
        $validator = Validator::make($input, [
            'mobile' => 'required|exists:clients,mobile',
            'code' => 'required|exists:clients,forgot_password_code',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $client = Client::where(['mobile' => $input['mobile'], 'forgot_password_code' => $input['code']])->first();
        if (!$client) {
            return $this->fail('Invalid code');
        }

        if ($client->update(['forgot_password_code' => '', 'password' => bcrypt($input['password'])])) {
            return $this->success(__('Password updated'));
        } else {
            return $this->fail('Cannot update password, please try again later');
        }


    }

    public function change_password(Request $request)
    {
        $input = $request->only([ 'password', 'password_confirmation','old_password']);
        $validator = Validator::make($input, [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        if(bcrypt($input['old_password']) != Auth::user()->password){
            return $this->fail(__('Wrong Old Password'));
        }

        if (Auth::user()->update(['forgot_password_code' => '', 'password' => bcrypt($input['password'])])) {
            return $this->success(__('Password updated'));
        } else {
            return $this->fail('Cannot update password, please try again later');
        }


    }


    public function resend_forgot_code(Request $request)
    {
        $input = $request->only('mobile');
        $validator = Validator::make($input, [
            'mobile' => 'required|exists:clients,mobile',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $client = Client::select('forgot_password_code','mobile')->where('mobile', $input['mobile'])->first();
        $sms_content = __('Code is ' . $client->forgot_password_code);
        if (send_sms($client->mobile, $sms_content)) {
            return $this->success(__('Code resent successfully ' . $sms_content));
        } else {
            return $this->fail(__('Can not send code, please try again later'));
        }

    }

    public function login_board()
    {
        if (Auth::user()->type == 'renter') {
            $type = 'renter';
        } else {
            $type = 'owner';
        }
        $slider = Slider::select('title_' . lang() . ' as title', 'description_' . lang() . ' as description', 'image')
            ->where('status', 'active')->where('type', $type)->get();
        if ($slider->isEmpty()) {
            $data['board'] = [];
        } else {
            $HomeTransformer = new HomeTransformer();
            $data['board'] = $HomeTransformer->transformCollection($slider->toArray(), lang(), 'board');
        }
        return $this->success('Done', $data);
    }

    public function profile()
    {

        $HomeTransformer = new HomeTransformer();
        $ClientTransformer = new ClientTransformer();
        $auth = Auth::user();
        $files = $HomeTransformer->image($auth->images()->get()->toArray());
        $data['user'] = [
            'id' => $auth['id'],
            'type' => $auth['type'],
            'first_name' => $auth['first_name'],
            'second_name' => $auth['second_name'],
            'third_name' => $auth['third_name'],
            'last_name' => $auth['last_name'],
            'gender' => $auth['gender'],
            'birth_date' => $auth['birth_date'],
            'id_number' => $auth['id_number'],
            'area_id' => $auth['area_id'],
            'area_name' => !empty($auth['area_id'])?$auth['area']['name_'.lang()]:'',
            'email' => $auth['email'],
            'phone' => $auth['phone'],
            'mobile' => $auth['mobile'],
            'address' => $auth['address'],
            'description' => $auth['description'],
            'bank_code' => $auth['bank_code'],
            'branch_code' => $auth['branch_code'],
            'bank_account_number' => $auth['bank_account_number'],
            'files' => $files,
            'jobs' => (!empty($auth->jobs()->get()->toArray()))?$ClientTransformer->transformCollection($auth->jobs()->get()->toArray(), [lang()], 'jobs'):[]
        ];

        return $this->success('Done', $data);
    }


    function update_profile(Request $request)
    {
        $id = Auth::id();
        $input = $request->only('gender', 'first_name', 'second_name', 'third_name', 'last_name', 'birth_date', 'id_number', 'area_id',
            'email', 'phone', 'address', 'description', 'personal_photo', 'card_face', 'card_back', 'passport', 'criminal_record', 'images','job_title',
            'bank_code','branch_code','bank_account_number');

        $validator = Validator::make($input, [
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'third_name' => 'required|string',
            'last_name' => 'required|string',
            'birth_date' => 'required|date_format:"Y-m-d"',
            'id_number' => 'required|numeric|digits:14|unique:clients,id_number,' . $id,
            'area_id' => 'nullable',
            'email' => 'required|string|email|unique:clients,email,' . $id,
            'address' => 'nullable',
            'description' => 'nullable',
            'personal_photo' => 'mimes:jpeg,jpg,png|max:10000',
            'card_face' => 'mimes:jpeg,jpg,png|max:10000',
            'card_back' => 'mimes:jpeg,jpg,png|max:10000',
            'passport' => 'mimes:jpeg,jpg,png|max:10000',
            'criminal_record' => 'mimes:jpeg,jpg,png|max:10000',
            'bank_account_number' => 'string|nullable',
            'bank_code' => 'string|nullable|exists:banks,bank_code',
            'branch_code' => 'string|nullable|exists:banks_branches,branch_code',
            ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $client =  Client::find($id);
        $custom_key = md5(rand() . time());

        if($request->file('personal_photo')){
            $path = $request->file('personal_photo')->store(setting('system_path').date('Y/m/d'),'first_public');
            Image::where([
                'sign_id'=>$id,
                'image_name'=>'personal_photo',
            ])->delete();
            if($path){
                Image::create([
                    'custom_key' => $custom_key,
                    'path' => $path,
                    'sign_id' => $id,
                    'sign_type' => 'App\Models\Client',
                    'image_name' => 'personal_photo'
                ]);
            }
        }

        if($request->file('card_face')){
            $path = $request->file('card_face')->store(setting('system_path').date('Y/m/d'),'first_public');
            Image::where([
                'sign_id'=>$id,
                'image_name'=>'card_face',
            ])->delete();
            if($path){
                Image::create([
                    'custom_key' => $custom_key,
                    'path' => $path,
                    'sign_id' => $id,
                    'sign_type' => 'App\Models\Client',
                    'image_name' => 'card_face'
                ]);
            }
        }

        if($request->file('card_back')){
            $path = $request->file('card_back')->store(setting('system_path').date('Y/m/d'),'first_public');
            Image::where([
                'sign_id'=>$id,
                'image_name'=>'card_back',
            ])->delete();
            if($path){
                Image::create([
                    'custom_key' => $custom_key,
                    'path' => $path,
                    'sign_id' => $id,
                    'sign_type' => 'App\Models\Client',
                    'image_name' => 'card_back'
                ]);
            }
        }

        if($request->file('passport')){
            $path = $request->file('passport')->store(setting('system_path').date('Y/m/d'),'first_public');
            Image::where([
                'sign_id'=>$id,
                'image_name'=>'passport',
            ])->delete();
            if($path){
                Image::create([
                    'custom_key' => $custom_key,
                    'path' => $path,
                    'sign_id' => $id,
                    'sign_type' => 'App\Models\Client',
                    'image_name' => 'passport'
                ]);
            }
        }

        if($request->file('criminal_record')){
            $path = $request->file('criminal_record')->store(setting('system_path').date('Y/m/d'),'first_public');
            Image::where([
                'sign_id'=>$id,
                'image_name'=>'criminal_record',
            ])->delete();
            if($path){
                Image::create([
                    'custom_key' => $custom_key,
                    'path' => $path,
                    'sign_id' => $id,
                    'sign_type' => 'App\Models\Client',
                    'image_name' => 'criminal_record'
                ]);
            }
        }

        unset($input['personal_photo']);
        unset($input['card_face']);
        unset($input['card_back']);
        unset($input['passport']);
        unset($input['criminal_record']);

        $updateData = $client->update($input);
        if ($updateData) {
            //upload files and images
            $files = $request->allFiles();
            if (!empty($files)) {

                foreach ($files as $key => $val) {
                    if ($request->hasFile($key)) {
                        $path = $request->file($key)->store(setting('system_path') . '/' . date('Y/m/d'), 'first_public');
                        $old_image = Image::where(['image_name' => $key, 'sign_id' => $id, 'sign_type' => 'App\Models\Client'])->first();
                        if (!$old_image) { //create image
                            Image::create([
                                'custom_key' => $custom_key,
                                'path' => $path,
                                'sign_id' => $id,
                                'sign_type' => 'App\Models\Client',
                                'image_name' => $key
                            ]);
                        } else { // update image
                            if (is_file($old_image->path))
                                unlink($old_image->path);
                            $old_image->update([
                                'path' => $path
                            ]);
                        }
                    }
                }
            }

            if (!empty($request->job_title)) {

                $client->jobs()->forceDelete();
                $job_title = $request->job_title;
                $job_company = $request->company_name;
                $job_from = $request->from_date;
                $job_to = $request->to_date;
                $job_present = $request->present;

                foreach ($job_title as $key => $value) {
                    if (empty($job_title[$key]) || empty($job_from[$key])) {
                        continue;
                    }
                    if ($job_present[$key] == 'no' && empty($job_to[$key])) {
                        continue;
                    }
                    $job_data = array(
                        'client_id' => $id,
                        'job_title' => $job_title[$key],
                        'company_name' => $job_company[$key],
                        'from_date' => $job_from[$key],
                        'to_date' => $job_to[$key],
                        'present' => $job_present[$key]
                    );
                    ClientJob::create($job_data);
                }
            }
            return $this->success( __('Data modified successfully'));

        } else {
            return $this->fail(__('Sorry, we could not edit the data'));
        }
    }


    function tickets(Request $request){
        $tickets = Auth::user()->tickets()->select('id','title','status','created_at')->orderBy('id','desc')->paginate();
        if($tickets->isEmpty()){
            $data['tickets'] = [];
        }else {
            $client_transformer = new ClientTransformer();
            $data['tickets'] = $client_transformer->transformCollection($tickets->toArray(), lang(),'tickets');
        }
        return $this->success('Tickets Data',$data);
    }

    function ticket_details(Request $request){
        $input = $request->only('id');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:tickets,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $ticket = Auth::user()->tickets()->where('id',$input['id'])->with(['comments'=>function($q){
            $q->with(['client','staff']);
        }])->first();

        if(!$ticket) {
            return $this->fail(__('Invaild ticket id'));
        }
        $client_transformer = new ClientTransformer();
        $data['ticket'] = $client_transformer->ticket_details($ticket->toArray());
        return $this->success('Ticket Data',$data);

    }


    function add_ticket(Request $request)
    {
        $input = $request->only('title','comment');

        $validator = Validator::make($input, [
            'title' => 'string|required',
            'comment' => 'string|required',
            'image' => 'mimes:jpeg,jpg,png|max:10000',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $path = '';
        if($request->file('image')){
            $path = $request->file('image')->store(setting('system_path').date('Y/m/d'),'first_public');

        }
        $ticket = Ticket::create([
            'client_id'=>Auth::id(),
            'title'=>$input['title'],
            'status'=>'new'
        ]);

        if($ticket){
            $ticket->comments()->create([
               'ticket_id'=>$ticket->id,
                'client_id'=>Auth::id(),
                'comment'=>$input['comment'],
                'image'=>$path
            ]);
            return $this->success('Ticket added successful');
        }else{
            return $this->fail(__('Please try again later'));
        }

    }

    function ticket_add_comment(Request $request)
    {
        $input = $request->only('id','comment');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:tickets,id',
            'comment' => 'string|required',
            'image' => 'mimes:jpeg,jpg,png|max:10000',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $ticket = Auth::user()->tickets()->find($input['id']);

        if(!$ticket){
            return $this->fail(__('Ticket not found'));
        }
        if($ticket->status == 'solve' || $ticket->status == 'close'){
            return $this->fail(__('Ticket is '.$ticket->status.', please add a new ticket'));
        }
        $path = '';
        if($request->file('image')){
            $path = $request->file('image')->store(setting('system_path').date('Y/m/d'),'first_public');

        }

            $ticket->comments()->create([
                'ticket_id'=>$ticket->id,
                'client_id'=>Auth::id(),
                'comment'=>$input['comment'],
                'image'=>$path
            ]);


            return $this->success('Ticket comment added successful');


    }


    function ticket_solve(Request $request){
        $input = $request->only('id','comment');

        $validator = Validator::make($input, [
            'id' => 'int|required|exists:tickets,id'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $ticket = Auth::user()->tickets()->find($input['id']);
        if(!$ticket){
            return $this->fail(__('Ticket not found'));
        }
        if($ticket->status == 'solve' || $ticket->status == 'close'){
            return $this->fail(__('Ticket is Already '.$ticket->status));
        }

        $ticket->update(['status'=>'solve']);

        return $this->success('Ticket solve successful');

    }

    function add_user(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'mobile' => 'required|unique:clients',
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'password' => 'required|confirmed|min:6',
            'permissions.*'=>'required|string',
            'permissions'=>'required|string',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
        $input['password'] = bcrypt($input['password']);
        unset($input['password_confirmation']);
        $input['status']= 'active';
        $input['verified_at']= date('Y-m-d H:i:s');
        $input['parent_id']= Auth::id();
        $user = Client::create($input);

        if ($user) {
            return $this->success(__('User created successfully'));
        } else {
            return $this->fail(__('Please try again later'));
        }
    }


    public function update_user(Request $request)
    {
        $input = $request->all();

        if(!isset($input['id'])){
            return $this->fail('ID is required');
        }

        $validation_arr = [];
        if(isset($input['password'])){
             $validation_arr['password'] =  'confirmed|min:6';
            $validation_arr['password_confirmation'] =  'required';
        }

        $data =  [
            'id' => 'required|exists:clients',
            'mobile' => 'required|unique:clients,mobile,'.$input['id'],
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'permissions'=>'required|string',
        ];


        $validate_array = array_merge($data,$validation_arr);
        $validator = Validator::make($input,$validate_array);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $user = Client::where('parent_id',Auth::id())->find($input['id']);
        if(!$user){
            return $this->fail('User Not Exist');
        }


        if(isset($input['password']) && !empty($input['password'])){
            $input['password'] = bcrypt($input['password']);
            unset($input['password_confirmation']);
        }
        unset($input['id']);
        $update = $user->update($input);

        if ($update) {
            return $this->success(__('User Updated successfully'));
        } else {
            return $this->fail(__('Please try again later'));
        }

    }


    public function users_list()
    {
        $id = Auth::id();

        $users = Client::where('parent_id',$id)->get();

        $userTransformer = new ClientTransformer();

        if($users->isEmpty()){
            $data['users'] = (object)[];
        }else{
            $data['users'] = $userTransformer->transformCollection($users->toArray(),lang(),'users');
        }

        return $this->success('Done',$data);

    }

    public function delete_user(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required|exists:clients'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $user = Client::where('parent_id',Auth::id())->find($input['id']);
        if(!$user){
            return $this->fail('User Not Exist');
        }

        $user->delete();

        return $this->success(__('User Deleted'));

    }



    public function request_user(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'request_id' => 'required|exists:requests,id'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }


        $property_ids = Auth::user()->property()->get(['id']);
        if($property_ids->isEmpty()){
            return $this->fail('No Property');
        }
        $ids = array_column($property_ids->toArray(),'id');

        $request = RequestModal::whereIn('property_id',$ids)->where('id',$input['request_id'])->first();

        if(!$request){
            return $this->fail('Request Not Exists');
        }


        $HomeTransformer = new HomeTransformer();
        $ClientTransformer = new ClientTransformer();
        $auth = Client::find($request->renter_id);
        $files = $HomeTransformer->image($auth->images()->get()->toArray());
        $data['user'] = [
            'type' => $auth['type'],
            'first_name' => $auth['first_name'],
            'second_name' => $auth['second_name'],
            'third_name' => $auth['third_name'],
            'last_name' => $auth['last_name'],
            'gender' => $auth['gender'],
            'birth_date' => $auth['birth_date'],
            'id_number' => $auth['id_number'],
            'area_id' => $auth['area_id'],
            'area_name' => (isset($auth['area']['name_'.lang()]))?$auth['area']['name_'.lang()]:'',
            'email' => $auth['email'],
            'phone' => $auth['phone'],
            'mobile' => $auth['mobile'],
            'address' => $auth['address'],
            'description' => $auth['description'],
            'files' => $files,
            'jobs' => ($auth->jobs->isNotEmpty())?$ClientTransformer->transformCollection($auth->jobs()->get()->toArray(), [lang()], 'jobs'):[]
        ];

        return $this->success('Done', $data);
    }



    public function notifications(){
        $notifications = Auth::user()->notification()->select('id','title','data','read_at')->orderby('id','desc')->paginate();
        if($notifications->isEmpty()){
            $data['notifications'] = (object)[];
        }
        Notification::where('client_id',Auth::id())->update(['read_at'=>date('Y-m-d H:i:s')]);
        return $this->success('Data',$notifications);

    }



    public function send_notification(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'renter_ids' => 'required|string',
            'type' => 'required|array',
            'title' => 'required|string',
            'invoice_id' => 'nullable|int',
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }

        $invoices = Invoice::whereIn('id',explode(',',$input['renter_ids']))->pluck('client_id');

        if($invoices->isEmpty()){
            return $this->fail('no client selected not found');
        }

        $renters = Client::whereIn('id',$invoices->toArray())->get();

        if($renters->isEmpty()){
            return $this->fail('no client selected not found');
        }

        foreach ($renters as $row){
            if(in_array('notification',$request->type)) {
              Notification::create([
                    'title' =>$input['title'],
                    'client_id' =>$row->id,
            ]);
                if(!empty($row->firebase_token)) {
                  push_notification($input['title'], $row->firebase_token,$request->invoice_id);
                }
            }

            if(in_array('email',$request->type)&& !empty($row->email)) {
                send_email($row->email,__('Osouly'),$input['title']);
            }
        }

        return $this->success('done');

    }
    
    


}