<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class signinController extends Controller
{
    public function signin(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'برجاء ادخال الايميل والباسورد...'
            ],200);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = Employee::where('email','=',$email)->first();

        if (! $user)
            $user = Member::where('email','=',$email)->first();
        if (! $user)
            $user = Student::where('email','=',$email)->first();
        if (! $user){
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم غير مسجل لدينا ..'
            ],200);
        }




        // check if password true
        if(Hash::check($password, $user->password)){
            return response()->json([
                'status' => true,
                'message' => 'تم تسجبل الدخول بنجاح ..',
                'user' => $user
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'خطأ فى الباسورد ..'
            ],200);
        }

    }



}
