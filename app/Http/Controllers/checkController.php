<?php

namespace App\Http\Controllers;


use App\Employee;
use App\Member;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class checkController extends Controller
{
    public function checkMember(Request $request){
        
        $validator = Validator::make($request->all(), [
            'nat_id' => 'required|min:3',
            'phone' => 'required|min:3'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'برجاء ادخال البيانات...'
            ],200);
        }

        $nat_id = $request->input('nat_id');
        $phone = $request->input('phone');



        $client = new Client();

        $response = $client->request(
            'POST',
            'https://protected-gorge-62577.herokuapp.com/check-members/',
            [
                'form_params' => [
                    'nat_id' => $nat_id,
                    'phone' => $phone
                ]
            ]
        );

        $result = $response->getBody()->getContents();
        $member = json_decode($result);

        if( count($member) > 0){ // if member existed in json file


            if (count(Member::where('fcode', '=', $member->F_CODE)->get()) > 0){ // if member existed in my app db
                return response()->json([
                    "status" => false,
                    "message" => ' هذا العضو مسجل من قبل '
                ],200);
            }

            return response()->json([
                "status" => true,
                "message" => 'هذا العضو موجود بالنقابه ..',
                "member" => $member
            ],200);
        }else{
            return response()->json([
                "status" => false,
                "message" => 'برجاء الرجوع للنقابه لتحديث بياناتك ..'
            ],200);
        }


    }

    public function checkEmployee(Request $request){
        
        $validator = Validator::make($request->all(), [
            'nat_id' => 'required|min:3',
            'phone' => 'required|min:3'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'برجاء ادخال البيانات...'
            ],200);
        }


        $file = fopen(asset('files/Employers.Json'),"r"); // we opened file
        $fileContent = "";
        while(!feof($file)){  // loop file content
            $fileContent .= fread($file,1024);  // read every 1 mb data
        }
        fclose($file);

        $jsonObjs = json_decode($fileContent, true,512,JSON_UNESCAPED_UNICODE);

        $phone = $request->input('phone');
        $natId = $request->input('nat_id');


        foreach($jsonObjs as $jsonobj){
            $F_MOBILE = $jsonobj['F_MOBILE_NO'];
            $F_NATIONNO = $jsonobj['NATIONALNO'];

            if ($F_MOBILE == $phone && $F_NATIONNO == $natId){

                if (count(Employee::where('emp_id', '=', $jsonobj->F_EMPID)->get()) > 0){

                    return response()->json([
                        "status" => false,
                        "message" => ' هذا العضو مسجل من قبل '
                    ],200);

                }else{
                    return response()->json([
                        "status" => true,
                        "message" => "هذا العضو موجود بالنقابه ..",
                        "member" => $jsonobj
                    ],200);
                }
            }
        }


        return response()->json([
            "status" => false,
            "message" => "برجاء الرجوع للنقابه لتحديث بياناتك .."
        ],200);





    }

}
