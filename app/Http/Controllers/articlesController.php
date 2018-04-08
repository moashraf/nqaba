<?php

namespace App\Http\Controllers;

use App\Article;
use App\Employee;
use App\Image;
use App\Member;
use App\Student;
use Illuminate\Http\Request;

class articlesController extends Controller
{
    public function index(){

    }

    public function getArticlesCheck(Request $request){

        $header = $request->input('api_header');



        if (count(Member::where('api_header', '=', $header)->get()) > 0){

            return $this->getArticles();
        }
        else if(count(Employee::where('api_header', '=', $header)->get()) > 0){

            return $this->getArticles();
        }
        else if(count(Student::where('api_header', '=', $header)->get()) > 0){

            return $this->getArticles();
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'هذا العضو لا يمكنه مشاهده البيانات'
            ]);
        }
    }

    public function getArticles(){
        $articles = Article::all();
        if (count($articles) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم ارسال الاخبار',
                'articles' => $articles
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد اخبار متاحه'
            ],200);
        }
    }






    public function getArticle($id){
        $article = Article::find($id);
        if (count($article) > 0){
            return response()->json([
                'status' => true,
                'message' => 'تم ارسال الاخبار .. ',
                'data' => $article
            ], 201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد اخبار متاحه'
            ],200);
        }
    }




    public function postArticles(Request $request){


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
                    'message' => 'خطأ اثناء رفع الصوره ..'
                ],200);
            }
        }

        $articles = new Article([
            'title' => $request->input('title'),
            'desc' => $request->input('desc'),
            'author' => $request->input('author'),
            'image' => $imageName

        ]);

        if ($articles->save()){
            return response()->json([
                'status' => true,
                'message' => 'تم تحرير الخبر..'
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'خطأ اثناء تحرير الخبر ..'
            ],200);
        }
    }
}
