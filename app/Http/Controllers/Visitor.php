<?php

namespace App\Http\Controllers;

use App\Facades\Helpers;
use App\Image;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class Visitor extends Controller
{
     
 
    public function register(Request $request){



   $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nationalNo' => 'required',
            'mobile' => 'required',
            'regToken' => 'required',
            'username' => 'required', 
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
        'message' => 'برجاء ادخال  البيانات كالمه   ...'
        ],200);
        }
        
        $email = $request->input('email');
        $checkEmail = Member::where('email' , '=', $email)->first();
        if(! $checkEmail){
            if($request->file('pic')){
            $file = $request->file('pic');
            $imageName = time().$file->getClientOriginalName();
            $moving = $file->move(public_path().'/images',$imageName);
            if ($moving){
                Image::create([
                    'path' => $imageName
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ اثناء رفع الصوره'
                ],200);
            }
        }

        $visitor = new Member([
            'name' => $request->input('name'),
            'nationalNo' => $request->input('nationalNo'),
			'mobile' => $request->input('mobile'),
            'password'=> bcrypt($request->input('password')),
            'username' => $request->input('username'),
            'api_header' =>  'mem'.str_random(40),
            'regToken' => $request->input('regToken'),
            'email' => $request->input('email'),
            'Type' => 'Visitor' ,
            

            //'graduate' => Helpers::dateParser($request->input('graduate')),
           // 'pic' => $imageName
        ]);

        if ($visitor->save()){
            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل العضو بنجاح..',
                'visitor' => $visitor
            ],201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'خطأ اثناء تسجيل العضو ..'
            ],200);
        }
        }else{
            return response()->json([
                    'status' => false,
                    'message' => 'هذا الايميل موجود من قبل ..'
                ],200);
        }
        
    }
    
    
    
    
    
      public function forget(Request $request){

$user = Member::where('email', $request->input('email')) ->first();

        if (!is_null($user))
        {
            $activate = (rand(1000, 3000));
            $to = "$user->email";
            $subject = "nqaba email";
            $message = Helpers::mail_code($activate, $user->email);
            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // More headers
            $headers .= 'From: <info@nqaba.com>' . "\r\n";
           // mail($to, $subject, $message, $headers);
            $user->activate = $activate;
            $user->save();

            return Helpers::returnJsonResponse(true, '     successfully  send ...', $user);
        }
        else
        {
            return Helpers::returnJsonResponse(false, ' code  not send ...', null);
        }




      }
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
           // 'email' => 'required',
            'password' => 'required|min:1'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'برجاء ادخال الايميل والباسورد...'
            ],200);
        }

        $email = $request->input('email');
        $username = $request->input('username');
        $password = $request->input('password');


if($request->input('email')){
   
             $user = Member::where('email','=',$email)->first();
           if (! $user){
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم غير مسجل لدينا ..'
            ],200);
        }

        
        
}



elseif ($request->input('username')){
     
         $user = Member::where('username','=', $username)->first();
           if (! $user){
            return response()->json([
                'status' => false,
                'message' => 'هذا المستخدم غير مسجل لدينا ..'
            ],200);
        }

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
