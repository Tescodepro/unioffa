<?php

use App\Http\Controllers\Application\AdmittedStudentsDownloadController;
use App\Http\Controllers\Application\ApplicationController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\Staff\BursaryController;
use App\Http\Controllers\Staff\GeneralController as StaffGeneralController;
use App\Http\Controllers\Staff\Ict\AcademicSemesterController;
use App\Http\Controllers\Staff\Ict\AcademicSessionController;
use App\Http\Controllers\Staff\Ict\ApplicationSettingController;
use App\Http\Controllers\Staff\Ict\DepartmentController as IctDepartmentController;
use App\Http\Controllers\Staff\Ict\FacultyController as IctFacultyController;
use App\Http\Controllers\Staff\Ict\IctApplicationController;
use App\Http\Controllers\Staff\Ict\IctStudentController;
use App\Http\Controllers\Staff\Ict\MenuItemController;
use App\Http\Controllers\Staff\Ict\PermissionController;
use App\Http\Controllers\Staff\Ict\UserSearchController;
use App\Http\Controllers\Staff\Ict\UserTypeController;
use App\Http\Controllers\Staff\Lecturer\CourseController;
use App\Http\Controllers\Staff\Lecturer\LecturerGeneralController;
use App\Http\Controllers\Staff\Lecturer\ResultController;
use App\Http\Controllers\Staff\PaymentSettingController;
use App\Http\Controllers\Staff\Registrar\RegistrarController;
use App\Http\Controllers\Staff\SystemSettingController;
use App\Http\Controllers\Staff\Vc\VcController;
use App\Http\Controllers\Staff\CenterDirector\CenterDirectorController;
use App\Http\Controllers\Staff\ProgrammeDirector\ProgrammeDirectorController;
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\website\GeneralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Re-organized into STUDENT SIDE and STAFF SIDE
|--------------------------------------------------------------------------
*/

// =========================================================================
// PUBLIC & SHARED ROUTES
// =========================================================================

// Public News Route (no auth required)
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

Route::controller(GeneralController::class)->group(function () {
    // General Pages
    Route::get('/', 'home')->name('home');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/news', 'blog')->name('blog');

    // Applications (Public Initiations)
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

// Shared Payment Routes
Route::prefix('payments')->group(function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('initiate', 'initiatePayment')->name('application.payment.process');
        Route::get('callback', 'handleCallback')->name('payment.callback');
        Route::get('payment-status-page', 'paymentStatusPage')->name('payment.status.page');
        Route::get('/verify-receipt/{ref}', 'verifyReceipt')->name('verify.receipt');
        Route::get('/receipt/{reference}', 'downloadReceipt')->name('view.receipt');
    });
});

// =========================================================================
// STUDENT SIDE (Admission & Student Portal)
// =========================================================================

// ====== Admission / Application Routes ======= //
Route::prefix('admission')->group(function () {
    Route::controller(ApplicationController::class)->group(function () {
        Route::get('/', 'login')->name('application.login');
        Route::post('/', 'loginAction');

        Route::get('/register', 'index')->name('application.register');
        Route::post('/register', 'createAccount');

        Route::get('logout', 'logoutAction')->name('application.logout');

        Route::get('/dashboard', 'dashboard')->middleware(['auth', 'dynamic.permission'])->name('application.dashboard');
        Route::post('/start-application', 'startApplication')->middleware(['auth', 'dynamic.permission'])->name('application.start');
        Route::get('/form/{user_application_id}', 'applicationForm')->middleware(['auth', 'dynamic.permission'])->name('application.form');

        // ======= form submission ======= //
        Route::post('/form/save-profile/{user_application_id}', 'saveProfile')->middleware(['auth', 'dynamic.permission'])->name('application.personal_info.submit');
        Route::post('/form/save-olevel/{user_application_id}', 'saveOlevel')->middleware(['auth', 'dynamic.permission'])->name('application.olevel.submit');
        Route::post('/form/save-alevel/{user_application_id}', 'saveAlevel')->middleware(['auth', 'dynamic.permission'])->name('application.alevel.submit');
        Route::post('/form/save-jamb-details/{user_application_id}', 'saveJambDetails')->middleware(['auth', 'dynamic.permission'])->name('application.jamb_details.submit');
        Route::post('/form/save-course-of-study/{user_application_id}', 'saveCourseOfStudy')->middleware(['auth', 'dynamic.permission'])->name('application.course_of_study.submit');
        Route::post('/form/save-documents/{user_application_id}', 'saveDocuments')->middleware(['auth', 'dynamic.permission'])->name('application.documents.submit');
        Route::post('/form/handle-form-submission/{user_application_id}', 'handleFormSubmission')->middleware(['auth', 'dynamic.permission'])->name('application.handle_form_submission');
        Route::delete('/application/{user_application_id}', 'deleteApplication')->middleware(['auth', 'dynamic.permission'])->name('application.delete');
        Route::get('/admission-letter/{applicationId}', 'downloadAdmissionLetter')->name('student.admission.letter')->middleware('auth');
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('application.forgot.password');
        Route::post('/forgot-password', 'postForgotPassword')->name('password.email');
        Route::get('/password/update-otp', 'showUpdateWithOtp')->name('password.otp.update');
        Route::post('/password/update-otp', 'updateWithOtp');
        Route::get('/application/{id}/download', 'downloadApplicantDetails')->name('applicant.printout.download')->middleware('auth');
    });
});

// ====== Student Portal Routes ======= //
Route::prefix('students')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/', 'login')->name('student.login');
        Route::post('/', 'loginAction');

        Route::get('/forget-password', 'forgetPasswordIndex')->name('students.auth.forget-password');
        Route::post('/forget-password', 'forgetPasswordAction');
        Route::get('/auth/change-password', 'verifyOtpIndex')->name('students.auth.change-password');
        Route::post('/auth/change-password', 'verifyOtpAction');
    });

    Route::middleware(['auth', 'dynamic.permission'])->group(function () {
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

            // Student Results & Transcript
            Route::get('/results', 'viewResults')->name('students.results');
            Route::get('/transcript', 'viewTranscript')->name('students.transcript');
            Route::get('/transcript/download', 'downloadTranscript')->name('students.transcript.download');
        });

        Route::controller(CourseRegistrationController::class)->group(function () {
            Route::get('/course-registration', 'index')->name('students.course.registration');
            Route::post('course-registration', 'store');
            Route::delete('course-registration/{id}', 'removeCourse')->name('students.course.remove');
            Route::get('course-registration/download', 'downloadCourseForm')->name('students.course.download');
        });
    });
});

// =========================================================================
// STAFF SIDE (Staff Portal)
// =========================================================================

Route::prefix('staff')->group(function () {
    // --- Staff Authentication (Keep as is, but remove dynamic.permission from auth-logout) ---
    Route::controller(StaffAuthController::class)->group(function () {
        Route::get('/', 'login')->name('staff.login');
        Route::post('/', 'loginAction');
        Route::get('logout', 'logoutAction')->name('staff.logout');
    });

    // --- Unified Staff Portal ---
    // All staff access is now purely determined by the 'dynamic.permission' middleware
    // which checks the 'route_permissions' table mappings.
    Route::middleware(['auth', 'user.type:staff', 'dynamic.permission'])->group(function () {

        // --- Admissions & Agent Management ---
        Route::controller(StaffGeneralController::class)->group(function () {
            Route::get('/admission/overview', 'index_admin')->name('admission.overview');
            Route::get('/admission/applicants', 'index_admin')->name('admission.applicants');
            Route::get('/admission/applicants/{user}/{application}', 'showApplicantDetails')->name('admission.details');
            Route::post('/admission/admit/{userId}', 'admitStudent')->name('admission.admit');
            Route::post('/admission/recommend/{userId}', 'recommendStudent')->name('admission.recommend');
            Route::get('/admission/export-applicants', 'exportApplicants')->name('admission.exportApplicants');
            
            Route::get('/agent-applicants', 'showAgentDetail')->name('admin.agent.applicants');
            Route::post('/agent-applicants/status', 'changeAgentStatus')->name('admin.agent.application.update_status');
        });

        // --- Dashboards (Permission-Driven) ---
        Route::get('/dashboard', [StaffGeneralController::class, 'index_admin'])->name('admin.dashboard');

        Route::get('/vc/dashboard', [VcController::class, 'dashboard'])->name('vc.dashboard');
        Route::get('/registrar/dashboard', [RegistrarController::class, 'dashboard'])->name('registrar.dashboard');
        Route::get('/center-director/dashboard', [CenterDirectorController::class, 'dashboard'])->name('center-director.dashboard');
        Route::get('/center-director/admission/applicants', [CenterDirectorController::class, 'admissionApplicants'])->name('center-director.admission.applicants');
        Route::get('/programme-director/dashboard', [ProgrammeDirectorController::class, 'dashboard'])->name('programme-director.dashboard');

        // --- Student & ICT Management ---
        Route::controller(AdmittedStudentsDownloadController::class)->group(function () {
            Route::get('/admitted-students', 'index')->name('admitted-students.index');
            Route::post('/admitted-students/download', 'download')->name('admitted-students.download');
        });

        Route::controller(IctStudentController::class)->group(function () {
            Route::get('/ict/students', 'index')->name('ict.students.index');
            Route::get('/ict/students/create', 'create')->name('ict.students.create');
            Route::post('/ict/students', 'store')->name('ict.students.store');
            Route::get('/ict/students/{student}/edit', 'edit')->name('ict.students.edit');
            Route::put('/ict/students/{student}', 'update')->name('ict.students.update');
            Route::delete('/ict/students/{student}', 'destroy')->name('ict.students.destroy');
            Route::get('/ict/students/template', 'downloadTemplate')->name('ict.students.bulk.template');
            Route::get('/ict/students/bulk', 'bulkUploadForm')->name('ict.students.bulk');
            Route::post('/ict/students/bulk', 'bulkUpload')->name('ict.students.bulk.upload');
        });

        // --- Financial / Bursary Management ---
        Route::prefix('burser')->group(function () {
            Route::controller(BursaryController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('burser.dashboard');
                Route::get('/transactions', 'transactions')->name('bursary.transactions');
                Route::get('/reports/faculty', 'reportByFaculty')->name('bursary.reports.faculty');
                Route::get('/reports/department', 'reportByDepartment')->name('bursary.reports.department');
                Route::get('/reports/level', 'reportByLevel')->name('bursary.reports.level');
                Route::get('/reports/student', 'reportByStudent')->name('bursary.reports.student');
                Route::get('/reports/export/{type}/{format}', 'export')->name('bursary.reports.export');
                Route::get('/student-history', 'searchStudentHistory')->name('bursary.student.history');
                Route::get('/student-receipt/{reference}', 'downloadReceipt')->name('bursary.student.receipt');
                Route::get('/transactions/export/{format}', 'exportTransactions')->name('bursary.transactions.export');
                Route::get('/transactions/{id}/verify', 'verifySingle')->name('bursary.transactions.verify');
                Route::get('/verify-payment', 'verifyPaymentForm')->name('bursary.verify.form');
                Route::post('/verify-payment', 'verifyPaymentAction')->name('bursary.verify.action');
                Route::get('/transactions/create', 'createManual')->name('bursary.transactions.create');
                Route::post('/transactions/store', 'storeManual')->name('bursary.transactions.store');
                Route::put('/transactions/update/{transaction}', 'updateManual')->name('bursary.transactions.update');
                Route::delete('/transactions/destroy/{transaction}', 'destroyManual')->name('bursary.transactions.destroy');
            });
            Route::controller(PaymentSettingController::class)->group(function () {
                Route::get('/payment-settings', 'index')->name('bursary.payment-settings.index');
                Route::post('/payment-settings', 'store')->name('bursary.payment-settings.store');
                Route::get('/payment-settings/create', 'create')->name('bursary.payment-settings.create');
                Route::get('/payment-settings/{paymentSetting}/edit', 'edit')->name('bursary.payment-settings.edit');
                Route::put('/payment-settings/{paymentSetting}', 'update')->name('bursary.payment-settings.update');
                Route::delete('/payment-settings/{paymentSetting}', 'destroy')->name('bursary.payment-settings.destroy');
            });
        });

        // --- Academic / Result Management ---
        // REMOVED 'user.type:staff' as it's now in the parent group
        Route::prefix('reports/broadsheet')->group(function () {
            Route::controller(\App\Http\Controllers\Staff\BroadsheetController::class)->group(function () {
                Route::get('/sessional', 'indexSessional')->name('broadsheet.sessional');
                Route::get('/semester', 'indexSemester')->name('broadsheet.semester');
                Route::get('/print-official', 'printOfficial')->name('broadsheet.printOfficial');
            });
        });

        Route::prefix('dean')->group(function () {
            Route::controller(LecturerGeneralController::class)->group(function () {
                Route::get('/dashboard', 'dean_dashboard')->name('lecturer.dean.dashboard');
                Route::get('/lecturer/dashboard', 'lecturer_dashboard')->name('lecturer.dashboard');
                Route::get('/department/{department}/students', 'department_students')->name('dean.department.students');
                Route::get('/staff-list', 'listStaff')->name('staff.users.index');
                Route::post('/staff-list', 'addStaff')->name('staff.users.store');
                Route::put('/staff-list/{id}', 'updateStaff')->name('staff.users.update');
                Route::delete('/staff-list/{id}', 'destroyStaff')->name('staff.users.destroy');
                
                Route::get('/course-assignments', 'courseAssignments')->name('staff.course.assignments');
                Route::post('/course-assignments', 'assignCourse')->name('staff.course.assign');
                Route::delete('/course-assignments/{id}', 'deleteAssignment')->name('staff.course.assign.delete');

                // Legacy names for compatibility (optional)
                Route::get('/staff', 'listStaff')->name('staff.index');
                Route::post('/staff', 'addStaff')->name('staff.store');
                Route::put('/staff/{staff}', 'updateStaff')->name('staff.update');
                Route::delete('/staff/{staff}', 'destroyStaff')->name('staff.destroy');
            });

            Route::controller(CourseController::class)->group(function () {
                Route::get('/courses', 'index_course')->name('staff.courses.index');
                Route::post('/courses', 'store')->name('staff.courses.store');
                Route::put('/courses/{course}', 'update')->name('staff.courses.update');
                Route::delete('/courses/{course}', 'destroy')->name('staff.courses.destroy');
            });

            Route::controller(ResultController::class)->group(function () {
                Route::get('results/upload', 'uploadPage')->name('staff.results.upload');
                Route::post('results/upload', 'processUpload')->name('staff.results.process');
                Route::get('results/download', 'downloadSheet')->name('staff.results.download');
                Route::get('/results/manage-status', 'manageStatus')->name('results.manage.status');
                Route::post('/results/update-status', 'updateStatus')->name('results.update.status');
                Route::get('/results/view-uploaded', 'viewUploadedResults')->name('results.viewUploaded');
                Route::get('/results/print-uploaded', 'printUploadedResults')->name('results.printUploaded');
                Route::get('/results/download-detailed', 'downloadResults')->name('results.download');
                Route::get('/results/summary', 'summaryByDepartment')->name('results.summary');
                Route::get('/results/print-summary', 'printSummaryReport')->name('results.printSummary');
                Route::get('/results/transcript/search', 'transcriptSearchPage')->name('transcript.search.page');
                Route::get('/results/transcript/action', 'searchTranscript')->name('transcript.search');
                Route::post('/results/bulk-update-status', 'bulkUpdateStatus')->name('results.bulk.update');
                Route::get('/results/transcript/print', 'printTranscript')->name('results.printTranscript');
                Route::put('/results/{id}', 'update')->name('results.update');
                Route::delete('/results/{id}', 'destroy')->name('results.delete');
                Route::get('/results/backlog-upload', 'showBacklogUploadPage')->name('backlog.upload.page');
                Route::post('/results/backlog-upload', 'processBacklogUpload')->name('backlog.upload.process');
                Route::get('/results/backlog-template', 'downloadBacklogTemplate')->name('backlog.upload.template');
            });
        });

        // --- ICT Management / System Setup ---
        Route::prefix('ict')->name('ict.')->group(function () {
            Route::get('/dashboard', [IctStudentController::class, 'dashboard'])->name('dashboard');

            Route::resource('application-settings', ApplicationSettingController::class)->except(['show', 'destroy']);
            Route::resource('semesters', AcademicSemesterController::class)->except(['show', 'create', 'edit']);
            Route::resource('sessions', AcademicSessionController::class)->except(['show', 'create', 'edit']);
            Route::resource('faculties', IctFacultyController::class)->except(['show', 'create', 'edit']);
            Route::resource('departments', IctDepartmentController::class)->except(['show', 'create', 'edit']);

            Route::controller(IctStudentController::class)->group(function () {
                Route::get('/staff/users', 'getAllUsers')->name('staff.users.index');
                Route::post('/staff/users', 'storeUsers')->name('staff.users.store');
                Route::post('/staff/users/{id}', 'updateUsers')->name('staff.users.update');
                Route::delete('/staff/users/{id}/force-delete', 'forceDeleteUser')->name('staff.users.destroy');
                Route::post('/staff/users/{id}/disable', 'disableUser')->name('staff.users.disable');
                Route::post('/staff/users/{id}/enable', 'enableUser')->name('staff.users.enable');
            });

            Route::controller(IctApplicationController::class)->prefix('applications')->group(function () {
                Route::get('/incomplete', 'incompleteApplications')->name('applications.incomplete');
                Route::post('/{id}/unsubmit', 'unsubmitApplication')->name('applications.unsubmit');
            });

            Route::controller(UserSearchController::class)->prefix('search')->group(function () {
                Route::get('/students', 'searchStudents')->name('search.students');
                Route::get('/lecturers', 'searchLecturers')->name('search.lecturers');
            });

            Route::resource('news', NewsController::class)->names('news');

            Route::resource('user-types', UserTypeController::class)->only(['index', 'create', 'store']);
            Route::controller(UserTypeController::class)->group(function () {
                Route::get('/user-types/{userType}/permissions', 'permissions')->name('user-types.permissions');
                Route::post('/user-types/{userType}/permissions', 'updatePermissions')->name('user-types.permissions.update');
            });

            Route::controller(PermissionController::class)->group(function () {
                Route::get('/permissions', 'index')->name('permissions.index');
                Route::post('/permissions', 'store')->name('permissions.store');
                Route::put('/permissions/{permission}', 'update')->name('permissions.update');
                Route::delete('/permissions/{permission}', 'destroy')->name('permissions.destroy');
            });

            Route::controller(MenuItemController::class)->group(function () {
                Route::get('/menu-items', 'index')->name('menu-items.index');
                Route::post('/menu-items', 'store')->name('menu-items.store');
                Route::put('/menu-items/{menuItem}', 'update')->name('menu-items.update');
                Route::delete('/menu-items/{menuItem}', 'destroy')->name('menu-items.destroy');
            });

            Route::controller(SystemSettingController::class)->group(function () {
                Route::get('/system-settings', 'index')->name('system_settings.index');
                Route::post('/system-settings', 'update')->name('system_settings.update');
            });
            
            Route::resource('course-registration-settings', \App\Http\Controllers\Staff\Ict\CourseRegistrationSettingController::class);
        });
    });
});
