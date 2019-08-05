<?php

//use Illuminate\Http\Request;

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
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//会员注册
Route::post('/register', 'Auth\RegisterController@register')->name('register');
//Route::post('/register', 'Auth\RegisterController@forbid')->name('register');

//会员登录
Route::post('/login', 'Auth\LoginController@login')->name('login');

//退出登录
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

//图片上传
Route::post('/photos', 'PhotoController@create')->name('photo.create');

//图片列表
Route::get('/photos', 'PhotoController@index')->name('photo.index');

//图片详情
Route::get('/photos/{id}', 'PhotoController@show')->name('photo.show');

//添加评论
Route::post('/photos/{photo}/comments', 'PhotoController@addComment')->name('photo.comment');

//添加收藏
Route::put('/photos/{id}/like', 'PhotoController@like')->name('photo.like');

//取消收藏
Route::delete('/photos/{id}/like', 'PhotoController@unlike');

//删除图片
Route::delete('/photos/{id}/delete', 'PhotoController@delete');

//获取用户数据
Route::get('/user', function () {
    return Auth::user();
})->name('user');

//刷新token
Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return response()->json();
});
