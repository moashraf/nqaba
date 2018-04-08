<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Member;
use App\Message;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    public function createMessages(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'body' => 'required|min:10',
            'option' => 'required',
            'value' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Error , Missing inputs...'
            ],200);
        }

        // inside if : $message = Message::create($request->all())

        if ($message = Message::create($request->all())){ // if message created send notification


        $option = $request->input('option');
        $value = $request->input('value');
            if ($option > 6 || $option < 1)
                $option = '6';

            switch ( $option ){
                case '1' :
                    $members = Member::where('gender', '=', $value)->get();
                    break;
                case '2' :
                    $members = Member::where('elec_branch', '=', $value)->get();
                    break;
                case '3' :
                    $members = Member::where('graduate', '=', $value)->get();
                    break;
                case '4' :
                    $members = Member::where('dep', '=', $value)->get();
                    break;
                case '5' :
                    $members = Member::where('gov', '=', $value)->get();
                    break;
                case '6' :
                    $members = Member::all();

            }

            if (count($members) < 1){
                return response()->json([
                    'status' => false,
                    'message' => 'No Users in this category...'
                ],200);
            }
                // message inserted and we get all $members should recieve the notification


            return response()->json([
                'status' => true,
                'message' => 'Message Inserted Successfully ..',
                'Message' => $message
            ],200);


        }else{ // else message created
            return response()->json([
                'status' => false,
                'message' => 'Error Creating Message...'
            ],200);
        }

    }

    public function readMessages(Request $request){

       if ($this->checkHeader($request->input('api_header'))){

           $user = $this->getUser($request->input('api_header'));
           $createdAt = $user->created_at;

           $gender = $request->input('gender');
           $elec_branch = $request->input('elec_branch');
           $graduate = $request->input('graduate');
           $dep = $request->input('dep');
           $gov = $request->input('gov');


           $messages = $this->getMessages($gender, $elec_branch, $graduate, $dep, $gov, $createdAt);

           if (count($messages) > 0){
               return response()->json([
                   'status' => true,
                   'message' => 'messages listed ..',
                   'messages' => $messages
               ]);
               return response()->json([
                   'status' => false,
                   'message' => 'no messages for this user ..'
               ]);
           }
       }else{ // if no api header
           return response()->json([
               'status' => false,
               'message' => 'user not allowed to see this data ..'
           ]);
       }

    }

    public function checkHeader($header){
        if ((count(Member::where('api_header', '=', $header)->get()) > 0) || (count(Employee::where('api_header', '=', $header)->get()) > 0) || (count(Student::where('api_header', '=', $header)->get()) > 0)){
            return true;
        }else{
            return false;
        }
    }

    public function getUser($header){

        if ($user = Member::where('api_header', '=', $header)->first()){
            return $user;
        }else if($user = Employee::where('api_header', '=', $header)->first()){
            return $user;
        }else if ($user = Student::where('api_header', '=', $header)->first()){
            return $user;
        }
    }

    public function getMessages($gender, $elec_branch, $graduate, $dep, $gov, $createdAt)
    {
        $messages = Message::where('option', '=', 1)->where('value', '=', $gender)
            ->orWhere('option', '=', 2)->where('value', '=', $elec_branch)
            ->orWhere('option', '=', 3)->where('value', '=', $graduate)
            ->orWhere('option', '=', 4)->where('value', '=', $dep)
            ->orWhere('option', '=', 5)->where('value', '=', $gov)
            ->orWhere('option', '=', 6)->get()
            ->where('created_at', '>', $createdAt);
        return $messages;
    }

}
