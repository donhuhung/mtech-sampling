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
        Route::post('/change-password', 'Mtech\Api\Controllers\User@changePassword');               
        Route::post('/checkin', 'Mtech\Api\Controllers\User@userCheckin');               
        Route::post('/checkout', 'Mtech\Api\Controllers\User@userCheckout');               
    });
    
    Route::middleware('Mtech\API\Middleware\JwtMiddleware')->prefix('config')->group(function () {
        Route::post('/app', 'Mtech\Api\Controllers\Setting@configApp')->name('config.ConfigApp');               
    });
    
    Route::middleware('Mtech\API\Middleware\JwtMiddleware')->prefix('general')->group(function () {
        Route::get('/get-product-sampling', 'Mtech\Api\Controllers\Setting@getProductSampling')->name('general.getProductSampling');           
        Route::get('/get-list-location', 'Mtech\Api\Controllers\Setting@getLocations')->name('general.getListLocation');           
        Route::post('/get-list-project', 'Mtech\Api\Controllers\Setting@getProjects')->name('general.getProjects');           
    });
    
    Route::middleware('Mtech\API\Middleware\JwtMiddleware')->prefix('customer')->group(function () {
        Route::post('/store', 'Mtech\Api\Controllers\Customer@storeCustomer');
        Route::post('/check-phone', 'Mtech\Api\Controllers\Customer@checkPhone');
        Route::post('/update-bill', 'Mtech\Api\Controllers\Customer@updateBill');
        Route::post('/update-avatar', 'Mtech\Api\Controllers\Customer@updateAvatar');
        Route::post('/get-otp', 'Mtech\Api\Controllers\Customer@getOTP');
    });
    
    Route::middleware('Mtech\API\Middleware\JwtMiddleware')->prefix('gift')->group(function () {
        Route::post('/list', 'Mtech\Api\Controllers\Gift@getListGift');
        Route::post('/catch-gift', 'Mtech\Api\Controllers\Gift@catchGift');
    });
    
});
