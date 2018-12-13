<?php
/**
 * Created by PhpStorm.
 * User: yuse
 * Date: 2018/12/11
 * Time: 13:06
 */
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PassService
{
    public function apply($applyInfo)
    {
        $car = DB::table('passes')->where('car_number',$applyInfo['car_number'])->first();
        if ($car != null)
        {
            if ($car->status == 1)
            {
                return -1;
            }
            else
            {
                DB::table('passes')->where('car_number',$applyInfo['car_number'])->delete();
            }
        }


        $time = Carbon::now();
        $info = array_merge($applyInfo,[
            'created_at' =>  $time,
            'updated_at'    =>  $time
        ]);
        DB::table('passes')->insert($info);
        return 0;
    }

    public function examine($ids)
    {
        foreach ($ids as $id)
        {
            DB::table('passes')->where('id',$id)->update(['status'  =>  1]);
        }
    }

    public function deletePasses($ids)
    {
        foreach ($ids as $id)
        {
            DB::table('passes')->where('id',$id)->delete();
        }
    }

}