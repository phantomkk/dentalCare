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

Route::get('initAdmin', 'AdminController@initAdmin')->name('admin.login.initAdmin');
Route::get('initAdmin2', 'AdminController@initAdmin2')->name('admin.login.initAdmin2');
Route::get('initTreatmentCate', 'AdminController@initTreatmentCate')->name('admin.login.initTreatmentCate');
Route::get('initTreatment', 'AdminController@initTreatment')->name('admin.login.initTreatment');
Route::get('logoutAdmin', 'AdminController@logout')->name('admin.logout');
Route::post('loginAdmin', 'AdminController@login')->name('admin.login.post');
Route::get('lara-admin', 'AdminController@loginGet')->name('admin.login');


// webuser phuc
Route::get('/','HomeController@HomePage');
Route::get('/doctorList','HomeController@DoctorInformation');
// end webuser

Route::group(['middleware' => 'admins'], function () {
Route::get('dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::get('/getDB','HomeController@getDB');
Route::get('/banggia','HomeController@BangGiaDichVu');

//CRUD new
Route::get('/create-Newsx', 'Mobile\NewsController@loadcreateNews');
Route::post('/createNews', 'Mobile\NewsController@createNews');
Route::get('/list-News', 'Mobile\NewsController@loadListNews');
// Route::post('/createNews', 'HomeController@createNews');
//end CRUD new
