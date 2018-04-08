<?php

namespace App\Http\Controllers;

use App\Image;
use App\Student;
use Illuminate\Http\Request;

class studentsController extends Controller
{
    public function index(){

    }

    public function getStudent($id){
        $student = Student::find($id);
        if (count($student) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم تحميل بيانات الطالب ..',
                'data' => $student
            ], 201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'هذا الطالب غير مسجل لدينا ..'
            ],200);
        }
    }

    public function getStudents(){
        $student = Student::all();
        if (count($student) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم تحميل بيانات الطلاب',
                'data' => $student
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد طلاب حتى الان'
            ],200 );
        }
    }

    public function postStudents(Request $request){
        
        
        $email = $request->input('email');
        $checkEmail = Student::where('email', '=', $email)->first();
        
        if(! $checkEmail){
            
            if($request->file('pic')){
            $file = $request->file('pic');
            $imageName = time().$file->getClientOriginalName();
            $moving = $file->move(public_path().'/images',$imageName);
            if ($moving){
                Image::create([
                   'path' => $imageName
                ]);
            }
        }

        $student = new Student([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'=> bcrypt($request->input('password')),
            'reg_token' => $request->input('reg_token'),
            'api_header' =>  'stu'.str_random(40),
            'nat_id' => $request->input('nat_id'),
            'ac_year' => $request->input('ac_year'),
            'university' => $request->input('university'),
            'collage' => $request->input('collage'),
            'dep' => $request->input('dep'),
            'mobile' => $request->input('mobile'),
            'pic' => $imageName
        ]);

        if ($student->save()){
            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل الطالب بنجاح..',
                'student' => $student
            ],201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'خطا اثناء تسجيل الطالب ..'
            ],200 );
        }
            
        }else{
            return response()->json([
                    'status' => false,
                    'message' => 'هذا الايميل موجود من قبل .. '
                ],200);
        }
        

    }
}
