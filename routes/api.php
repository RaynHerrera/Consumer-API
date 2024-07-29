<?php

use App\Http\Controllers\OnboardingController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::post('register', 'Authentication\UserController@create')->name('user-register');
Route::post('email-verify', 'Authentication\UserController@checkActivation')->name('user-register-email-verification');
Route::post('profile-email-verify', 'Authentication\UserController@profileEmailVerify')->name('user-profile-email-verification');
Route::post('login', 'Authentication\UserController@Login')->name('user-login');
Route::post('forgot-password', 'Authentication\UserController@forGotPassword')->name('forgot-password');
Route::post('forgot-password-verify', 'Authentication\UserController@forGotPasswordVerify')->name('forgot-password');


//*for 2 factore 
Route::post('two-factor-verify-code', 'Authentication\UserController@authverify')->name('validate-2facode');
Route::post('send-2facode', 'Authentication\UserController@sendCode')->name('send-2facode');
Route::get('get-countries', 'Common\CountriesController@index')->name('get-countries');

/**New APi */

Route::post('user-check', 'Authentication\UserController@checkUser')->name('check-user');
Route::post('email-check', 'Authentication\UserController@checkEmail')->name('check-email');
Route::post('send-Verify-email', 'Authentication\UserController@sendVerifyEmail')->name('Send-verify-email');

/**End New Api */

Route::get('test', function () {
    return view('welcome');
});
Route::get('test-email', function () {
    try {
        return    Mail::to('abhishek.patel@theopeneyes.in')->send(new TestMail());;
    } catch (Exception $ex) {
        return  $ex;
    }
});
Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('count-company', 'Common\DashboardController@getCompanyCount')->name('company-count');
        Route::get('count-unique-data', 'Common\DashboardController@getUniqueCount')->name('count-unique-data');
    });


    // Route::group(['prefix' => 'user'], function () {
    Route::get('profile', 'Authentication\UserController@profile')->name('profile');
    Route::post('setup-onboarding', 'OnboardingController@setupOnboarding')->name('setup-onboarding');
    Route::post('update-onboarding', 'OnboardingController@updateOnboardingData')->name('update-onboarding');
    Route::get('setup-onboarding', 'OnboardingController@getOnboadingData')->name('setup-onboarding');

    Route::post('profile', 'Authentication\UserController@updateProfile')->name('update-profile');
    Route::post('two-factor-active-inactive', 'Authentication\UserController@updateTwoFactor')->name('two-factor-active-inactive');
    Route::post('update-password', 'Authentication\UserController@updatePassword')->name('update-password');
    // });

    // Route::group(['prefix' => 'company'], function () {
    Route::post('add-company', 'Common\CompanyController@add')->name('add-company');
    Route::post('detect-new-company', 'Common\CompanyController@detact')->name('detect-new-company');
    Route::post('set-company-logo', 'Common\CompanyController@setLogo')->name('set-company-logo');
    Route::post('list', 'Common\CompanyController@getCompanyList')->name('getCompany');
    Route::get('list-for-mobile', 'Common\CompanyController@getCompanyListForMobile')->name('getCompany-for-mobile');
    Route::post('company-dashboard', 'Common\CompanyController@getCompanyDashboard')->name('get-Company-dashboard');
    Route::post('company-dashboard-privacy-statement', 'Common\CompanyController@getCompanyDashboardPrivacyStatement')->name('get-Company-dashboard-privacy-statement');
    // });

    Route::post('user-dataelement-for-company', 'Common\UserDataElementController@userDataElementForCompany')->name('user-data-element-for-comapny');

    Route::post('user-dataelement', 'Common\UserDataElementController@userDataElement')->name('user-data-element');
    Route::post('user-dataelement-list', 'Common\UserDataElementController@userDataElementList')->name('user-data-element-list');
    Route::post('user-Company-dataelement', 'Common\UserDataElementController@userCompanyDataElement')->name('user-company-data-element-list');

    //**new api */
    Route::post('privacy-statement-data-list', 'Common\PrivacyStatementController@index')->name('privacy-statement-data-list');
    Route::post('privacy-statement-company-data-list', 'Common\PrivacyStatementController@index')->name('privacy-statement-company-data-list');

    Route::get('your-action-company/{id}', 'Common\YourActionController@getCompany')->name('your-action-company');
    Route::post('add-your-actions', 'Common\YourActionController@Add')->name('add-your-actions');
    Route::post('your-company-actions', 'Common\YourActionController@index')->name('your-company-actions');
    Route::post('your-company-actions-update', 'Common\YourActionController@update')->name('your-company-actions-update');
    //** end API  */
    Route::get('get-formdata', 'Common\PIDataElementController@getFormData')->name('get-from-data');
    Route::post('add-new-element', 'Common\PIDataElementController@addElement')->name('add-new-element');
    Route::post('form-track', 'Common\PIDataElementController@formTrack')->name('form-track');

    Route::get('settings', 'Common\PIDataElementController@getSetting')->name('get-setting');
    Route::post('settings', 'Common\PIDataElementController@updateSetting')->name('update-setting');

    Route::get('get-pielementlist', 'Common\PIDataElementController@getPiElement')->name('get-pi-element');
    Route::get('get-pinamelist', 'Common\PIDataElementController@getPiName')->name('get-pi-name');
    Route::get('elements', 'Common\PIDataElementController@getElement')->name('get-element');
    Route::get('formdata/{id}', 'Common\CompanyController@getFromData')->name('get-from-company');

    Route::post('privacy', 'Common\CompanyController@getPrivacyPolicy')->name('get-company-privacy-policy');
    Route::get('logout', 'Authentication\UserController@logout')->name('user-logout');


    Route::get('excel-upload-list', 'Import\ExcelImportController@excelUploadlist')->name('excel-upload-list');

    // endpoint to get all the data elements and companies for the user (Onboarding Flow)

});
Route::post('excel-upload', 'Import\ExcelImportController@excelUpload')->name('excel-upload');
Route::get('excel-import', 'Import\ExcelImportController@excelImport')->name('excel-import');

Route::get('save-onboarding-data', [OnboardingController::class, 'updateUserOnboardingData'])->name('save-onboarding-data');
