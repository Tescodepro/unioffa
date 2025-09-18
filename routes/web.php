<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\website\GeneralController;
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Application\ApplicationController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Student\DashboardController;
use App\Models\Course;
use Faker\Provider\ar_EG\Payment;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the          
RouteServiceProvider within a group which contains the "web" middleware group. Now create something great!
|           

*/  
Route::controller(GeneralController::class)->group(function(){
    Route::get('/', 'home')->name('home');
    Route::get('/contact', 'contact')->name('contact');
});


// ====== Application Routes ======= //
Route::prefix('admission')->group(function(){
	Route::controller(ApplicationController::class)->group(function(){
		Route::get('/', 'login')->name('application.login');
        Route::post('/', 'loginAction');
        
        Route::get('/register', 'index')->name('application.register');
        Route::post('/register', 'createAccount');

        Route::get('/dashboard', 'dashboard')->middleware('user.type:applicant')->name('application.dashboard');
        Route::post('/start-application', 'startApplication')->middleware('user.type:applicant')->name('application.start');

        Route::get('/form/{user_application_id}', 'applicationForm')->middleware('user.type:applicant') ->name('application.form');

        // ======= form submition ======= //
        Route::post('/form/save-profile/{user_application_id}', 'saveProfile')->middleware('user.type:applicant')->name('application.personal_info.submit');
        Route::post('/form/save-olevel/{user_application_id}', 'saveOlevel')->middleware('user.type:applicant')->name('application.olevel.submit');
        Route::post('/form/save-alevel/{user_application_id}', 'saveAlevel')->middleware('user.type:applicant')->name('application.alevel.submit');
        Route::post('/form/save-course-of-study/{user_application_id}', 'saveCourseOfStudy')->middleware('user.type:applicant')->name('application.course_of_study.submit');
        Route::post('/form/save-documents/{user_application_id}', 'saveDocuments')->middleware('user.type:applicant')->name('application.documents.submit'); 
        Route::post('/form/handle-form-submission/{user_application_id}', 'handleFormSubmission')->middleware('user.type:applicant')->name('application.handle_form_submission');


	});
});

// ====== Payment Routes ======= //
Route::prefix('payments')->group(function(){
    Route::controller(PaymentController::class)->group(function(){
        Route::post('initiate','initiatePayment')->name('application.payment.process');
        Route::get('callback','handleCallback')->name('payment.callback');
        Route::get('payment-status-page','paymentStatusPage')->name('payment.status.page');
    });
});

Route::prefix('students')->group(function(){
    Route::controller(AuthController::class)->group(function(){
        Route::get('/', 'login')->name('student.login');
        Route::post('/','loginAction');
    });

    Route::middleware('user.type:student')->group(function(){
        Route::controller(DashboardController::class)->group(function(){
            Route::get('/dashboard', 'index')->name('students.dashboard');
            Route::get('/payment', 'loadPayment')->name('students.load_payment');
        });

        Route::controller(CourseRegistrationController::class)->group(function(){
            Route::get('/course-registration', 'index')->name('students.course.registration');
            Route::post('course-registration','store');
            Route::get('course-registration/download','downloadCourseForm')->name('students.course.download');
        });


    });
});

