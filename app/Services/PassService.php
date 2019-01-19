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
use ZipArchive;


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
        $car = DB::table('passes')->select('id','name','department','car_number','phone','status')->where('car_number',$carNumber)->where('status',1)->get();
        return $car;
    }

    public function getInfoByLike($carNumber)
    {
        $car = DB::table('passes')->select('id','name','department','car_number','phone','status')->where('car_number','like','%'.$carNumber.'%')->where('status',1)->get();
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

    public function madePasses($ids)
    {
        $time = Carbon::now();
        foreach ($ids as $id)
        {
            DB::table('passes')->where('id',$id)->update(['made_date' => $time]);
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
            $fileName = $pass->department.'-'.$pass->name.'-'.$info;
            $fileName = iconv('UTF-8', 'GBK', $fileName);
            QrCode::format('png')->encoding('UTF-8')->size(1000)->merge('/public/logo.png',.3)->errorCorrection('H')->generate('1#'.$info, '../public/QRCodes/'.$fileName.'.png');
                                                                                                                    //generate（二维码内容，地址）
            $dst_path = 'QRCodes/'.$fileName.'.png';
            $img = imagecreatefromstring(file_get_contents($dst_path));
            $font = 'font/simhei.ttf';
            $black = imagecolorallocate($img, 0x00, 0x00, 0x00);//字体颜色
            imagefttext($img, 33, 0, 0, 35, $black, $font, $info);
            imagepng($img,'QRCodes/'.$fileName.'.png');

        }

//        $zipper = new Zipper();
//        $public_path = 'QRCodes/';
//        $arr = glob(public_path($public_path));
//        $zipper->make(public_path('zips/QRcodes.zip'))->add($arr)->close();
        $public_path = 'QRCodes/';
        $arr = array();
        $arr[0] = null;
        $arr = glob(public_path($public_path));
        if(isset($arr[0]))
        self::zipDir($arr[0],'zips/QRcodes.zip');
        else
        {
            return public_path($public_path);
        }


        self::madePasses($ids);
        return public_path('zips/QRcodes.zip');
    }

    function zipDir($basePath,$zipName){
        $zip = new ZipArchive();
        $fileArr = [];
        $fileNum = 0;
        if (is_dir($basePath)){
            if ($dh = opendir($basePath)){
                $zip->open($zipName,ZipArchive::CREATE);
                while (($file = readdir($dh)) !== false){
                    if(in_array($file,['.','..',])) continue; //无效文件，重来
                    $file = iconv('gbk','utf-8',$file);
                    $extension = strchr($file,'.');
                    rename(iconv('UTF-8','GBK',$basePath.'/'.$file), iconv('UTF-8','GBK',$basePath.'/'.$fileNum.$extension));
                    $zip->addFile($basePath.'/'.$fileNum.$extension,$fileNum.$extension);
                    $zip->renameName($fileNum.$extension,$file);
                    $fileArr[$fileNum.$extension] = $file;
                    $fileNum++;
                }
                $zip->close();
                closedir($dh);
                foreach($fileArr as $k=>$v){
                    rename(iconv('UTF-8','GBK',$basePath.'/'.$k), iconv('UTF-8','GBK',$basePath.'/'.$v));
                }
            }
        }
    }
}