<?php

namespace App\Modules\System;

use App\Http\Requests\BanksBranchesFormRequest;
use App\Models\BankBranch;
use Illuminate\Http\Request;
use Form;
use Auth;
use App;
use Illuminate\Support\Facades\DB;

class BanksBranchesController extends SystemController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){



        if($request->isDataTable){

            $eloquentData = BankBranch::select([
                'id',
                'branch_code',
                'bank_code',
                'name_ar',
                'name_en',
                'created_at'
            ]);
            //dd($eloquentData);


            if($request->withTrashed){
                $eloquentData->onlyTrashed();
            }


            whereBetween($eloquentData,'DATE(banks_branches.created_at)',$request->created_at1,$request->created_at2);


            if($request->id){
                $eloquentData->where('banks_branches.id',$request->id);
            }

            if($request->bank_code){
                $eloquentData->where('banks_branches.bank_code',$request->bank_code);
            }

            if($request->branch_code){
                $eloquentData->where('banks_branches.branch_code',$request->branch_code);
            }


            if($request->name_ar){
                $eloquentData->where('banks_branches.name_ar','LIKE','%'.$request->name_ar.'%');
            }

            if($request->name_en){
                $eloquentData->where('banks_branches.name_en','LIKE','%'.$request->name_en.'%');
            }

            return datatables()->eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('branch_code','{{$branch_code}}')
//                ->addColumn('bank_code','{{$bank_code}}')
                ->addColumn('bank_code', function($data){
                    $bank_link = $data->bank_code .'<br>( غير مسجل )';
                    if($data->bank){
                        $bank_link = '<a href="javascript:void(0)" rel="'.$data->bank_code.'" class="bank-code-link">'.$data->bank_code.' <br> '.$data->bank->{'name_'.lang()} .'</a>';
                    }
                    return $bank_link;
                })
                ->addColumn('name_ar','{{$name_ar}}')
                ->addColumn('name_en','{{$name_en}}')
                ->addColumn('created_at', function($data){
                    return $data->created_at->format('Y-m-d h:iA') . '<br /> ('.$data->created_at->diffForHumans().')';
                })
//                ->addColumn('action', function($data){
//                    return '<span class="dropdown">
//                            <a href="#" class="btn btn-md btn-clean btn-icon btn-icon-md" data-toggle="dropdown" aria-expanded="false">
//                              <i class="la la-gear"></i>
//                            </a>
//                            <div class="dropdown-menu '.( (\App::getLocale() == 'ar') ? 'dropdown-menu-left' : 'dropdown-menu-right').'" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-36px, 25px, 0px);">
//                                <a class="dropdown-item" href="'.route('system.lead.show',$data->id).'"><i class="la la-search-plus"></i> '.__('View').'</a>
//                            </div>
//                        </span>';
//                })
                ->escapeColumns([])
                ->make(false);
        }else{
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Branch Code'),
                __('Bank Code'),
                __('Name (Arabic)'),
                __('Name (English)'),
                __('Created At'),
//                __('Action')
            ];

            $this->viewData['breadcrumb'][] = [
                'text'=> __('Banks Branches')
            ];

            if($request->withTrashed){
                $this->viewData['pageTitle'] = __('Deleted Banks Branches');
            }else{
                $this->viewData['pageTitle'] = __('Banks Branches');
            }

            return $this->view('bank-branch.index',$this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text'=> __('Banks Branches'),
            'url'=> route('system.bank-branch.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text'=> __('Create Banks Branches'),
        ];

        $this->viewData['pageTitle'] = __('Create Banks Branches');

        return $this->view('bank-branch.create',$this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BanksBranchesFormRequest $request){

        $file = $request->file('file')->store(setting('system_path').'/banks/'.date('Y/m/d'),'first_public');

        try {
            $spreadsheet =  \PhpOffice\PhpSpreadsheet\IOFactory::load(base_path('public/'.$file))
                ->getActiveSheet()
                ->toArray(null,true,true,true);
        }catch (\Exception $e){
            if(is_file($file)){
                unlink($file);
            }
            return $this->response(
                false,
                11001,
                __('Please check the XLS file. Some columns are invalid') . ' :  ('.$e->getMessage().')'
            );
            // return $e;
        }

        if(count($spreadsheet) < 2){
            if(is_file($file)){
                unlink($file);
            }
            return $this->response(
                false,
                11001,
                __('Empty XLS file')
            );
        }

        if($request->ignore_first_row == 'yes'){
            unset($spreadsheet[1]);
        }

        $i = 0;
        BankBranch::truncate(); //empty banks_branches table before add new
        foreach ($spreadsheet as $key => $value) {
            if (
                !isset($value[strtoupper($request->columns_data_name_ar)]) ||
                !isset($value[strtoupper($request->columns_data_name_en)]) ||
                !isset($value[strtoupper($request->columns_data_branch_code)]) ||
                !isset($value[strtoupper($request->columns_data_bank_code)])
            ) continue;

            $name_ar = @$value[strtoupper($request->columns_data_name_ar)];
            $name_en = @$value[strtoupper($request->columns_data_name_en)];
            $bank_code = @$value[strtoupper($request->columns_data_bank_code)];
            $branch_code = @$value[strtoupper($request->columns_data_branch_code)];

            BankBranch::create([
                'bank_code' => $bank_code,
                'branch_code' => $branch_code,
                'name_ar' => $name_ar,
                'name_en' => $name_en
            ]);

            $i++;
        }

        if(!$i){
            if(is_file($file)){
                unlink($file);
            }
            return $this->response(
                false,
                11001,
                __('corrupted XLS file')
            );
        }


        if(is_file($file)){ // remove file to empty space
            unlink($file);
        }
        return $this->response(
            true,
            11001,
            __('Successfully added ( :num ) Data from ( :all )',['num'=>$i,'all'=>count($spreadsheet)])
        );


//            return $this->response(
//                true,
//                200,
//                __('Data added successfully'),
//                [
//                    'url'=> route('system.lead.show',$insertData->id)
//                ]
//            );

    }


}