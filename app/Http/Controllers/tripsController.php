<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Facades\Helpers;
use App\Member;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class tripsController extends Controller
{
    public function getTrips(Request $request){
        $header = $request->input('api_header');


        if ($this->checkHeader($header)){

            return $this->ReadTripsFile();

        }else{
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم لا يمكنه الوصول للبيانات '
            ],200);
        }

    }


    /*
     * get trips cities
     */

    public function getTripsCities(Request $request){

        if ($this->checkHeader($request->input('api_header'))){
            if (File::exists('files/Trips2.Json')){
                $tripsObjs = $this->ReadTripsFile();
                $cities = [];

                foreach($tripsObjs as $tripsObj){
                    if(!in_array(['code'=>$tripsObj['F_CODE'] , 'trip'=>$tripsObj['TRIPNAME']],$cities , true)){

                        array_push($cities, ['code'=>$tripsObj['F_CODE'] , 'trip'=>$tripsObj['TRIPNAME']]);
                    }
                }

               if (count($cities) > 0){
                   return response()->json([
                       "status" => true,
                       "message" => "Trips Found ..",
                       "trips" => $cities
                   ], 200);
               }else{
                   return response()->json([
                       "status" => false,
                       "message" => "لا يوجد رحلات متاحه ..",
                   ], 200);
               }
            } // end file exist if
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
     * get city available weeks for trips
     */

    public function getCityTripsTimes(Request $request){

        if ($this->checkHeader($request->input('api_header'))){
            if (File::exists('files/Trips2.Json')){
                // loop file content
                $tripsObjs = $this->ReadTripsFile();
                $citySchedule = [];
                // loop cities to get one city resorts times
                foreach($tripsObjs as  $tripsObj){
                    if ($tripsObj['F_CODE'] == $request->input('code')){
                        if(!in_array(
                            array(
                                'from' => Helpers::dateParser($tripsObj['TRIP_FROM']),
                                'to' => Helpers::dateParser($tripsObj['TRIP_TO']),
                                'trip' => $tripsObj['TRIPNAME']
                            )
                            , $citySchedule, true)){
                            array_push($citySchedule, [
                                'from' => Helpers::dateParser($tripsObj['TRIP_FROM']),
                                'to' => Helpers::dateParser($tripsObj['TRIP_TO']),
                                'trip' => $tripsObj['TRIPNAME']
                            ]);
                        }
                    }
                }

                if (count($citySchedule) > 0){
                    return response()->json([
                        "status" => true,
                        "message" => "Trips Schedule Listed ..",
                        "trips" => $citySchedule
                    ],200);
                }else{
                    return response()->json([
                        "status" => false,
                        "message" => "لا يوجد رحلات متاحه ..",
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
     * get all trips on this week
     */
    public function getWeekTrips(Request $request){
        if ($this->checkHeader($request->input('api_header'))){

            if (File::exists('files/Trips2.Json')){

                $tripsObjs = $this->ReadTripsFile();
                $weekTrips = [];

                foreach($tripsObjs as $tripsObj){

                    if($tripsObj['F_CODE'] == $request->input('code') && Helpers::dateParser($tripsObj['TRIP_FROM']) == $request->input('from')){
                        // parse all dates before return
                        $tripsObj['START_DATE'] = Helpers::dateParser($tripsObj['START_DATE']);
                        $tripsObj['TRIP_FROM'] = Helpers::dateParser($tripsObj['TRIP_FROM']);
                        $tripsObj['TRIP_TO'] = Helpers::dateParser($tripsObj['TRIP_TO']);
                        $tripsObj['END_DATE'] = Helpers::dateParser($tripsObj['END_DATE']);
                        array_push($weekTrips, $tripsObj);
                    }
                }// end foreach loop

                /*
                 * check if results existed
                 */
                if (count($weekTrips) > 0){
                    return response()->json([
                        "status" => true,
                        "message" => "Trips Existed .. ",
                        "trips" => $weekTrips
                    ],200);
                }else{
                    return response()->json([
                        "status" => false,
                        "message" => "لا يوجد رحلات متاحه ..",
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

    //================================================================

    public function checkHeader($header){
        if ((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || (count(Student::where('api_header', '=', $header)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }

    /*
     * read file
     */

    public function ReadTripsFile()
    {
        $file = fopen(asset('files/Trips2.Json'), "r"); // we opened file
        $fileContent = "";
        while (!feof($file)) {  // loop file content
            $fileContent .= fread($file, 1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true, 512, JSON_UNESCAPED_UNICODE);
        return $jsonObjs;
    }


}
