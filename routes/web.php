<?php

use App\Http\Controllers\Application\AdmittedStudentsDownloadController;
use App\Http\Controllers\Application\ApplicationController;
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
    // --- Staff Authentication ---
    Route::controller(StaffAuthController::class)->group(function () {
        Route::get('/', 'login')->name('staff.login');
        Route::post('/', 'loginAction');
        Route::get('logout', 'logoutAction')->name('staff.logout');
    });

    // --- Staff Base & Admissions Management ---
    Route::middleware(['auth', 'dynamic.permission'])->group(function () {
        // Unified Admission Management
        Route::prefix('admission')->group(function () {
            Route::controller(AdminGeneralController::class)->group(function () {
                Route::get('/overview', 'index_admin')->name('admission.overview');
                Route::get('/applicants', 'index_admin')->name('admission.applicants');
                Route::get('/applicants/{user}/{application}', 'showApplicantDetails')->name('admission.details');
                Route::post('/admit/{userId}', 'admitStudent')->name('admission.admit');
                Route::post('/recommend/{userId}', 'recommendStudent')->name('admission.recommend');
                Route::get('/export-applicants', 'exportApplicants')->name('admission.exportApplicants');
            });
        });

        // Admin Dashboard & Agent Management
        Route::get('/dashboard', [AdminGeneralController::class, 'index_admin'])->name('admin.dashboard');
        Route::controller(AdminGeneralController::class)->group(function () {
            // Agent Application Management
            Route::get('/agent-applicants', 'showAgentDetail')->name('admin.agent.applicants');
            Route::post('/agent-applicants/update-status', 'changeAgentStatus')->name('admin.agent.application.update_status');
        });

        // Vice-Chancellor Dashboard
        Route::prefix('vc')->group(function () {
            Route::controller(\App\Http\Controllers\Staff\Vc\VcController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('vc.dashboard');
            });
        });

        // Registrar Dashboard
        Route::prefix('registrar')->group(function () {
            Route::controller(\App\Http\Controllers\Staff\Registrar\RegistrarController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('registrar.dashboard');
            });
        });

        // Center Director
        Route::prefix('center-director')
            ->controller(\App\Http\Controllers\Staff\CenterDirector\CenterDirectorController::class)
            ->group(function () {
                Route::get('/dashboard', 'dashboard')->name('center-director.dashboard');
            });

        // Programme Director
        Route::prefix('programme-director')->group(function () {
            Route::controller(\App\Http\Controllers\Staff\ProgrammeDirector\ProgrammeDirectorController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('programme-director.dashboard');
            });
        });

        // --- Student Management (ICT/Bursary view) ---
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
            Route::get('/ict/students/bulk', 'bulkUploadForm')->name('ict.students.bulk');
            Route::post('/ict/students/bulk', 'bulkUpload')->name('ict.students.bulk.upload');
            Route::get('/ict/students/bulk/template', 'downloadTemplate')->name('ict.students.bulk.template');
        });

        // --- Financial / Bursary Management ---
        Route::prefix('burser')->group(function () {
            Route::controller(BursaryController::class)->group(function () {
                Route::get('/dashboard', 'dashboard')->name('burser.dashboard');
                Route::get('/student-history', 'searchStudentHistory')->name('bursary.student.history');
                Route::get('/student-receipt/{reference}', 'downloadReceipt')->name('bursary.student.receipt');
                Route::get('/transactions', 'transactions')->name('bursary.transactions');
                Route::get('/transactions/export/{format}', 'exportTransactions')->name('bursary.transactions.export');
                Route::get('/transactions/{id}/verify', 'verifySingle')->name('bursary.transactions.verify');
                Route::get('/verify-payment', 'verifyPaymentForm')->name('bursary.verify.form');
                Route::post('/verify-payment', 'verifyPaymentAction')->name('bursary.verify.action');
                Route::get('/transactions/create', 'createManual')->name('bursary.transactions.create');
                Route::post('/transactions/store', 'storeManual')->name('bursary.transactions.store');
                Route::put('/transactions/update/{transaction}', 'updateManual')->name('bursary.transactions.update');
                Route::delete('/transactions/destroy/{transaction}', 'destroyManual')->name('bursary.transactions.destroy');
                Route::get('/reports/faculty', 'reportByFaculty')->name('bursary.reports.faculty');
                Route::get('/reports/department', 'reportByDepartment')->name('bursary.reports.department');
                Route::get('/reports/level', 'reportByLevel')->name('bursary.reports.level');
                Route::get('/reports/student', 'reportByStudent')->name('bursary.reports.student');
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

        // --- Academic / Result Management ---
        Route::middleware(['user.type:staff'])->group(function () {
            // Broadsheets
            Route::prefix('reports/broadsheet')->group(function () {
                Route::controller(\App\Http\Controllers\Staff\BroadsheetController::class)->group(function () {
                    Route::get('/sessional', 'indexSessional')->name('broadsheet.sessional');
                    Route::get('/semester', 'indexSemester')->name('broadsheet.semester');
                    Route::get('/print-official', 'printOfficial')->name('broadsheet.printOfficial');
                });
            });

            // Dean / Lecturer Portal
            Route::prefix('dean')->group(function () {
                Route::controller(LecturerGeneralController::class)->group(function () {
                    Route::get('/dashboard', 'dean_dashboard')->name('lecturer.dean.dashboard');
                    Route::get('/lecturer/dashboard', 'lecturer_dashboard')->name('lecturer.dashboard');
                    Route::get('/department/{department}/students', 'department_students')->name('dean.department.students');

                    // Staff management
                    Route::get('/staff', 'listStaff')->name('staff.index');
                    Route::post('/staff', 'addStaff')->name('staff.store');
                    Route::put('/staff/{staff}', 'updateStaff')->name('staff.update');
                    Route::delete('/staff/{staff}', 'destroyStaff')->name('staff.destroy');

                    // Course assignments
                    Route::get('staff/course-assignments', 'courseAssignments')->name('staff.course.assignments');
                    Route::post('staff/course-assignments', 'assignCourse')->name('staff.course.assign');
                    Route::delete('staff/course-assignments/{id}', 'deleteAssignment')->name('staff.course.assign.delete');
                });

                // Course management
                Route::controller(CourseController::class)->group(function () {
                    Route::get('/courses', 'index_course')->name('staff.courses.index');
                    Route::post('/courses', 'store')->name('staff.courses.store');
                    Route::put('/courses/{course}', 'update')->name('staff.courses.update');
                    Route::delete('/courses/{course}', 'destroy')->name('staff.courses.destroy');
                });

                // Results
                Route::controller(ResultController::class)->group(function () {
                    Route::get('results/upload', 'uploadPage')->name('staff.results.upload');
                    Route::post('results/upload', 'processUpload')->name('staff.results.process');
                    Route::get('results/download-template/{courseId}', 'downloadTemplate')->name('staff.results.template');
                    Route::get('results/download', 'downloadSheet')->name('staff.results.download');
                    Route::get('/backlog-upload', 'showBacklogUploadPage')->name('backlog.upload.page');
                    Route::post('/backlog-upload/process', 'processBacklogUpload')->name('backlog.upload.process');
                    Route::get('/backlog-upload/template', 'downloadBacklogTemplate')->name('backlog.upload.template');
                    Route::put('/results/{id}', 'update')->name('results.update');
                    Route::delete('/results/{id}', 'destroy')->name('results.delete');
                    Route::get('/results/view-uploaded', 'viewUploadedResults')->name('results.viewUploaded');
                    Route::get('/results/print-uploaded', 'printUploadedResults')->name('results.printUploaded');
                    Route::get('/results/download-uploaded-results', 'downloadResults')->name('results.download');
                    Route::get('/results/manage-status', 'manageStatus')->name('results.manage.status');
                    Route::post('/results/update-status', 'updateStatus')->name('results.update.status');
                    Route::post('/results/bulk-update-status', 'bulkUpdateStatus')->name('results.bulk.update');
                    Route::get('/results/summary', 'summaryByDepartment')->name('results.summary');
                    Route::get('/results/print-summary', 'printSummaryReport')->name('results.printSummary');
                    Route::get('/transcript/result/view', 'searchTranscript')->name('transcript.search');
                    Route::get('/transcript/search', 'transcriptSearchPage')->name('transcript.search.page');
                    Route::get('/transcript/print', 'printTranscript')->name('results.printTranscript');
                });
            });
        });

        // --- ICT Management / System Setup ---
        Route::prefix('ict')->group(function () {
            Route::get('/dashboard', [IctStudentController::class, 'dashboard'])->name('ict.dashboard');

            // User Management
            Route::controller(IctStudentController::class)->group(function () {
                Route::get('/users', 'getAllUsers')->name('ict.staff.users.index');
                Route::post('/users', 'storeUsers');
                Route::post('users/{id}', 'updateUsers')->name('ict.staff.users.update');
                Route::post('users/{id}/disable', 'disableUser')->name('ict.staff.users.disable');
                Route::post('users/{id}/enable', 'enableUser')->name('ict.staff.users.enable');
                Route::delete('users/{id}/force-delete', 'forceDeleteUser')->name('ict.staff.users.destroy');

                // Search API
                Route::get('/api/search/students', [App\Http\Controllers\Staff\Ict\UserSearchController::class, 'searchStudents'])->name('ict.search.students');
                Route::get('/api/search/lecturers', [App\Http\Controllers\Staff\Ict\UserSearchController::class, 'searchLecturers'])->name('ict.search.lecturers');
            });

            // Website / News Management
            Route::resource('news', NewsController::class)->names('ict.news');

            // Application Setup
            Route::controller(\App\Http\Controllers\Staff\Ict\IctApplicationController::class)->group(function () {
                Route::get('/applications/incomplete', 'incompleteApplications')->name('ict.applications.incomplete');
                Route::post('/applications/unsubmit/{id}', 'unsubmitApplication')->name('ict.applications.unsubmit');
            });

            Route::controller(\App\Http\Controllers\Staff\Ict\ApplicationSettingController::class)->group(function () {
                Route::get('/application-settings', 'index')->name('ict.application_settings.index');
                Route::get('/application-settings/create', 'create')->name('ict.application_settings.create');
                Route::post('/application-settings', 'store')->name('ict.application_settings.store');
                Route::get('/application-settings/{id}/edit', 'edit')->name('ict.application_settings.edit');
                Route::post('/application-settings/{id}', 'update')->name('ict.application_settings.update');
            });

            // System Settings
            Route::controller(\App\Http\Controllers\Staff\SystemSettingController::class)->group(function () {
                Route::get('/system-settings', 'index')->name('ict.system_settings.index');
                Route::post('/system-settings', 'update')->name('ict.system_settings.update');
                Route::post('/system-settings/grading', 'updateGrading')->name('ict.system_settings.grading.update');
            });

            // User Types & Permissions
            Route::controller(\App\Http\Controllers\Staff\Ict\UserTypeController::class)->group(function () {
                Route::get('/user-types', 'index')->name('ict.user-types.index');
                Route::get('/user-types/create', 'create')->name('ict.user-types.create');
                Route::post('/user-types', 'store')->name('ict.user-types.store');
                Route::get('/user-types/{id}/permissions', 'permissions')->name('ict.user-types.permissions');
                Route::post('/user-types/{id}/permissions', 'updatePermissions')->name('ict.user-types.permissions.update');
            });

            Route::controller(\App\Http\Controllers\Staff\Ict\PermissionController::class)->group(function () {
                Route::get('/permissions', 'index')->name('ict.permissions.index');
                Route::get('/permissions/create', 'create')->name('ict.permissions.create');
                Route::post('/permissions', 'store')->name('ict.permissions.store');
                Route::get('/permissions/{permission}/edit', 'edit')->name('ict.permissions.edit');
                Route::put('/permissions/{permission}', 'update')->name('ict.permissions.update');
                Route::delete('/permissions/{permission}', 'destroy')->name('ict.permissions.destroy');
            });

            // Menu Management
            Route::controller(\App\Http\Controllers\Staff\Ict\MenuItemController::class)->group(function () {
                Route::get('/menu-items', 'index')->name('ict.menu-items.index');
                Route::get('/menu-items/create', 'create')->name('ict.menu-items.create');
                Route::post('/menu-items', 'store')->name('ict.menu-items.store');
                Route::get('/menu-items/{menuItem}/edit', 'edit')->name('ict.menu-items.edit');
                Route::put('/menu-items/{menuItem}', 'update')->name('ict.menu-items.update');
                Route::post('/menu-items/{menuItem}/toggle', 'toggle')->name('ict.menu-items.toggle');
                Route::delete('/menu-items/{menuItem}', 'destroy')->name('ict.menu-items.destroy');
            });

            // Academic Setup
            Route::controller(\App\Http\Controllers\Staff\Ict\FacultyController::class)->group(function () {
                Route::get('/faculties', 'index')->name('ict.faculties.index');
                Route::post('/faculties', 'store')->name('ict.faculties.store');
                Route::put('/faculties/{id}', 'update')->name('ict.faculties.update');
                Route::delete('/faculties/{id}', 'destroy')->name('ict.faculties.destroy');
            });

            Route::controller(\App\Http\Controllers\Staff\Ict\DepartmentController::class)->group(function () {
                Route::get('/departments', 'index')->name('ict.departments.index');
                Route::post('/departments', 'store')->name('ict.departments.store');
                Route::put('/departments/{id}', 'update')->name('ict.departments.update');
                Route::delete('/departments/{id}', 'destroy')->name('ict.departments.destroy');
            });

            Route::controller(\App\Http\Controllers\Staff\Ict\AcademicSessionController::class)->group(function () {
                Route::get('/sessions', 'index')->name('ict.sessions.index');
                Route::post('/sessions', 'store')->name('ict.sessions.store');
                Route::put('/sessions/{id}', 'update')->name('ict.sessions.update');
                Route::delete('/sessions/{id}', 'destroy')->name('ict.sessions.destroy');
            });

            Route::controller(\App\Http\Controllers\Staff\Ict\AcademicSemesterController::class)->group(function () {
                Route::get('/semesters', 'index')->name('ict.semesters.index');
                Route::post('/semesters', 'store')->name('ict.semesters.store');
                Route::put('/semesters/{id}', 'update')->name('ict.semesters.update');
                Route::delete('/semesters/{id}', 'destroy')->name('ict.semesters.destroy');
            });
        });
    });
});
