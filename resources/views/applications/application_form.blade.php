<!DOCTYPE html>
<html lang="en">

@include('applications.partials.head')

<body>
    <!-- header area -->
    @include('applications.partials.menu')
    <!-- header area end -->
    <main>
        <!-- team single -->
        <div class="team-single pt-20 pb-100">
            <div class="container">
                @include('applications.partials.application_head')

                <div class="row mt-5">
                    @if(!isset($application_payment_status['application']))
                        <div class="col-xl-12">
                            <div id="welcome_section">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        @include('layouts.flash-message')
                                        <h4>Application Payment</h4>
                                        <p class="text-muted mb-4">
                                            Before you have access to the application form, you must pay the application fee
                                        </p>
                                        <p><strong>Application Type:</strong> {{ $application->applicationSetting->name }}</p>
                                        <p><strong>Academic Session:</strong> {{ $application->academic_session }}</p>
                                        <p><strong>Application Fee:</strong> {{ $application->applicationSetting->application_fee }}</p>
                                        {{-- Payment Status & Action --}}
                                        <div class="mt-4">
                                            @if(isset($application_payment_status['application']))
                                                <span class="badge bg-success">✅ Application Fee Paid</span>
                                            @else
                                                <button class="theme-btn mt-3" data-bs-toggle="modal" data-bs-target="#confirmPaymentModal">
                                            Pay Application Fee
                                        </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($application->is_approved && $admission_status == 1 && !isset($application_payment_status['acceptance']))
                        <div class="col-xl-12">
                            <div id="welcome_section">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        @include('layouts.flash-message')
                                        <h4>Acceptance Fee Payment</h4>
                                        <p class="text-muted mb-4">
                                            Congratulations! You have been admitted. Before proceeding, you must pay the acceptance fee.
                                        </p>
                                        <p><strong>Application Type:</strong> {{ $application->applicationSetting->name }}</p>
                                        <p><strong>Academic Session:</strong> {{ $application->academic_session }}</p>
                                        <p><strong>Acceptance Fee:</strong> {{ $application->applicationSetting->acceptance_fee }}</p>

                                        {{-- Payment Status & Action --}}
                                        <div class="mt-4">
                                            @if(isset($application_payment_status['acceptance']))
                                                <span class="badge bg-success">✅ Acceptance Fee Paid</span>
                                            @else
                                                <button class="theme-btn mt-3" data-bs-toggle="modal" data-bs-target="#confirmAcceptance">
                                                    Pay Acceptance Fee
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($application->is_approved && $admission_status == 1)
                        <div class="col-xl-12">
                            <div id="admission_letter_section">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <h4>Congratulations, {{ $application->user->full_name ?? 'Student' }}!</h4>
                                        <p class="text-muted mb-4">
                                            You have been officially admitted. You can now download your admission letter.
                                        </p>
                                        <a href="{{ route('student.admission.letter', $application->id) }}" 
                                        class="theme-btn" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Download Admission Letter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Sidebar Menu -->
                        <div class="col-xl-4 col-lg-4">
                            <div class="department-sidebar">
                                <div class="widget category">
                                    <h4 class="widget-title">Application Menu</h4>
                                    <div class="category-list">
                                        @php
                                            $moduleOrder = [
                                                'profile' => 'Personal Profile',
                                                'olevel' => 'O\'Level Results',
                                                'alevel' => 'A\'Level Results', 
                                                'course_of_study' => 'Course of Study',
                                                'documents' => 'Document Upload'
                                            ];
                                        @endphp
                                        
                                        @foreach($moduleOrder as $key => $title)
                                            @if(isset($modules[$key]) && $modules[$key])
                                                <a href="#" class="menu-item" data-section="{{ $key }}" onclick="showSection('{{ $key }}')">
                                                    <i class="far fa-long-arrow-right"></i>{{ $title }}
                                                    @if(
                                                        ($key == 'profile' && $profile) ||
                                                        ($key == 'olevel' && $olevel) ||
                                                        ($key == 'alevel' && $alevel) ||
                                                        ($key == 'course_of_study' && $courseOfStudy) ||
                                                        ($key == 'documents' && $documents->count() > 0)
                                                    )
                                                        <span class="badge bg-success ms-2">✓</span>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6>Application Details</h6>
                                        <small class="text-muted">
                                            <strong>Session:</strong> {{ $application->academic_session }}<br>
                                            <strong>Type:</strong> {{ $application->applicationSetting->name }}<br>
                                            <strong>Code:</strong> {{ $application->applicationSetting->application_code }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Main Form Content -->
                        <div class="col-xl-8 col-lg-8">
                            <div class="department-details application-form">
                                @include('layouts.flash-message')
                                <!-- Profile Section -->
                                @if(isset($modules['profile']) && $modules['profile'])
                                    <div class="form-section " id="profile_section" style="display: none;">
                                        <div class="card mb-4">
                                            <div class="card-header header-primary">
                                                <h5 class="text-white">Personal Profile</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('application.personal_info.submit', $user_application_id) }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3 form-group">
                                                            <label class="form-label">Date of Birth *</label>
                                                            <input type="date" class="form-control" name="date_of_birth" 
                                                                value="{{ old('date_of_birth', $profile->date_of_birth ?? '') }}" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3 form-group">
                                                            <label class="form-label">Gender *</label>
                                                            <select class="form-select" name="gender" required>
                                                                <option value="">Select Gender</option>
                                                                <option value="male" {{ old('gender', $profile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                                <option value="female" {{ old('gender', $profile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3 form-group">
                                                            <label class="form-label">Address *</label>
                                                            <textarea class="form-control" name="address" rows="3" required>{{ old('address', $profile->address ?? '') }}</textarea>
                                                        </div>
                                                        <div class="col-md-6 mb-3 form-group">
                                                            <label class="form-label">State of Origin *</label>
                                                            <input type="text" class="form-control" name="state_of_origin" 
                                                                value="{{ old('state_of_origin', $profile->state_of_origin ?? '') }}" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3 form-group">
                                                            <label class="form-label">Nationality *</label>
                                                            <input type="text" class="form-control" name="nationality" 
                                                                value="{{ old('nationality', $profile->nationality ?? '') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-4">
                                                        <button type="submit" name="action" value="save_continue" class="theme-btn me-2">
                                                            Save & Continue
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- O'Level Section -->
                                @if(isset($modules['olevel']) && $modules['olevel'])
                                    <div class="form-section" id="olevel_section" style="display: none;">
                                        <div class="card mb-4">
                                            <div class="card-header header-primary">
                                                <h5 class="text-white">O'Level Results</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('application.olevel.submit', $user_application_id) }}" method="POST">
                                                    @csrf
                                                    {{-- Exam Type & Year --}}
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Examination Type *</label>
                                                            <select class="form-select" name="olevel_exam_type" required>
                                                                <option value="">Select Exam Type</option>
                                                                <option value="waec" {{ old('olevel_exam_type', $olevel->exam_type ?? '') == 'waec' ? 'selected' : '' }}>WAEC</option>
                                                                <option value="neco" {{ old('olevel_exam_type', $olevel->exam_type ?? '') == 'neco' ? 'selected' : '' }}>NECO</option>
                                                                <option value="nabteb" {{ old('olevel_exam_type', $olevel->exam_type ?? '') == 'nabteb' ? 'selected' : '' }}>NABTEB</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Examination Year *</label>
                                                            <input type="number" class="form-control" name="olevel_year" 
                                                                min="2010" max="{{ date('Y') }}" 
                                                                value="{{ old('olevel_year', $olevel->exam_year ?? '') }}" required>
                                                        </div>
                                                    </div>

                                                    {{-- Subjects and Grades --}}
                                                    <h6>Subjects and Grades</h6>
                                                    <div id="olevel_subjects_container">
                                                        @php
                                                            $savedSubjects = $olevel ? (is_array($olevel->subjects) ? $olevel->subjects : json_decode($olevel->subjects, true)) : [];
                                                            $savedGrades = $olevel ? (is_array($olevel->grades) ? $olevel->grades : json_decode($olevel->grades, true)) : [];

                                                            $subjects = ['English Language', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Further Mathematics', 'Economics', 'Government', 'Literature'];
                                                            $grades = ['A1','B2','B3','C4','C5','C6','D7','E8','F9'];
                                                        @endphp

                                                        
                                                        @if(count($savedSubjects) > 0)
                                                            @foreach($savedSubjects as $index => $savedSubject)
                                                                <div class="row mb-2 olevel-row">
                                                                    <div class="col-md-6">
                                                                        <select class="form-select" name="olevel_subjects[]" required>
                                                                            <option value="">Select Subject</option>
                                                                            @foreach($subjects as $subject)
                                                                                <option value="{{ $subject }}" {{ $savedSubject == $subject ? 'selected' : '' }}>{{ $subject }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <select class="form-select" name="olevel_grades[]" required>
                                                                            <option value="">Select Grade</option>
                                                                            @foreach($grades as $grade)
                                                                                <option value="{{ $grade }}" {{ ($savedGrades[$index] ?? '') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <button type="button" class="btn btn-danger btn-remove">Remove</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="row mb-2 olevel-row">
                                                                <div class="col-md-6">
                                                                    <select class="form-select" name="olevel_subjects[]" required>
                                                                        <option value="">Select Subject</option>
                                                                        @foreach($subjects as $subject)
                                                                            <option value="{{ $subject }}">{{ $subject }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <select class="form-select" name="olevel_grades[]" required>
                                                                        <option value="">Select Grade</option>
                                                                        @foreach($grades as $grade)
                                                                            <option value="{{ $grade }}">{{ $grade }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-danger btn-remove">Remove</button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <button type="button" id="add_subject_btn" class="btn btn-primary mb-3">Add Subject</button>

                                                    {{-- Submit Buttons --}}
                                                    <div class="text-center mt-4">
                                                        <button type="submit" name="action" value="save_continue" class="theme-btn me-2">
                                                            Save & Continue
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- A'Level Section -->
                                @if(isset($modules['alevel']) && $modules['alevel'])
                                    <div class="form-section" id="alevel_section" style="display: none;">
                                        <div class="card mb-4">
                                            <div class="card-header header-primary">
                                                <h5 class="text-white">A'Level Results</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('application.alevel.submit', $user_application_id) }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Examination Type *</label>
                                                            <select class="form-select" name="alevel_exam_type">
                                                                <option value="">Select Exam Type</option>
                                                                <option value="ijmb" {{ old('alevel_exam_type', $alevel->exam_type ?? '') == 'ijmb' ? 'selected' : '' }}>IJMB</option>
                                                                <option value="jupeb" {{ old('alevel_exam_type', $alevel->exam_type ?? '') == 'jupeb' ? 'selected' : '' }}>JUPEB</option>
                                                                <option value="cambridge" {{ old('alevel_exam_type', $alevel->exam_type ?? '') == 'cambridge' ? 'selected' : '' }}>Cambridge A'Level</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Examination Year</label>
                                                            <input type="number" class="form-control" name="alevel_year" 
                                                                min="2010" max="2025" 
                                                                value="{{ old('alevel_year', $alevel->exam_year ?? '') }}">
                                                        </div>
                                                    </div>
                                                    
                                                    <h6>Subject Grades</h6>
                                                    <div class="row">
                                                        @php
                                                            $alevels = ['Mathematics', 'Physics', 'Chemistry', 'Biology', 'Economics', 'Government'];
                                                            $alevel_grades = ['A', 'B', 'C', 'D', 'E', 'F'];
                                                            $savedAlevelGrades = $alevel ? json_decode($alevel->grades, true) : [];
                                                        @endphp
                                                        
                                                        @foreach($alevels as $subject)
                                                            @php
                                                                $subjectKey = strtolower(str_replace(' ', '_', $subject));
                                                            @endphp
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">{{ $subject }}</label>
                                                                <select class="form-select" name="alevel_grades[{{ $subjectKey }}]">
                                                                    <option value="">Select Grade</option>
                                                                    @foreach($alevel_grades as $grade)
                                                                        <option value="{{ $grade }}" {{ ($savedAlevelGrades[$subjectKey] ?? '') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="text-center mt-4">
                                                        <button type="submit" name="action" value="save_continue" class="theme-btn me-2">
                                                            Save & Continue
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Course of Study Section -->
                                @if(isset($modules['course_of_study']) && $modules['course_of_study'])
                                    <div class="form-section" id="course_of_study_section" style="display: none;">
                                        <div class="card mb-4">
                                            <div class="card-header header-primary">
                                                <h5 class="text-white">Course of Study</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('application.course_of_study.submit', $user_application_id) }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <!-- First Choice -->
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">First Choice *</label>
                                                            <select class="form-select" name="first_choice" required>
                                                                <option value="">Select First Choice</option>
                                                                @foreach($faculties as $faculty)
                                                                    <optgroup label="{{ $faculty->faculty_name }}">
                                                                        @foreach($departments->where('faculty_id', $faculty->id) as $dept)
                                                                            <option value="{{ $dept->id }}"
                                                                                {{ old('first_choice', $courseOfStudy->first_department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                                                                {{ $dept->department_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                            @error('first_choice')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Second Choice -->
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Second Choice</label>
                                                            <select class="form-select" name="second_choice">
                                                                <option value="">Select Second Choice</option>
                                                                @foreach($faculties as $faculty)
                                                                    <optgroup label="{{ $faculty->faculty_name }}">
                                                                        @foreach($departments->where('faculty_id', $faculty->id) as $dept)
                                                                            <option value="{{ $dept->id }}"
                                                                                {{ old('first_choice', $courseOfStudy->second_department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                                                                {{ $dept->department_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                            @error('second_choice')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="text-center mt-4">
                                                        <button type="submit" name="action" value="save_continue" class="theme-btn me-2">
                                                            Save & Continue
                                                        </button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Documents Section -->
                                @if(isset($modules['documents']) && is_array($modules['documents']))
                                    <div class="form-section" id="documents_section" style="display: none;">
                                        <div class="card mb-4">
                                            <div class="card-header header-primary">
                                                <h5 class="text-white">Document Upload</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('application.documents.submit', $user_application_id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="alert alert-info">
                                                        <small>Upload all required documents in PDF format (max 2MB each)</small>
                                                    </div>
                                                    
                                                    @foreach($modules['documents'] as $doc)
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ ucfirst($doc) }} Certificate *</label>
                                                            @if(isset($documents[$doc]))
                                                                <div class="mb-2">
                                                                    <span class="text-success">✓ File uploaded: {{ $documents[$doc]->original_name }}</span>
                                                                    <a href="{{ asset('storage/' . $documents[$doc]->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">View</a>
                                                                </div>
                                                            @endif
                                                            <input type="file" class="form-control" name="documents[{{ $doc }}]" accept=".pdf" {{ !isset($documents[$doc]) ? 'required' : '' }}>
                                                            <small class="text-muted">PDF format only, max 2MB {{ isset($documents[$doc]) ? '(Upload new file to replace current one)' : '' }}</small>
                                                        </div>
                                                    @endforeach
                                                    
                                                    <div class="text-center mt-4">
                                                        <button type="submit" name="action" value="save_continue" class="theme-btn me-2">
                                                            Save & Continue
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Default Welcome Message -->
                                <div id="welcome_section">
                                    <div class="card">
                                        <div class="card-body text-center py-5">
                                            <h4>Welcome to Your Application</h4>
                                            <p class="text-muted mb-4">Click on any section from the menu to start filling out your application.</p>
                                            <p><strong>Application Type:</strong> {{ $application->applicationSetting->name }}</p>
                                            <p><strong>Academic Session:</strong> {{ $application->academic_session }}</p>
                                            
                                            {{-- Progress indicator --}}
                                            <div class="mt-4">
                                                <h6>Completion Status:</h6>
                                                <div class="row">
                                                    @if(isset($modules['profile']) && $modules['profile'])
                                                        <div class="col-md-6 mb-2">
                                                            <span class="badge {{ $profile ? 'bg-success' : 'bg-secondary' }}">
                                                                Personal Profile {{ $profile ? '✓' : '' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if(isset($modules['olevel']) && $modules['olevel'])
                                                        <div class="col-md-6 mb-2">
                                                            <span class="badge {{ $olevel ? 'bg-success' : 'bg-secondary' }}">
                                                                O'Level Results {{ $olevel ? '✓' : '' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if(isset($modules['alevel']) && $modules['alevel'])
                                                        <div class="col-md-6 mb-2">
                                                            <span class="badge {{ $alevel ? 'bg-success' : 'bg-secondary' }}">
                                                                A'Level Results {{ $alevel ? '✓' : '' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if(isset($modules['course_of_study']) && $modules['course_of_study'])
                                                        <div class="col-md-6 mb-2">
                                                            <span class="badge {{ $courseOfStudy ? 'bg-success' : 'bg-secondary' }}">
                                                                Course of Study {{ $courseOfStudy ? '✓' : '' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if(isset($modules['documents']) && is_array($modules['documents']))
                                                        <div class="col-md-6 mb-2">
                                                            <span class="badge {{ $documents->count() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                                Documents {{ $documents->count() > 0 ? '✓' : '' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                {{-- Check if all required modules are completed --}}
                                                @php
                                                    $allCompleted = true;

                                                    if(isset($modules['profile']) && $modules['profile']) {
                                                        $allCompleted = $allCompleted && (!empty($profile));
                                                    }
                                                    if(isset($modules['olevel']) && $modules['olevel']) {
                                                        $allCompleted = $allCompleted && (!empty($olevel));
                                                    }
                                                    if(isset($modules['alevel']) && $modules['alevel']) {
                                                        $allCompleted = $allCompleted && (!empty($alevel));
                                                    }
                                                    if(isset($modules['course_of_study']) && $modules['course_of_study']) {
                                                        $allCompleted = $allCompleted && (!empty($courseOfStudy));
                                                    }
                                                    if(isset($modules['documents']) && is_array($modules['documents'])) {
                                                        $allCompleted = $allCompleted && ($documents->count() > 0);
                                                    }
                                                @endphp


                                                <div class="mt-4">
                                                    @if($allCompleted)
                                                        @if(!$application->submitted_by) {{-- assuming you track submission --}}
                                                            <div class="alert alert-warning text-center mb-3" role="alert">
                                                                ⚠️ Your application will only be considered <strong>completed</strong> once you click the final submission button below.
                                                            </div>
                                                            <form action="{{ route('application.handle_form_submission',$user_application_id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-lg">
                                                                    ✅ Final Submit Application
                                                                </button>
                                                            </form>
                                                        @else
                                                            <div class="alert alert-success text-center mt-3" role="alert">
                                                                🎉 Your application has been successfully submitted!
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="alert alert-info text-center mt-3" role="alert">
                                                            Please complete all required sections above before final submission.
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    <!-- Transaction History Section -->
                    @if($payment_transaction->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Transaction History</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Reference</th>
                                                <th>Payment Type</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Gateway</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payment_transaction as $index => $txn)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $txn->refernce_number }}</td>
                                                    <td class="text-capitalize">{{ $txn->payment_type }}</td>
                                                    <td>₦{{ number_format($txn->amount, 2) }}</td>
                                                    <td>
                                                        @if($txn->payment_status === '1')
                                                            <span class="badge bg-success">Successful</span>
                                                        @elseif($txn->payment_status === '0')
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @else
                                                            <span class="badge bg-danger">Failed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ ucfirst($txn->payment_method ?? 'N/A') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('d M Y h:i A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @include('applications.partials.footer')

   {{-- Fixed Payment Confirmation Modal --}}
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPaymentLabel">Confirm Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-credit-card fa-3x text-success mb-3"></i>
                        </div>
                        <h3>Payment Confirmation</h3>
                        <p class="mb-3">You are about to pay 
                            <strong class="text-success">{{ $application->applicationSetting->application_fee }}</strong> 
                            for <strong>{{ $application->applicationSetting->name }}</strong>.
                        </p>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> You will be redirected to our secure payment gateway to complete this transaction.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="theme-btn" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    {{-- Fixed form with proper action --}}
                    <form action="{{ route('application.payment.process') }}" method="post" style="display: inline;">
                        @csrf
                        <input type="hidden" name="application_id" value="{{ $application->id }}">
                        <input type="hidden" name="amount" value="{{ str_replace(['₦', ',', ' '], '', $application->applicationSetting->application_fee) }}">
                        <input type="hidden" name="fee_type" value="application">
                        <input type="hidden" name="gateway" value="oneapp">
                        <button type="submit" class="theme-btn">
                            <i class="fas fa-credit-card"></i> Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmAcceptance" tabindex="-1" aria-labelledby="confirmAcceptanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmAcceptanceLabel">Confirm Acceptance Fee Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-credit-card fa-3x text-success mb-3"></i>
                        </div>
                        <h3>Acceptance Fee Payment</h3>
                        <p class="mb-3">
                            You are about to pay 
                            <strong class="text-success">{{ $application->applicationSetting->acceptance_fee }}</strong> 
                            as your <strong>Acceptance Fee</strong>.
                        </p>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                You will be redirected to our secure payment gateway to complete this transaction.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="theme-btn" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    {{-- Payment form --}}
                    <form action="{{ route('application.payment.process') }}" method="post" style="display: inline;">
                        @csrf
                        <input type="hidden" name="application_id" value="{{ $application->id }}">
                        <input type="hidden" name="amount" value="{{ str_replace(['₦', ',', ' '], '', $application->applicationSetting->acceptance_fee) }}">
                        <input type="hidden" name="fee_type" value="acceptance">
                        <input type="hidden" name="gateway" value="oneapp">
                        <button type="submit" class="theme-btn">
                            <i class="fas fa-credit-card"></i> Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- Loading Overlay for Payment Processing --}}
    <div id="payment-loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Processing payment, please wait...</p>
        </div>
    </div>
    
    {{-- JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById('welcome_section').style.display = 'none';

            // Show selected section
            document.getElementById(sectionName + '_section').style.display = 'block';

            // Update active menu item
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');
        }
    </script>
    <script>
        const subjects = {!! json_encode(['English Language', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'Further Mathematics', 'Economics', 'Government', 'Literature']) !!};
        const grades = {!! json_encode(['A1','B2','B3','C4','C5','C6','D7','E8','F9']) !!};

        document.getElementById('add_subject_btn').addEventListener('click', function() {
            const container = document.getElementById('olevel_subjects_container');

            const row = document.createElement('div');
            row.className = 'row mb-2 olevel-row';

            const subjectCol = document.createElement('div');
            subjectCol.className = 'col-md-6';
            const subjectSelect = document.createElement('select');
            subjectSelect.className = 'form-select';
            subjectSelect.name = 'olevel_subjects[]';
            subjectSelect.required = true;
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Select Subject';
            subjectSelect.appendChild(defaultOption);
            subjects.forEach(s => {
                const option = document.createElement('option');
                option.value = s;
                option.text = s;
                subjectSelect.appendChild(option);
            });
            subjectCol.appendChild(subjectSelect);

            const gradeCol = document.createElement('div');
            gradeCol.className = 'col-md-4';
            const gradeSelect = document.createElement('select');
            gradeSelect.className = 'form-select';
            gradeSelect.name = 'olevel_grades[]';
            gradeSelect.required = true;
            const defaultGrade = document.createElement('option');
            defaultGrade.value = '';
            defaultGrade.text = 'Select Grade';
            gradeSelect.appendChild(defaultGrade);
            grades.forEach(g => {
                const option = document.createElement('option');
                option.value = g;
                option.text = g;
                gradeSelect.appendChild(option);
            });
            gradeCol.appendChild(gradeSelect);

            const removeCol = document.createElement('div');
            removeCol.className = 'col-md-2';
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-danger btn-remove';
            removeBtn.innerText = 'Remove';
            removeBtn.addEventListener('click', () => row.remove());
            removeCol.appendChild(removeBtn);

            row.appendChild(subjectCol);
            row.appendChild(gradeCol);
            row.appendChild(removeCol);

            container.appendChild(row);
        });

        // Remove button for initial row
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                btn.closest('.olevel-row').remove();
            });
        });
    </script>
</body>
</html>