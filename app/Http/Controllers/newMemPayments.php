<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class newMemPayments extends Controller
{
    public function getNewMemPayments(){


        if(File::exists('files/MemNewPayment.Json')){
            $file = fopen(asset('files/MemNewPayment.Json'),"r"); // we opened file
            $fileContent = "";
            while(!feof($file)){  // loop file content
                $fileContent .= fread($file,1024);  // read every 1 mb data
            }
            fclose($file);

            $jsonObjs = json_decode($fileContent, true,512,JSON_UNESCAPED_UNICODE);

            return response()->json([
                "status" => true,
                "message" => "تم تحميل المصاريف المستحقه ..",
                "Payments" => $jsonObjs
            ],200);

        }else{

            return response()->json([
                "status" => false,
                "message" => "لا يوجد مصاريف مستحقه ..",
            ],200);

        }


    }



    public function getNewMemPayment(Request $request){

        if(File::exists('files/MemNewPayment.Json')){
            $file = fopen(asset('files/MemNewPayment.Json'),'r');
            $fileContent = "";
            while(!feof($file)){
                $fileContent .= fread($file , 1024);
            }
            fclose($file);
            $jsonObjs = json_decode($fileContent,true, 512, JSON_UNESCAPED_UNICODE);

            foreach ($jsonObjs as $jsonObj){
                if($jsonObj['F_GRADUATED_YEAR'] == $request->input('graduate_year')){
                    return response()->json([
                        "status" => true,
                        "message" => "تم ايجاد مصاريف مستحقه ..",
                        "payments" => $jsonObj
                    ],200);
                }
            }

            return response()->json([
                "status" => false,
                "message" => "لا يوجد مصاريف مستحقه ..",
            ],200);

        }
    }
}
