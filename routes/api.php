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

/////////////////home/////////////////
Route::get('/home','HomeApiController@home')->name('api.home');
Route::get('/dashboard','HomeApiController@dashboard')->name('api.dashboard');
Route::get('/data','HomeApiController@data')->name('api.data');
Route::get('/index','HomeApiController@index');
Route::post('/subscribe','HomeApiController@subscribe')->name('api.subscribe');
Route::post('/update_token','HomeApiController@update_token')->name('api.subscribe');
Route::get('/area','HomeApiController@area')->name('api.area');
Route::get('/area-search','HomeApiController@search_area')->name('api.area-search');
Route::post('/contactus','HomeApiController@contactus')->name('api.contactus');
Route::get('/page','HomeApiController@page')->name('api.page');
Route::get('/services','HomeApiController@services')->name('api.services');
Route::get('/service/details','HomeApiController@service_details')->name('api.services.details');
Route::get('/all-requests', 'PropertyApiController@all_requests');
Route::get('/all-invoices', 'PropertyApiController@all_invoices');
Route::post('/change-invoice-status', 'PropertyApiController@change_invoice_status');
Route::get('/all-dues', 'PropertyApiController@all_dues');
Route::get('/all-contracts', 'PropertyApiController@all_contracts');
Route::get('/request/renter', 'PropertyApiController@renter_requests');
Route::get('/dues/renter', 'PropertyApiController@renter_dues');
Route::get('/contracts/renter', 'PropertyApiController@renter_contracts');
Route::get('/contracts/create/renters', 'PropertyApiController@contract_property_requests');
Route::get('/request/user', 'ClientApiController@request_user');
Route::get('/maintenance', 'MaintenanceApiController@index');
Route::get('/maintenance/category', 'MaintenanceApiController@category');
Route::post('/maintenance/add', 'MaintenanceApiController@add');
Route::get('/maintenance/show', 'MaintenanceApiController@show');
Route::post('/maintenance/update', 'MaintenanceApiController@update');
Route::delete('/maintenance/delete', 'MaintenanceApiController@delete');
Route::get('/checkout', 'HomeApiController@checkout');
Route::post('/pay-start-session', 'HomeApiController@init_pay');
Route::post('/complete-pay', 'HomeApiController@complete_pay');
Route::get('/bank-branchs', 'HomeApiController@bank_branchs');
Route::get('/pdf', 'HomeApiController@pdf');
Route::get('/subscribes', 'HomeApiController@subscribes');
Route::post('/check-promocode', 'HomeApiController@check_promocode');



//////////////////////////property//////////////////////
Route::prefix('property')->group(function () {
    Route::get('/own/no-pagination', 'PropertyApiController@own_without_pagination');
    Route::post('/favorite', 'PropertyApiController@favorite');
    Route::get('/favorite', 'PropertyApiController@favorite_list');
    Route::delete('/delete/{id}', 'PropertyApiController@destroy');
    Route::post('/delete2', 'PropertyApiController@delete');
    Route::delete('/favorite', 'PropertyApiController@remove_form_favorite');
    Route::post('/request', 'PropertyApiController@request');
    Route::get('/mobile', 'PropertyApiController@mobile');
    Route::get('/list', 'PropertyApiController@index');
    Route::get('/show', 'PropertyApiController@show');
    Route::get('/my-property-details', 'PropertyApiController@my_property_details');
    Route::get('/my-property-requests', 'PropertyApiController@my_property_requests');
    Route::get('/my-property-invoices', 'PropertyApiController@my_property_invoices');
    Route::get('/my-property-dues', 'PropertyApiController@my_property_dues');
    Route::get('/my-property-contracts', 'PropertyApiController@my_property_contracts');
    Route::post('/contract/change-status', 'PropertyApiController@contract_change_status');
    Route::get('/own', 'PropertyApiController@own');
    Route::get('/rent', 'PropertyApiController@rent');
    Route::get('/requests', 'PropertyApiController@requests');
    Route::post('/request/change-status', 'PropertyApiController@request_change_status');
    Route::post('/request/renter/cancel', 'PropertyApiController@renter_cancel_request');
    Route::get('/ads', 'PropertyApiController@my_property_ads');
    Route::post('/add-to-ads', 'HomeApiController@add_property_to_ads');

    Route::get('/create', 'PropertyApiController@create');
    Route::post('/create', 'PropertyApiController@store');
    Route::post('/update', 'PropertyApiController@update');
    Route::get('/invoices/renter','PropertyApiController@renter_invoices');
    Route::get('/invoices/owner','PropertyApiController@owner_invoices');
    Route::post('/facilities/add','PropertyApiController@add_facilities');
    Route::post('/facilities/update','PropertyApiController@update_facilities');
    Route::post('/facilities/delete','PropertyApiController@delete_facilities');
    Route::post('/image/delete','PropertyApiController@delete_property_image');
    Route::post('/contract/add','PropertyApiController@add_contract');
    Route::get('/contract/templates','PropertyApiController@contract_data');
    Route::get('/contract/print','PropertyApiController@print_contract');
    Route::get('/contract/print/file','PropertyApiController@print_contract_file')->name('api.contract.print');
    Route::get('/invoice/print','PropertyApiController@print_invoice');
    Route::get('/invoice/print/file','PropertyApiController@print_invoice_file')->name('api.invoice.print');
});



/////////////////////client/////////////////////////
Route::prefix('client')->group(function () {
    Route::post('/login','ClientApiController@login');
    Route::post('/register','ClientApiController@register');
    Route::post('/resend_activation_code','ClientApiController@resend_activation_code');
    Route::post('/verify_account','ClientApiController@verify_account');
    Route::post('/change-password','ClientApiController@change_password');
    Route::post('/forgot-password','ClientApiController@forgot_password');
    Route::post('/reset-password','ClientApiController@reset_password');
    Route::post('/resend-forgot-code','ClientApiController@resend_forgot_code');
    Route::post('/login-board','ClientApiController@login_board');
    Route::get('/profile','ClientApiController@profile');
    Route::post('/update-profile','ClientApiController@update_profile');
    Route::get('/tickets','ClientApiController@tickets');
    Route::post('/tickets/add','ClientApiController@add_ticket');
    Route::post('/tickets/add/comment','ClientApiController@ticket_add_comment');
    Route::get('/ticket/details','ClientApiController@ticket_details');
    Route::post('/tickets/solve','ClientApiController@ticket_solve');
    Route::post('/users/add','ClientApiController@add_user');
    Route::post('/users/update','ClientApiController@update_user');
    Route::get('/users/list','ClientApiController@users_list');
    Route::delete('/users/delete','ClientApiController@delete_user');
    Route::get('/permissions','ClientApiController@get_permissions');
    Route::post('/send-notifications', 'ClientApiController@send_notification');
    Route::get('/notifications', 'ClientApiController@notifications');

});
















//////////////////////////////////////////

Route::get('/user', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post(env('APP_URL').'oauth/token', [
        'form_params' => [
            'grant_type' => 'password',
            'client_id' => '2',
            'client_secret' => 'brfIOS9vCUw2HuLOvLdyrjh9iA3gtxPV2xWVvw9w',
            'username' => '01014778866',
            'password' => '123123',
            'scope' => '',
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
});


Route::prefix('jjjj')->group(function () {
    Route::get('users', function () {
        // Matches The "/admin/users" URL
    });
});
