<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/store',[UserController::class,'store']);
Route::post('/transfer',[TransactionController::class,'transfer']);
Route::get('/teste-email',function () {
    Mail::to('vitorgguimaraes56@gmail.com')->send(new \App\Mail\WelcomeEmail());
    return 'Email enviado';
});
