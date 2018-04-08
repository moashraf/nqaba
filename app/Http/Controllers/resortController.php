<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class resortController extends Controller
{

    // "F_HCODE": city code, "F_WEEK" : week code ,

    public function getResorts(Request $request){

        if ($this->checkHeader($request->input('api_header'))){
            return $this->getResortsData();
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمكنه الوصول للبيانات'
            ],200);
        }



    }

    /*
     * get resorts cities
     */

    public function getResortsCities(Request $request){

        if ($this->checkHeader($request->input('api_header'))){
            if (File::exists('files/ResortNotRcr.Json')){
                $jsonObjs = $this->ReadResortsFile();
                $cities = [];

                foreach($jsonObjs as $jsonObj){
                    if(!in_array(['code'=>$jsonObj['F_HCODE'] , 'resort'=>$jsonObj['HOLDAY_NAME']],$cities , true)){
                        array_push($cities, ['code'=>$jsonObj['F_HCODE'] , 'resort'=>$jsonObj['HOLDAY_NAME']]);
                    }
                }

                return response()->json([
                    "status" => true,
                    "message" => "تم تحميل المصايف ..",
                    "resorts" => $cities
                ], 200);
            } // end file exist if
            else{
                return response()->json([
                    "status" => false,
                    "message" => "لا يوجد مصايف حاليا ..",
                ], 200);
            }
        }// if header true
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمكنه الوصول للبيانات'
            ]);
        }

    }

    /*
     * Get all City Resorts By  Time
     */

    public function getCityResTimes(Request $request){

        if ($this->checkHeader($request->input('api_header'))){
            if (File::exists('files/ResortNotRcr.Json')){
                // loop file content
               $jsonObjs = $this->ReadResortsFile();
                $citySchedule = [];
                // loop cities to get one city resorts times
                foreach($jsonObjs as  $jsonObj){
                    if ($jsonObj['F_HCODE'] == $request->input('code')){
                        if(!in_array(['from' => $jsonObj['F_FROM'],'to' => $jsonObj['F_TO'], 'resort' => $jsonObj['HOLDAY_NAME']]
                            , $citySchedule, true)){
                            array_push($citySchedule, [
                                'from' => $jsonObj['F_FROM'],
                                'to' => $jsonObj['F_TO'],
                                'resort' => $jsonObj['HOLDAY_NAME']
                            ]);
                        }
                    }
                }

                if (count($citySchedule) > 0){
                    return response()->json([
                        "status" => true,
                        "message" => "تم تحميل الافواج ..",
                        "resorts" => $citySchedule
                    ],200);
                }else{
                    return response()->json([
                        "status" => false,
                        "message" => "لا يوجد افواج متاحه ..",
                    ], 200);
                }

            }
            else{
                return response()->json([
                    "status" => false,
                    "message" => "لا يوجد بيانات متاحه ..",
                ], 200);
            }
        }// if header true
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمكنه الوصول للبيانات'
            ]);
        }

    }



    /*
     *  get week's all resorts
     */


    public function getWeekResorts(Request $request){
        if ($this->checkHeader($request->input('api_header'))){

            if (File::exists('files/ResortNotRcr.Json')){

                $jsonObjs = $this->ReadResortsFile();
                $weekResorts = [];

                foreach($jsonObjs as $jsonObj){
                    if($jsonObj['F_HCODE'] == $request->input('code') && $jsonObj['F_FROM'] == $request->input('from')){
                            array_push($weekResorts, $jsonObj);
                    }
                }// end foreach loop

                if (count($weekResorts) > 0){
                    return response()->json([
                        "status" => true,
                        "message" => "تم تحميل الاسابيع المتاحه .. ",
                        "resorts" => $weekResorts
                    ],200);
                }else{
                    return response()->json([
                        "status" => false,
                        "message" => "لا يوجد اسابيع متاحه ..",
                    ], 200);
                }


            }else{
                return response()->json([
                    "status" => false,
                    "message" => "لا يوجد بيانات متاحه ..",
                ], 200);
            }

        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمكنه الوصول للبيانات'
            ]);
        }
    }



    /*
     *    get All resorts data
     */

    public function getResortsData(){


        $jsonObjs = $this->ReadResortsFile();

            return response()->json([
                'status' => true,
                'message' => 'resorts listed',
                'resorts' => $jsonObjs
            ],200);


    }



    /*
     * check if api header for existed user
     */

    public function checkHeader($header){
        if((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || count(Student::where('api_header', '=', $header)->get()) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function ReadResortsFile()
    {
        $file = fopen(asset('files/ResortNotRcr.Json'), "r"); // we opened file
        $fileContent = "";
        while (!feof($file)) {  // loop file content
            $fileContent .= fread($file, 1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true, 512, JSON_UNESCAPED_UNICODE);
        return $jsonObjs;
    }

}
