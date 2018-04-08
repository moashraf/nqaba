<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Member;
use App\Employee;

class branchesController extends Controller
{
    public function getBranches(Request $request){


        $header = $request->input('api_header');

        if ($this->checkHeader($header)){

            return $this->getBranchesData();
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا العضو لا يمكنه مشاهده البيانات'
            ]);
        }



    }


    public function getBranchesData(){
        $file = fopen(asset('files/BranchDsc.Json'),"r"); // we opened file
        $fileContent = "";
        while(!feof($file)){  // loop file content
            $fileContent .= fread($file,1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true,512,JSON_UNESCAPED_UNICODE);

        if (count($jsonObjs) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم ارسال الفروع ..',
                'Branches' => $jsonObjs
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد فروع متاحه ..'
            ]);
        }


    }

    public function checkHeader($header){
        if ((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || (count(Student::where('api_header', '=', $header)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }


}
