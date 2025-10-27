<?php

use App\Http\Controllers\Application\ApplicationController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\Staff\GeneralController as AdminGeneralController;
use App\Http\Controllers\Staff\{BursaryController, PaymentSettingController};
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\website\GeneralController;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\Route;
use App\Services\HostelAssignmentService;

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
    Route::get('/agent-application', 'agentApplication')->name('agent.application');
    Route::post('/submit-agent-application', 'submitAgentApplication')->name('agent.application.submit');
    Route::get('/lgas/{state_id}', 'getLgas')->name('lgas.by.state');
    Route::get('/scholarship-application', 'scholarshipApplication')->name('scholarship.application');

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
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('application.forgot.password');
        Route::post('/forgot-password', 'postForgotPassword')->name('password.email');
        Route::get('/password/update-otp', 'showUpdateWithOtp')->name('password.otp.update');
        Route::post('/password/update-otp', 'updateWithOtp');
        Route::get('/application/{id}/download',  'downloadApplicantDetails')->name('applicant.printout.download')->middleware('auth');
    });
});

// ====== Payment Routes ======= //
Route::prefix('payments')->group(function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('initiate', 'initiatePayment')->name('application.payment.process');
        Route::get('callback', 'handleCallback')->name('payment.callback');
        Route::get('payment-status-page', 'paymentStatusPage')->name('payment.status.page');
        Route::get('/verify-receipt/{ref}', 'verifyReceipt')->name('verify.receipt');
        Route::get('/receipt/{reference}', 'downloadReceipt')->name('view.receipt');
    });
});

Route::prefix('students')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/', 'login')->name('student.login');
        Route::post('/', 'loginAction');

        Route::get('/forget-password', 'forgetPasswordIndex')->name('students.auth.forget-password');
        Route::post('/forget-password', 'forgetPasswordAction');
        Route::get('/auth/change-password', 'verifyOtpIndex')->name('students.auth.change-password');
        Route::post('/auth/change-password', 'verifyOtpAction');
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
            Route::get('/hostel',  'hostelIndex')->name('students.hostel.index');
            Route::post('/hostel',  'hostelApply');
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
        Route::get('logout', 'logoutAction')->name('staff.logout');
    });

    Route::middleware('user.type:administrator')->group(function () {
        Route::controller(AdminGeneralController::class)->group(function () {
            Route::get('/dashboard', 'index_admin')->name('admin.dashboard');
            Route::post('admit/{userId}', 'admitStudent')->name('admin.admit');
            Route::post('recommend/{userId}', 'recommendStudent')->name('admin.recommend');
            Route::get('/export-applicants', 'exportApplicants')->name('admin.exportApplicants');
            Route::get('/applicants/{user}/{application}', 'showApplicantDetails')->name('admin.applicants.details');
            Route::get('/agent-applicants', 'showAgentDetail')->name('admin.agent.applicants');
            Route::post('/agent-applicants/update-status', 'changeAgentStatus')->name('admin.agent.application.update_status');
        });
    });

    Route::middleware('user.type:bursary')->group(function () {
        Route::prefix('burser')->group(function () {
            Route::controller(BursaryController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('burser.dashboard');
                Route::get('/transactions', 'transactions')->name('bursary.transactions');
                Route::get('/transactions/export/{format}',  'exportTransactions')->name('bursary.transactions.export');
                Route::get('/transactions/verify/{id}',  'verifySingle')->name('bursary.transactions.verify');
                Route::get('/verify-payment', 'verifyPaymentForm')->name('bursary.verify.form');
                Route::post('/verify-payment', 'verifyPaymentAction')->name('bursary.verify.action');
                Route::get('/transactions/{id}/verify', 'verifySingle')->name('bursary.transactions.verify');

                Route::get('/transactions/create',  'createManual')->name('bursary.transactions.create');
                Route::post('/transactions/store',  'storeManual')->name('bursary.transactions.store');
                Route::put('/transactions/update/{transaction}',  'updateManual')->name('bursary.transactions.update');
                Route::delete('/transactions/destroy/{transaction}',  'destroyManual')->name('bursary.transactions.destroy');

                Route::get('/reports/faculty', 'reportByFaculty')->name('bursary.reports.faculty');
                Route::get('/reports/department', 'reportByDepartment')->name('bursary.reports.department');
                Route::get('/reports/level', 'reportByLevel')->name('bursary.reports.level');
                Route::get('/reports/student', 'reportByStudent')->name('bursary.reports.student');


                // Exports
                Route::get('/reports/{type}/export/{format}', 'export')->name('bursary.reports.export');
            });
            Route::controller(PaymentSettingController::class)->group(function () {
                Route::get('/payment-settings', 'index')->name('bursary.payment-settings.index');
                Route::get('/payment-settings/create', 'create')->name('bursary.payment-settings.create');
                Route::post('/payment-settings', 'store')->name('bursary.payment-settings.store');
                Route::get('/payment-settings/{paymentSetting}/edit', 'edit')->name('bursary.payment-settings.edit');
                Route::put('/payment-settings/{paymentSetting}', 'update')->name('bursary.payment-settings.update');
                Route::delete('/payment-settings/{paymentSetting}', 'destroy')->name('bursary.payment-settings.destroy');
            });
        });
    });

    Route::prefix('ict')->middleware('user.type:ict')->group(function () {
        Route::controller(App\Http\Controllers\Staff\Ict\IctStudentController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('ict.dashboard');
            // Student CRUD
            Route::get('/students', 'index')->name('ict.students.index');
            Route::get('/students/create', 'create')->name('ict.students.create');
            Route::post('/students', 'store')->name('ict.students.store');
            Route::get('/students/{student}/edit', 'edit')->name('ict.students.edit');
            Route::put('/students/{student}', 'update')->name('ict.students.update');
            Route::delete('/students/{student}', 'destroy')->name('ict.students.destroy');
            // Bulk upload
            Route::get('/students/bulk', 'bulkUploadForm')->name('ict.students.bulk');
            Route::post('/students/bulk', 'bulkUpload')->name('ict.students.bulk.upload');
            Route::get('/students/bulk/template', 'downloadTemplate')->name('ict.students.bulk.template');
        });
    });
});
