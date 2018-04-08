<?php

namespace App\Http\Controllers;

use App\Facades\Helpers;
use App\Image;
use App\Member;
use Illuminate\Http\Request;

class membersController extends Controller
{
    public function getMember($id){
        $member = Member::find($id);
        if (count($member) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم تحميل بيانات العضو ..',
                'data' => $member
            ], 201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'هذا العضو غير مسجل لدينا ..'
            ],200);
        }
    }

    public function getMembers(){
        $members = Member::all();
        if (count($members) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم تحميل بيانات الاعضاء',
                'data' => $members
            ], 201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد اعضاء مسجلين حتى الان'
            ],200);
        }
    }

    public function postMembers(Request $request){

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

        $member = new Member([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'=> bcrypt($request->input('password')),
            'reg_token' => $request->input('reg_token'),
            'api_header' =>  'mem'.str_random(40),
            'fcode' => $request->input('fcode'),
            'dep' => $request->input('dep'),
            'job' => $request->input('job'),
            'nat_id' => $request->input('nat_id'),
            'mobile' => $request->input('mobile'),
            'gender' => $request->input('gender'),
            'gov' => $request->input('gov'),
            'branch' => $request->input('branch'),
            'elec_branch' => $request->input('elec_branch'),
            'graduate' => Helpers::dateParser($request->input('graduate')),
            'pic' => $imageName
        ]);

        if ($member->save()){
            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل العضو بنجاح..',
                'member' => $member
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
}
