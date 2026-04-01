<?php

namespace App\Http\Controllers\Staff\Ict;

use App\Http\Controllers\Controller;
use App\Models\UserApplications;
use Illuminate\Http\Request;

class IctApplicationController extends Controller
{
    public function incompleteApplications()
    {
        // Get all applications that have been submitted
        $submittedApplications = UserApplications::with([
            'user.courseOfStudy',
            'applicationSetting',
            'profile',
            'olevels',
            'educationHistories',
            'jambDetail',
            'documents',
        ])
            ->whereNotNull('submitted_by')
            ->where('academic_session', activeSession()->name)
            ->where('is_approved', 0)
            ->get();

        $incompleteApplications = collect();

        foreach ($submittedApplications as $app) {
            $modules = $app->applicationSetting->modules_enable ?? [];
            $missing = [];

            if (isset($modules['profile']) && $modules['profile'] && ! $app->profile) {
                $missing[] = 'Profile';
            }
            if (isset($modules['olevel']) && $modules['olevel'] && $app->olevels->isEmpty()) {
                $missing[] = 'O\'Level';
            }
            if (isset($modules['alevel']) && $modules['alevel'] && $app->educationHistories->isEmpty()) {
                $missing[] = 'A\'Level';
            }
            if (isset($modules['course_of_study']) && $modules['course_of_study'] && ! $app->user->courseOfStudy) {
                $missing[] = 'Course of Study';
            }
            if (isset($modules['jamb_detail']) && $modules['jamb_detail'] && ! $app->jambDetail) {
                $missing[] = 'JAMB Detail';
            }
            if (isset($modules['documents']) && is_array($modules['documents'])) {
                $requiredDocsCount = count($modules['documents']);
                // The documents are grouped by type, we only check if the count matches
                // Or easier:
                $uploadedDocTypes = $app->documents->pluck('type')->toArray();
                $missingDocs = array_diff($modules['documents'], $uploadedDocTypes);
                if (! empty($missingDocs)) {
                    $missing[] = 'Documents ('.implode(', ', $missingDocs).')';
                }
            }

            if (! empty($missing)) {
                $app->missing_modules = implode(', ', $missing);
                $incompleteApplications->push($app);
            }
        }

        return view('staff.ict.applications.incomplete', compact('incompleteApplications'));
    }

    public function unsubmitApplication(Request $request, $id)
    {
        $application = UserApplications::findOrFail($id);
        $application->submitted_by = null;
        $application->save();

        return redirect()->back()->with('success', 'Application has been successfully unsubmitted. The applicant can now continue filling their form.');
    }
}
