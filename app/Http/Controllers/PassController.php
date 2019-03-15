<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PassService;
use App\Pass;
use Carbon\Carbon;
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
            'phone' => $this->rule['phone']
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
                'code'      =>  '2001',
                'message'   =>  '该车牌已经通过审核'
            ]);
        }


    }
    public function getPasses(Request  $request)
    {
        if ($request->user->role < 100)     //scanner
        {
            $passes = $this->passTable
                ->select('id','name','department','car_number','phone','relation')
                ->where('status',0)
                ->orderBy('created_at')
                ->paginate(10);

        }
        else
        {
            $passes = $this->passTable
                ->where('department',$request->user->department)        //  部门
                ->select('id','name','department','car_number','phone','relation')
                ->where('status',0)
                ->orderBy('created_at')
                ->paginate(10);
        }
        return response([
            'code'  =>  '0',
            'data'  =>  $passes
        ]);
    }

    public function getCheckedPasses(Request $request)
    {

        if ($request->user->role < 100)     //scanner
        {
            $passes = $this->passTable
                ->select('id','name','department','car_number','phone','relation')
                ->orderBy('created_at')
                ->where('status',1)
                ->paginate(10);
        }
        else
        {
            $passes = $this->passTable
                ->where('department',$request->user->department)        //  部门
                ->select('id','name','department','car_number','phone','relation')
                ->orderBy('created_at')
                ->where('status',1)
                ->paginate(10);
        }
        return response([
            'code'  =>  '0',
            'data'  =>  $passes
        ]);
    }

    public function examine(Request $request)
    {
        $this->passService->examine($request->ids,$request->user->department);
        return response([
            'code'  =>  0
        ]);
    }
    public function deletePasses(Request $request)
    {
        $this->passService->deletePasses($request->ids,$request->user->department);
        return response([
            'code'  =>  0
        ]);
    }

    public function getInfoByCarNumber(Request $request)
    {
        $data = $this->passService->getInfoByCarNumber($request->car_number);
        if (sizeof($data) == 0)
        {
            return response([
                'code'  =>  2002,
                'message'   =>  '非管理下的车牌'
            ]);
        }
        else
        {
            return response([
                'code'  =>  0,
                'data'  =>  $data
            ]);
        }

    }
    public function getInfoByLike(string $carNumber)
    {
        $data = $this->passService->getInfoByLike($carNumber);
        if (sizeof($data) == 0)
        {
            return response([
                'code'  =>  2002,
                'message'   =>  '非管理下的车牌'
            ]);
        }
        else
        {
            return response([
                'code'  =>  0,
                'data'  =>  $data
            ]);
        }
    }

    public function getQRCode(string $ids)
    {
        $data = $this->passService->getQRCode(explode('+',$ids));
        $time = Carbon::now();
        $name = '二维码'.$time.'.zip';
        return response()->download($data,$name)->deleteFileAfterSend(true);
    }

}
