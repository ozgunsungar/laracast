<?php
use \app\Core\Route;

Route::get("/",function(){

});

Route::get("/users",function(){

});
//
Route::get("/register","RegisterController@registerShowForm");//şu fonksiyonu aç ve çalıştır.
//browsera geldiğini anlıyoruz çünkü web.php içinde bu işlemi yapıyoruz.
//Route::get("/register","RegisterController@register");//şu fonksiyonu aç ve çalıştır.
//Route::post("/register","RegisterController@register");//şu fonksiyonu aç ve çalıştır.
Route::post("/form","RegisterController@register");//şu fonksiyonu aç ve çalıştır.
Route::put("/register","RegisterController@register");//şu fonksiyonu aç ve çalıştır.

Route::dispatch();