<?php

Route::group([
    'middleware' => ['web'],
    'version' => 'v1',
    'prefix' => Config::get('cms.apiUri')
], function () {
    //API for User
    Route::prefix('user')->group(function () {
        Route::post('login', 'Mtech\Api\Controllers\User@login');
        Route::post('forgot-password', 'Mtech\Api\Controllers\User@forgotPassWord');
        Route::post('relogin', 'Mtech\Api\Controllers\User@reLogin');
        Route::post('logout', 'Mtech\Api\Controllers\User@logout');
    });
    Route::middleware('Mtech\API\Middleware\JwtMiddleware')->prefix('user')->group(function () {
        Route::post('/logout', 'Mtech\Api\Controllers\User@logout');               
    });
});
