<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'apiRegister'])->name('apiRegister');
Route::post('/loginsocial', [App\Http\Controllers\Auth\LoginOrRegister::class, 'apiRegisterOrLogin'])->name('apiRegisterOrLogin');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'apiLogin'])->name('apiLogin');
Route::post('/updateprofile', [App\Http\Controllers\Auth\updateprofile::class, 'apiupdateprofile'])->name('apiupdateprofile');
Route::post('/getcurrentuser', [App\Http\Controllers\Auth\getcurrentuser::class, 'apigetcurrentuser'])->name('apigetcurrentuser');
Route::post('/forgotpassword', [App\Http\Controllers\Auth\forgotpassword::class, 'apiforgotpassword'])->name('apiforgotpassword');
Route::post('/getlogingsection', [App\Http\Controllers\Auth\getlogingsection::class, 'apigetloging'])->name('apigetloging');
Route::post('/checkchangepass', [App\Http\Controllers\Auth\checkchangepass::class, 'apichangepass'])->name('apichangepass');
Route::post('/changepass', [App\Http\Controllers\Auth\changepass::class, 'changepass'])->name('changepass');
Route::post('/logout', [App\Http\Controllers\Auth\logout::class, 'apilogout'])->name('apilogout');
Route::post('/ListFriend', [App\Http\Controllers\Profile\ListFriend::class, 'apilistFriend'])->name('apilistFriend');
Route::post('/SuggestFriend', [App\Http\Controllers\Profile\SuggestFriend::class, 'apiSuggestFriend'])->name('apiSuggestFriend');
Route::post('/RequestFriend', [App\Http\Controllers\Profile\RequestFriend::class, 'apiRequestFriend'])->name('apiRequestFriend');

Route::post('/getlistrequest', [App\Http\Controllers\Profile\getlistrequest::class, 'apigetlistrequest'])->name('apigetlistrequest');

Route::post('/acceptOrdelete', [App\Http\Controllers\Profile\acceptOrdelete::class, 'apiacceptOrdelete'])->name('apiacceptOrdelete');
Route::post('/postcontent', [App\Http\Controllers\Home\postcontent::class, 'apipostcontent'])->name('apipostcontent');
Route::post('/getlistpost', [App\Http\Controllers\Home\getlistpost::class, 'apigetlistpost'])->name('apigetlistpost');
Route::post('/getmypost', [App\Http\Controllers\Home\getmypost::class, 'apigetmypost'])->name('apigetmypost');
Route::post('/likecontent', [App\Http\Controllers\Home\likecontent::class, 'apilikecontent'])->name('apilikecontent');
Route::post('/commentcontent', [App\Http\Controllers\Home\commentcontent::class, 'apicommentcontent'])->name('apicommentcontent');
Route::post('/postcomment', [App\Http\Controllers\Home\postcomment::class, 'apipostcomment'])->name('apipostcomment');
Route::post('/getlistfriendtochat', [App\Http\Controllers\Home\getlistfriendtochat::class, 'apigetlistfriendtochat'])->name('apigetlistfriendtochat');
Route::post('/getlistmessage', [App\Http\Controllers\Home\getlistmessage::class, 'apigetlistmessage'])->name('apigetlistmessage');

Route::post('/sendmessage', [App\Http\Controllers\Home\sendmessage::class, 'apisendmessage'])->name('apisendmessage');
Route::post('/updateprofile2', [App\Http\Controllers\Auth\updateprofile2::class, 'apiupdateprofile2'])->name('apiupdateprofile2');

Route::post('/actionwithfriend', [App\Http\Controllers\Home\actionwithfriend::class, 'apiactionwithfriend'])->name('apiactionwithfriend');
