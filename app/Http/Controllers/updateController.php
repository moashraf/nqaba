<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Image;
use App\Member;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class updateController extends Controller
{
    public function index(Request $request){

        $type = substr($request->input('api_header') , 0 , 3);
        if ($type == 'mem'){
            return $this->update($request, $type);
        }else if ($type == 'stu'){
            return $this->update($request, $type);
        }else if($type == 'emp'){
            return $this->update($request, $type);

        }else{
            return response()->json([
               'status' => false,
               'message' => 'هذا العضو غير مسجل لدينا'
            ]);
        } // end else
    }

    public function update(Request $request, $type){
        switch ($type){
            case 'mem':
                $user = Member::where('api_header','=',$request->input('api_header'))->first();
                break;
            case 'stu' :
                $user = Student::where('api_header','=',$request->input('api_header'))->first();
                break;
            case 'emp' :
                $user = Employee::where('api_header','=',$request->input('api_header'))->first();
                break;
        }

        if (count($user) > 0){

            // check if password set
            if (trim($request->input('password')) == '') {
                $updatedData = $request->except('password');
            }
            else {
                $updatedData = $request->all();
                $updatedData['password'] = bcrypt($request->input('password'));
            }

            if($file = $request->file('pic')){
                $imageName = time().$file->getClientOriginalName();
                $moving = $file->move(public_path().'/images',$imageName);
                if ($moving){
                    Image::create([
                        'path' => $imageName
                    ]);
                    $updatedData['pic'] = $imageName;
                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'خطا اثناء رفع الصوره'
                    ],200);
                }
            }

            if ($user->update($updatedData)){
                return response()->json([
                   'status' => true,
                   'message' => 'تم تحديث بيانات العضو'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'خطا اثناء تحديث بيانات العضو'
                ]);
            }

        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا العضو غير مسجل لدينا'
            ]);
        }
    }

}
