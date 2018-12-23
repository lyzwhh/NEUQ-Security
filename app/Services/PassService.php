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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Filesystem\Filesystem;
use Chumper\Zipper\Zipper;


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

    public function getInfoByCarNumber($carNumber)
    {
        $car = DB::table('passes')->select('id','name','department','car_number','phone')->where('car_number',$carNumber)->get();
        return $car;
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

    public function getQRCode($ids)
    {
        $file = new Filesystem();
        $file->cleanDirectory('QRCodes');

        foreach ($ids as $id)
        {
            $pass = DB::table('passes')->where('id',$id)->first();
            if ($pass == null)
            {
                continue;
            }

            $info = $pass->car_number;
            $fileName = $pass->name.' '.$info;
            QrCode::format('png')->size(1000)->merge('/public/logo.png',.3)->errorCorrection('H')->generate($info, '../public/QRCodes/'.$fileName.'.png');

            $dst_path = 'QRCodes/'.$fileName.'.png';
            $img = imagecreatefromstring(file_get_contents($dst_path));
            $font = 'font/simhei.ttf';
            $black = imagecolorallocate($img, 0x00, 0x00, 0x00);//字体颜色
            imagefttext($img, 33, 0, 0, 33, $black, $font, $info);
            imagepng($img,'QRCodes/'.$fileName.'.png');

        }

        $zipper = new Zipper();
        $public_path = 'QRCodes/';
        $arr = glob(public_path($public_path));
        $zipper->make(public_path('zips/QRcodes.zip'))->add($arr)->close();

        return public_path('zips/QRcodes.zip');
    }
}