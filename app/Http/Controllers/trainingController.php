<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Student;
use Illuminate\Http\Request;

class trainingController extends Controller
{
    public function getCourses(Request $request){
        $header = $request->input('api_header');


        if ($this->checkHeader($header)){

            return $this->getCoursesData();
        }else{
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمكنه الوصول للبيانات'
            ]);
        }

    }

    public function getCoursesData(){
        $file = fopen(asset('files/Tarining.Json'),"r"); // we opened file
        $fileContent = "";
        while(!feof($file)){  // loop file content
            $fileContent .= fread($file,1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true,512,JSON_UNESCAPED_UNICODE);

        return response()->json([
            'status' => true,
            'message' => 'Courses listed',
            'courses' => $jsonObjs
        ],200);
    }

    public function checkHeader($header){
        if ((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || (count(Student::where('api_header', '=', $header)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }

}
