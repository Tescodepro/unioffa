<?php

use App\Http\Controllers\Application\ApplicationController;
use App\Http\Controllers\Application\AdmittedStudentsDownloadController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\Staff\BursaryController;
use App\Http\Controllers\Staff\GeneralController as AdminGeneralController;
use App\Http\Controllers\Staff\Ict\IctStudentController;
use App\Http\Controllers\Staff\Lecturer\CourseController;
use App\Http\Controllers\Staff\Lecturer\LecturerGeneralController;
use App\Http\Controllers\Staff\Lecturer\ResultController;
use App\Http\Controllers\Staff\PaymentSettingController;
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
| RouteServiceProvider within a group which contains the "web" middleware group. Now create something great!
|
*/

// Public News Route (no auth required)
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

Route::controller(GeneralController::class)->group(function () {
    // General Pages
    Route::get('/', 'home')->name('home');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/news', 'blog')->name('blog');

    // Applications
    Route::get('/agent-application', 'agentApplication')->name('agent.application');
    Route::post('/submit-agent-application', 'submitAgentApplication')->name('agent.application.submit');
    Route::get('/lgas/{state_id}', 'getLgas')->name('lgas.by.state');
    Route::get('/scholarship-application', 'scholarshipApplication')->name('scholarship.application');

    // Principal Officers
    Route::get('pro-chancellor', 'prochancellor')->name('team.prochancellor');
    Route::get('registrar', 'registrar')->name('team.registrar');

    // Council Members
    Route::get('council/suraj-oyewale', 'council_suraj')->name('team.council.suraj');
    Route::get('council/opeyemi-abdulateef', 'council_abdulateef')->name('team.council.abdulateef');
    Route::get('council/abdulrasheed-oyewale', 'council_abdulrasheed')->name('team.council.abdulrasheed');
    Route::get('council/akeem-oyewale', 'council_akeem')->name('team.council.akeem');
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
        Route::delete('/application/{user_application_id}', 'deleteApplication')->middleware('user.type:applicant')->name('application.delete');
        Route::get('/admission-letter/{applicationId}', 'downloadAdmissionLetter')->name('student.admission.letter')->middleware('auth');
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('application.forgot.password');
        Route::post('/forgot-password', 'postForgotPassword')->name('password.email');
        Route::get('/password/update-otp', 'showUpdateWithOtp')->name('password.otp.update');
        Route::post('/password/update-otp', 'updateWithOtp');
        Route::get('/application/{id}/download', 'downloadApplicantDetails')->name('applicant.printout.download')->middleware('auth');
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
            Route::get('/hostel', 'hostelIndex')->name('students.hostel.index');
            Route::post('/hostel', 'hostelApply');
        });

        Route::controller(CourseRegistrationController::class)->group(function () {
            Route::get('/course-registration', 'index')->name('students.course.registration');
            Route::post('course-registration', 'store');
            Route::delete('course-registration/{id}', 'removeCourse')->name('students.course.remove');
            Route::get('course-registration/download', 'downloadCourseForm')->name('students.course.download');
        });

        // Student Results & Transcript
        Route::controller(DashboardController::class)->group(function () {
            Route::get('/results', 'viewResults')->name('students.results');
            Route::get('/transcript', 'viewTranscript')->name('students.transcript');
            Route::get('/transcript/download', 'downloadTranscript')->name('students.transcript.download');
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

        // Admitted Students Download Routes
        Route::controller(AdmittedStudentsDownloadController::class)->group(function () {
            Route::get('/admitted-students', 'index')->name('admitted-students.index');
            Route::post('/admitted-students/download', 'download')->name('admitted-students.download');
        });
    });

    // VC, ICT, and Registrar access to admitted students download
    Route::middleware('user.type:vice-chancellor')->group(function () {
        Route::controller(AdmittedStudentsDownloadController::class)->group(function () {
            Route::get('/admitted-students', 'index')->name('admitted-students.index');
            Route::post('/admitted-students/download', 'download')->name('admitted-students.download');
        });
    });

    Route::middleware('user.type:ict')->group(function () {
        Route::controller(AdmittedStudentsDownloadController::class)->group(function () {
            Route::get('/admitted-students', 'index')->name('admitted-students.index');
            Route::post('/admitted-students/download', 'download')->name('admitted-students.download');
        });
    });

    Route::middleware('user.type:registrar')->group(function () {
        Route::controller(AdmittedStudentsDownloadController::class)->group(function () {
            Route::get('/admitted-students', 'index')->name('admitted-students.index');
            Route::post('/admitted-students/download', 'download')->name('admitted-students.download');
        });
    });

    Route::middleware('user.type:bursary')->group(function () {
        Route::prefix('burser')->group(function () {
            Route::controller(BursaryController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('burser.dashboard');
                Route::get('/transactions', 'transactions')->name('bursary.transactions');
                Route::get('/transactions/export/{format}', 'exportTransactions')->name('bursary.transactions.export');
                Route::get('/transactions/verify/{id}', 'verifySingle')->name('bursary.transactions.verify');
                Route::get('/verify-payment', 'verifyPaymentForm')->name('bursary.verify.form');
                Route::post('/verify-payment', 'verifyPaymentAction')->name('bursary.verify.action');
                Route::get('/transactions/{id}/verify', 'verifySingle')->name('bursary.transactions.verify');

                Route::get('/transactions/create', 'createManual')->name('bursary.transactions.create');
                Route::post('/transactions/store', 'storeManual')->name('bursary.transactions.store');
                Route::put('/transactions/update/{transaction}', 'updateManual')->name('bursary.transactions.update');
                Route::delete('/transactions/destroy/{transaction}', 'destroyManual')->name('bursary.transactions.destroy');

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

    Route::middleware('user.type:dean')->group(function () {
        Route::prefix('dean')->group(function () {
            // Dean general dashboard
            Route::controller(LecturerGeneralController::class)->group(function () {
                Route::get('/dashboard', 'dean_dashboard')->name('lecturer.dean.dashboard');
                Route::get('/lecturer/dashboard', 'lecturer_dashboard')->name('lecturer.dashboard');
                Route::get('/department/{department}/students', 'department_students')->name('dean.department.students');

                // Staff management
                Route::get('/staff', 'listStaff')->name('staff.index');
                Route::post('/staff', 'addStaff')->name('staff.store');
                Route::put('/staff/{staff}', 'updateStaff')->name('staff.update');
                Route::delete('/staff/{staff}', 'destroyStaff')->name('staff.destroy');

                // Course assignment management
                Route::get('staff/course-assignments', 'courseAssignments')->name('staff.course.assignments');
                Route::post('staff/course-assignments', 'assignCourse')->name('staff.course.assign');
                Route::delete('staff/course-assignments/{id}', 'deleteAssignment')->name('staff.course.assign.delete');

            });

            // Dean course management
            Route::controller(CourseController::class)->group(function () {
                Route::get('/courses', 'index_course')->name('staff.courses.index');
                Route::post('/courses', 'store')->name('staff.courses.store');
                Route::put('/courses/{course}', 'update')->name('staff.courses.update');
                Route::delete('/courses/{course}', 'destroy')->name('staff.courses.destroy');
            });

            // Dean result management
            Route::get('results/upload', [ResultController::class, 'uploadPage'])->name('staff.results.upload');
            Route::post('results/upload', [ResultController::class, 'processUpload'])->name('staff.results.process');
            Route::get('results/download-template/{courseId}', [ResultController::class, 'downloadTemplate'])->name('staff.results.template');
            Route::get('results/download', [ResultController::class, 'downloadSheet'])->name('staff.results.download');
            Route::get('/results/view-uploaded', [ResultController::class, 'viewuploadReport'])->name('results.viewUploaded');
            Route::get('/results/download-uploaded-results', [ResultController::class, 'downloadResults'])->name('results.download');
            Route::get('/backlog-upload', [ResultController::class, 'showBacklogUploadPage'])->name('backlog.upload.page');
            Route::post('/backlog-upload/process', [ResultController::class, 'processBacklogUpload'])->name('backlog.upload.process');
            Route::get('/backlog-upload/template', [ResultController::class, 'downloadBacklogTemplate'])->name('backlog.upload.template');
            // Route::get('/transcript/{student}', [ResultController::class, 'viewTranscript'])->name('transcript.view');
            Route::get('/transcript/result/view', [ResultController::class, 'searchTranscript'])->name('transcript.search');
            Route::get('/transcript/search', [ResultController::class, 'transcriptSearchPage'])->name('transcript.search.page');
            Route::put('/results/{id}', [ResultController::class, 'update'])->name('results.update');
            Route::delete('/results/{id}', [ResultController::class, 'destroy'])->name('results.delete');
            Route::get('/results/summary', [ResultController::class, 'summaryByDepartment'])->name('results.summary');
            // Page to view list and filter
            Route::get('/results/manage-status', [ResultController::class, 'manageStatus'])->name('results.manage.status');

            // Action to update the status
            Route::post('/results/update-status', [ResultController::class, 'updateStatus'])->name('results.update.status');
            Route::post('/results/bulk-update-status', [ResultController::class, 'bulkUpdateStatus'])->name('results.bulk.update');

        });
    });

    Route::prefix('ict')->middleware('user.type:ict')->group(function () {
        Route::controller(IctStudentController::class)->group(function () {
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

            // User CRUD
            Route::get('/users', 'getAllUsers')->name('ict.staff.users.index');
            Route::post('/users', 'storeUsers');
            Route::post('users/{id}', 'updateUsers')->name('ict.staff.users.update');

            Route::post('users/{id}/disable', 'disableUser')->name('ict.staff.users.disable');
            Route::post('users/{id}/enable', 'enableUser')->name('ict.staff.users.enable');
            Route::delete('users/{id}/force-delete', 'forceDeleteUser')->name('ict.staff.users.destroy');
            Route::resource('news', NewsController::class)->names('ict.news');

        });

        Route::controller(\App\Http\Controllers\Staff\Ict\ApplicationSettingController::class)->group(function () {
            Route::get('/application-settings', 'index')->name('ict.application_settings.index');
            Route::get('/application-settings/create', 'create')->name('ict.application_settings.create');
            Route::post('/application-settings', 'store')->name('ict.application_settings.store');
            Route::get('/application-settings/{id}/edit', 'edit')->name('ict.application_settings.edit');
            Route::post('/application-settings/{id}', 'update')->name('ict.application_settings.update');
        });

        Route::controller(\App\Http\Controllers\Staff\SystemSettingController::class)->group(function () {
            Route::get('/system-settings', 'index')->name('ict.system_settings.index');
            Route::post('/system-settings', 'update')->name('ict.system_settings.update');
            Route::post('/system-settings/grading', 'updateGrading')->name('ict.system_settings.grading.update');
        });
    });
});
