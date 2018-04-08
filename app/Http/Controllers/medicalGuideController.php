<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Student;
use Illuminate\Http\Request;

class medicalGuideController extends Controller
{
    public function getMedical(Request $request){

        $header = $request->input('api_header');


        if ($this->checkHeader($header)){
            return $this->getMedicalData();
        } else{
            return response()->json([
                'status' => false,
                'message' => 'User Not Allowed To Access This Data'
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

    public function getMedicalData(){
        $file = fopen(asset('files/MedicalGuide.Json'),"r"); // we opened file
        $fileContent = "";
        while(!feof($file)){  // loop file content
            $fileContent .= fread($file,1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true,512,JSON_UNESCAPED_UNICODE);
        if (count($jsonObjs) > 0){
            return response()->json([
                'status' => true,
                'message' => 'Medical Guide listed',
                'medical_guide' => $jsonObjs
            ]);
        }else{ // if medical guide empty
            return response()->json([
                'status' => false,
                'message' => 'No medical guide existed ..',
            ]);
        }


    }
}
