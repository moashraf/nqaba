<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/upload-image','imagesController@uploadImage');

Route::post('/upload-images-n','imagesController@uploadImageAndroid');


// ================   check if data existed before sign up  ================

Route::post('/check-member','checkController@checkMember');

Route::post('/check-employee','checkController@checkEmployee');


// ================   Sign in routes   ================


Route::post('/signin','signinController@signin');


// ================   employees routes   ================

//Route::get('/employees',[
//    'uses' => "employeesController@getEmployees"
//]);


Route::get('/employee/{id}','employeesController@getEmployee');

Route::post('/employees','employeesController@postEmployees');

//  ================   members routes   ================

//Route::get('/members',[
//    'uses' => "membersController@getMembers"
//]);

Route::get('/member/{id}','membersController@getMember');

Route::post('/members','membersController@postMembers');


// ================  students routes  ================

//Route::get('/students',[
//    'uses' => "studentsController@getStudents"
//]);

Route::get('/student/{id}','studentsController@getStudent');

Route::post('/students','studentsController@postStudents');

// ================    User Update routes  ================

Route::post('/update-user','updateController@index');

// ================    news routes  ================

Route::post('/articles','articlesController@getArticlesCheck');

Route::get('/article/{id}','articlesController@getArticle');

Route::post('/article','articlesController@postArticles');


//==============  Resorts Routes    =====================


Route::post('/resorts','resortController@getResorts');

Route::post('/resorts-cities','resortController@getResortsCities');

Route::post('/resorts-schedule','resortController@getCityResTimes');

Route::post('/week-resorts','resortController@getWeekResorts');


//=================== Medical Guide Routes  ===============


Route::post('/medical','medicalGuideController@getMedical');


//================   Spend date Routes  ====================

Route::post('/spend-date','spendDateController@getSpendDate');


//================   Branches date Routes  ====================

Route::post('/branches','branchesController@getBranches');


//================   Training Routes  ====================

Route::post('/training','trainingController@getCourses');

//================   Trips Routes  ====================

Route::post('/trips-cities','tripsController@getTripsCities');

Route::post('/trips-schedule','tripsController@getCityTripsTimes');

Route::post('/week-trips','tripsController@getWeekTrips');





//================   Payment Routes  ====================

Route::post('/payment','paymentController@getPayment');

Route::post('/existed-payments', 'paymentController@getExistedPayments');

Route::post('/create-payment','paymentController@createPayment');

Route::post('/old-payments','paymentController@showOldPayments');

Route::post('/member-payments','paymentController@memberPayments');

//Route::get('/handle-payment','paymentController@handlePayment');
//================  New Members Payment Routes  ====================

Route::get('/new-payments','newMemPayments@getNewMemPayments');

Route::post('/new-payments','newMemPayments@getNewMemPayment');

//================  Messages Routes  ====================

Route::post('/create-message','MessagesController@createMessages');
Route::post('/read-messages','MessagesController@readMessages');
Route::post('/message','MessagesController@read_member_dmessages');

//================  Password Reset Routes  ====================

Route::post('/password-reset','passwordResetController@passwordReset');

//  ================    Visitor routes   ================
 
Route::post('/registerVisitor','Visitor@register');
Route::post('/loginVisitor','Visitor@login');
Route::post('/forgetViistor','Visitor@forget');


