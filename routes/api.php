<?php

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TimelineController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'messages'], static function () {
    Route::get('', [MessageController::class, 'index']);
    Route::post('', [MessageController::class, 'store']);
});

Route::group(['prefix' => 'avatars'], static function () {
    Route::get('', [AvatarController::class, 'index']);
});

Route::group(['prefix' => 'timelines'], static function () {
    Route::get('', [TimelineController::class, 'index']);
    Route::post('', [TimelineController::class, 'store']);
});
