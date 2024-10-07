<?php
use Illuminate\Support\Facades\Route;
    Route::group(['prefix' => 'account','namespace' => 'User','middleware' => 'check_user_login'], function() {
        Route::get('','UserDashboardController@dashboard')->name('get.user.dashboard');

        Route::get('update-info','UserInfoController@updateInfo')->name('get.user.update_info');
        Route::post('update-info','UserInfoController@saveUpdateInfo');

        Route::get('transaction','UserTransactionController@index')->name('get.user.transaction');
        Route::get('transaction/cancel/{id}','UserTransactionController@cancelTransaction')->name('get.user.transaction.cancel');
        Route::get('order/view/{id}','UserTransactionController@viewOrder')->name('get.user.order');

        Route::get('rating','UserRatingController@index')->name('get.user.rating');
        Route::get('rating/delete/{id}','UserRatingController@delete')->name('get.user.rating.delete');

        Route::get('comment','UserCommentController@index')->name('get.user.comment');
        Route::get('comment/delete/{id}','UserCommentController@delete')->name('get.user.comment.delete');


        Route::get('log-login','LogLoginUserController@index')->name('get.user.log_login');

        Route::get('tracking/view/{id}','UserTransactionController@getTrackingTransaction')->name('get.user.tracking_order');
        Route::get('favourite','UserFavouriteController@index')->name('get.user.favourite');
        Route::get('favourite-delete/{id}','UserFavouriteController@delete')->name('get.user.favourite.delete');

        Route::get('management-transaction','UserManagementTransaction@index')->name('get.user.management_transaction');

        Route::post('ajax-favourite/{idProduct}','UserFavouriteController@addFavourite')->name('ajax_get.user.add_favourite');
        Route::post('ajax-rating','UserRatingController@addRatingProduct')->name('ajax_post.user.rating.add');
        Route::post('captcha', 'CaptchaController@authCaptchaResume')->name('ajax_post.captcha.resume');
        Route::get('ajax-invoice-transaction/{id}','UserTransactionController@exportInvoiceTransaction')
			->name('ajax_get.user.invoice_transaction');
    });
