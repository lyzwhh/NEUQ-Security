<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PassService;
use App\Pass;
class PassController extends Controller
{
    //
    private $passService;
    private $passTable;
    public function __construct(PassService $passService,Pass $pass)
    {
        $this->passService = $passService;
        $this->passTable = $pass;
    }

    private $rule = [
        'name'          =>  'required',
        'department'    =>  'required',
        'car_number'    =>  'required',
        'phone'         =>  'required|regex:/^1[34578]{1}\d{9}$/',
        'relation'      =>  'required'
        ];
    public function apply(Request $request)
    {
        $this->validate($request,[
            'name' => $this->rule['name'],
            'department' => $this->rule['department'],
            'car_number' => $this->rule['car_number'],
            'phone' => $this->rule['phone'],
            'relation' => $this->rule['relation']
        ]);
        $applyIfo = $request->all();
        $status = $this->passService->apply($applyIfo);
        if ($status == 0)
        {
            return response([
                'code'      =>  '0',
                'message'   =>  '提交成功'
            ]);
        }
        else
        {
            return response([
                'code'      =>  '301',
                'message'   =>  '该车牌已经提交过'
            ]);
        }


    }
    public function getPasses(int $limit,int $offset)
    {
        $passes = $this->passTable
            ->select('id','name','department','car_number','phone','relation')
            ->orderBy('created_at')
            ->offset($offset)
            ->where('status',0)
            ->limit($limit)
            ->get();
        return response([
            'code'  =>  '0',
            'data'  =>  $passes
        ]);
    }
    public function examine(Request $request)
    {
        $this->passService->examine($request->ids);
        return response([
            'code'  =>  0
        ]);
    }
}
