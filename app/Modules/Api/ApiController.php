<?php

namespace App\Modules\Api;

use App\Http\Controllers\Controller;
use App\Models\RequestStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ApiController extends Controller{



    public function __construct(){


    }

    public function index (Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation Error.', $validator->errors());
        }
    }



    protected function success($msg='Done',$data = []){
        return $this->response(true,$msg,$data);
    }

    protected function fail($msg='fail',$data = []){
        return $this->response(false,$msg,$data);
    }

    protected function response($status,$message = 'Done',$data = [],$code = '200'): array {
        return [
            'status'=> $status,
            'code'=> $code,
            'message'=> $message,
            'data'=> $data
        ];
    }



}
