public function deleteApplication($user_application_id)
{
$user = Auth::user();

$application = UserApplications::where('user_id', $user->id)
->where('id', $user_application_id)
->firstOrFail();

// Prevent deletion if approved
if ($application->is_approved == 1) {
return redirect()->back()->with('error', 'Approved applications cannot be deleted.');
}

try {
DB::beginTransaction();

// Delete related records
// Note: If you have foreign keys with cascading deletes, this manual cleanup might be redundant but safe.
Profile::where('user_application_id', $user_application_id)->delete();
Olevel::where('user_application_id', $user_application_id)->delete();
Alevel::where('user_application_id', $user_application_id)->delete();
JambDetail::where('user_application_id', $user_application_id)->delete();
CourseOfStudy::where('user_application_id', $user_application_id)->delete();
EducationHistory::where('user_application_id', $user_application_id)->delete();

// Delete Documents and their files
$documents = Document::where('user_application_id', $user_application_id)->get();
foreach ($documents as $doc) {
if (Storage::exists($doc->file_path)) {
Storage::delete($doc->file_path);
}
$doc->delete();
}

// Finally delete the application
$application->delete();

DB::commit();

return redirect()->route('application.dashboard')->with('success', 'Application deleted successfully.');

} catch (\Exception $e) {
DB::rollBack();
Log::error('Application Deletion Error: ' . $e->getMessage());
return redirect()->back()->with('error', 'An error occurred while deleting the application.');
}
}