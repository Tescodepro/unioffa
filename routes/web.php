<?php

use App\Http\Controllers\Application\ApplicationController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\staff\GeneralController as AdminGeneralController;
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\website\GeneralController;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the
RouteServiceProvider within a group which contains the "web" middleware group. Now create something great!
|

*/
Route::controller(GeneralController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/contact', 'contact')->name('contact');
});

// ====== Application Routes ======= //
Route::prefix('admission')->group(function () {
    Route::controller(ApplicationController::class)->group(function () {
        Route::get('/', 'login')->name('application.login');
        Route::post('/', 'loginAction');

        Route::get('/register', 'index')->name('application.register');
        Route::post('/register', 'createAccount');

        Route::get('logout', 'logoutAction')->name('application.logout');

        Route::get('/dashboard', 'dashboard')->middleware('user.type:applicant')->name('application.dashboard');
        Route::post('/start-application', 'startApplication')->middleware('user.type:applicant')->name('application.start');

        Route::get('/form/{user_application_id}', 'applicationForm')->middleware('user.type:applicant')->name('application.form');

        // ======= form submition ======= //
        Route::post('/form/save-profile/{user_application_id}', 'saveProfile')->middleware('user.type:applicant')->name('application.personal_info.submit');
        Route::post('/form/save-olevel/{user_application_id}', 'saveOlevel')->middleware('user.type:applicant')->name('application.olevel.submit');
        Route::post('/form/save-alevel/{user_application_id}', 'saveAlevel')->middleware('user.type:applicant')->name('application.alevel.submit');
        Route::post('/form/save-jamb-details/{user_application_id}', 'saveJambDetails')->middleware('user.type:applicant')->name('application.jamb_details.submit');
        Route::post('/form/save-course-of-study/{user_application_id}', 'saveCourseOfStudy')->middleware('user.type:applicant')->name('application.course_of_study.submit');
        Route::post('/form/save-documents/{user_application_id}', 'saveDocuments')->middleware('user.type:applicant')->name('application.documents.submit');
        Route::post('/form/handle-form-submission/{user_application_id}', 'handleFormSubmission')->middleware('user.type:applicant')->name('application.handle_form_submission');
        Route::get('/admission-letter/{applicationId}', 'downloadAdmissionLetter')->name('student.admission.letter')->middleware('auth');
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('application.forgot.password')->middleware('guest');
        Route::post('/forgot-password', 'postForgotPassword')->name('password.email')->middleware('guest');
        Route::get('/password/update-otp', 'showUpdateWithOtp')->name('password.otp.update')->middleware('guest');
        Route::post('/password/update-otp', 'updateWithOtp')->middleware('guest');

    });
});

// ====== Payment Routes ======= //
Route::prefix('payments')->group(function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('initiate', 'initiatePayment')->name('application.payment.process');
        Route::get('callback', 'handleCallback')->name('payment.callback');
        Route::get('payment-status-page', 'paymentStatusPage')->name('payment.status.page');
    });
});

Route::prefix('students')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/', 'login')->name('student.login');
        Route::post('/', 'loginAction');
    });

    Route::middleware('user.type:student')->group(function () {
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('students.dashboard');
            Route::get('/payment', 'loadPayment')->name('students.load_payment');
            Route::get('/payment-history', 'paymentHistory')->name('students.payment.history');
            Route::get('logout', 'logoutAction')->name('students.logout');
            Route::get('/profile', 'profile')->name('students.profile');
            Route::post('/profile', 'updateProfile')->name('students.profile.update');
            Route::post('/change-password', 'changePassword')->name('students.change.password');
            Route::get('/admission-letter', 'downloadAdmissionLetter')->name('students.admission.letter');
        });

        Route::controller(CourseRegistrationController::class)->group(function () {
            Route::get('/course-registration', 'index')->name('students.course.registration');
            Route::post('course-registration', 'store');
            Route::get('course-registration/download', 'downloadCourseForm')->name('students.course.download');
        });


    });
});

Route::prefix('staff')->group(function () {
    Route::controller(StaffAuthController::class)->group(function () {
        Route::get('/', 'login')->name('staff.login');
        Route::post('/', 'loginAction');
    });

    Route::middleware('user.type:administrator')->group(function () {
        Route::controller(AdminGeneralController::class)->group(function () {
            Route::get('/dashboard', 'index_admin')->name('admin.dashboard');
            Route::post('admit/{userId}', 'admitStudent')->name('admin.admit');
            Route::get('/export-applicants', 'exportApplicants')->name('admin.exportApplicants');
            Route::get('/applicants/{user}/{application}', 'showApplicantDetails')->name('admin.applicants.details');

        });
    });
});
