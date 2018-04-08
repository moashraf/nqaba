<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Image;
use Illuminate\Http\Request;

class employeesController extends Controller
{
    public function index(){

    }

    public function getEmployee($id){
        $employee = Employee::find($id);
        if (count($employee) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم ايجاد الموظف ..',
                'employee' => $employee
            ], 201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'هذا الموظف غير مسجل لدينا ..'
            ],201);
        }
    }

    public function getEmployees(){
        $employees = Employee::all();
        if (count($employees) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم ارسال بيانات الموظفين',
                'employee' => $employees
            ], 201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد موظفين مسجلين'
            ],201);
        }
    }

    public function postEmployees(Request $request){

        $email = $request->input('email');
        $checkEmail = Employee::where('email', '=', $email)->first();
        
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

        $employee = new Employee([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'=> bcrypt($request->input('password')),
            'reg_token' => $request->input('reg_token'),
            'api_header' =>  'emp'.str_random(40),
            'emp_id' => $request->input('emp_id'),
            'job' => $request->input('job'),
            'nat_id' => $request->input('nat_id'),
            'mobile' => $request->input('mobile'),
            'pic' => $imageName
        ]);

        if ($employee->save()){
            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل بيانات الموظف..',
                'employee' => $employee
            ],201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'خطا اثناء تسجيل الموظف ..'
            ],201);
        }
        }else{
            return response()->json([
                    'status' => false,
                    'message' => 'هذا الايميل مستخدم من قبل .. '
                ],200);
        }
        
    }
}
