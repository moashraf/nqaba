<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Student;
use Illuminate\Http\Request;

class spendDateController extends Controller
{
    public function getSpendDate(Request $request){

        $header = $request->input('api_header');
        $memberCode = $request->input('fcode');


        if ($this->checkHeader($header)){

            return $this->getSpendDateData($memberCode);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'User Not Allowed To Access This Data'
            ],200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        }
    }


    public function getSpendDateData($memberCode){
        $file = fopen(asset('files/SpendDateSelf.json'),"r"); // we opened file
        $fileContent = "";
        while(!feof($file)){  // loop file content
            $fileContent .= fread($file,1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true,512,JSON_UNESCAPED_UNICODE);

        foreach ($jsonObjs as $jsonObj){
            if ($jsonObj['F_MEMBER'] == $memberCode){
                return response()->json([
                    'status' => true,
                    'message' => 'Spend Date Found',
                    'spend_date' => $jsonObj
                ],200);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'لم يتم ايجاد المعاش برجاء التواصل مع النقابة'
        ],200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function checkHeader($header){
        if ((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || (count(Student::where('api_header', '=', $header)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }

}
