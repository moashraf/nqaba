<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Mail\PasswordReset;
use App\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class passwordResetController extends Controller
{

    public function __construct()
    {
        $expiredRequests = ResetPassword::where('created_at', '<', Carbon::now()->subDay(2))->where('valid','=',1)->get();
        foreach($expiredRequests as $expiredRequest){
            $expiredRequest->valid = 0;
            $expiredRequest->save();
        }
    }

    public function passwordReset(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'برجاء ادخال الايميل...'
            ],200);
        }

        $email = $request->input('email');

        if ($this->checkEmail($email)){ // if email exists

            $token = str_random(40);
            $reset = new ResetPassword();
            $link = URL::to('/').'/password/reset?token='.$token;
            $reset->email = $email;
            $reset->token = $token;

            if ($reset->save()){
                // if mail server installed "inside if()"
                // $this->sendEmail($email, $link)
                //Mail::to($email)->send(new PasswordReset($email,$link));

                if ($this->sendEmail($email, $link)){
                    return response()->json([
                        'status' => true,
                        'message' => 'تم ارسال رساله تغيير الباسورد ..'
                    ]);
                }else{ // if sent email fails
                    return response()->json([
                        'status' => false,
                        'message' => 'خطا اثناء ارسال الايميل ..'
                    ]);
                }

            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'خطا اثناء تغيير الباسورد ..'
                ]);
            }



        }else{ // if exail exists else
            return response()->json([
                'status' => false,
                'message' => 'هذا الايميل غير مسجل لدينا ..'
            ]);
        }






    }

    public function checkEmail($email){
        if ((count(Member::where('email', '=', $email)->get()) > 0) || (count(Employee::where('email', '=', $email)->get()) > 0) || (count(Student::where('email', '=', $email)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }

    public function findUser($email){
        if ((count($user = Member::where('email', '=', $email)->first()) > 0) || (count($user = Employee::where('email', '=', $email)->first()) > 0) || (count($user = Student::where('email', '=', $email)->first()) > 0)){
            return $user;
        }else{
            return false;
        }
    }

    public function sendEmail($email, $link){

        $to = $email ; // note the comma
        $subject = 'نقابة العلميين - تغيير كلمه السر.';
        $message = '
                    <html>
                    <head>
                      <title>نقابة العلميين - تغيير كلمه السر</title>
                    </head>
                    <body>
                      <h3>تغيير كلمة السر</h3><br>
                        <h5> لتغيير كلمه المرور للحساب الخاص بالبريد الالكترونى '.$email.' اضغط على الرابط بالاسفل , يمكنك تجاهل هذه الرساله اذا لا تريد تغيير كلمه السر
                            <br> هذا الرابط فعال لمدة 48 ساعه</h5>
                        <p> '.$link.' </p>
                    </body>
                    </html>
                    ';
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        if (mail($to, $subject, $message, implode("\r\n", $headers))){
            return true;
        }else{
            return false;
        }

    }

    public function resetPage(){

        if (isset($_GET['token'])){

            $token = $_GET['token'];
            $resetRequest = ResetPassword::where('token','=',$token)->where('valid','=',1)->first();
            if(count($resetRequest) > 0){

                return view('reset.new', ['resetRequest'=>$resetRequest , 'token'=>$token]);

            }else{
                return view('reset.message', ['message' => 'خطأ برجاء المحاوله مره اخرى ..']);
            }

        }
        else
        {
            return view('reset.message', ['message' => 'خطأ برجاء المحاوله مره اخرى ..']);
        }


    }

    public function updatePassword(Request $request)
    {
        if ($user = $this->findUser($request->email)){

            $user->password = bcrypt($request->password);

            if ($user->save()){
                // to unvalid the token for this request so user cant use this url again
                $resetRequest = ResetPassword::where('token', '=', $request->token)->first();
                $resetRequest->valid = 0;
                $resetRequest->save();
                return view('reset.message', ['message' => 'تم تحديث الباسورد ..']);
            }else{
                return view('reset.message', ['message' => 'خطأ برجاء المحاوله مره اخرى ..']);
            }
        }
    }

}
