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
                {{-- Applications List --}}
                <div class="mt-5"></div>
                @if ($applications && $applications->count() > 0)
                    <div class="row">
                        @foreach ($applications as $application)
                            <div class="col-md-12 mb-4">
                                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                                    {{-- Header --}}
                                    <div
                                        class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <h5 class="mb-1 text-success">
                                                <i class="fas fa-graduation-cap me-2 text-success"></i>
                                                {{ $application->applicationSetting->academic_session }}
                                                <span class="text-muted">–
                                                    {{ $application->applicationSetting->name }}</span>
                                            </h5>
                                        </div>

                                        <div class="text-end">
                                            <small class="text-muted d-block">Admission Status</small>
                                            @switch($application->is_approved)
                                                @case(1)
                                                    <span class="badge bg-success px-3 py-2">
                                                        <i class="fas fa-check-circle me-1"></i> Admitted
                                                    </span>
                                                @break

                                                @case(0)
                                                    @if ($application->submitted_by)
                                                        <span class="badge bg-warning text-dark px-3 py-2">
                                                            <i class="fas fa-clock me-1"></i> Pending
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary px-3 py-2">
                                                            <i class="fas fa-edit me-1"></i> Not Submitted
                                                        </span>
                                                    @endif
                                                @break

                                                @case(2)
                                                    <span class="badge bg-info px-3 py-2">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> In Progress
                                                    </span>
                                                @break
                                            @endswitch
                                        </div>
                                    </div>

                                    {{-- Body --}}
                                    <div class="card-body bg-white">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <div class="border rounded p-3 h-100">
                                                    <small class="text-muted d-block mb-1">Started</small>
                                                    <strong>{{ $application->created_at->format('M d, Y') }}</strong>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="border rounded p-3 h-100">
                                                    <small class="text-muted d-block mb-1">Submitted</small>
                                                    @if ($application->submitted_by)
                                                        <strong>{{ \Carbon\Carbon::parse($application->submitted_by)->format('M d, Y \a\t g:i A') }}</strong>
                                                    @else
                                                        <span class="badge bg-secondary">Not Submitted</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="border rounded p-3 h-100">
                                                    <small class="text-muted d-block mb-1">Application Fee</small>
                                                    <strong>{{ $application->applicationSetting->application_fee ?? 'N/A' }}</strong>
                                                    <hr class="my-2">
                                                    @php
                                                        $appPayment = $application->transactions
                                                            ->where('payment_type', 'application')
                                                            ->where('payment_status', '1')
                                                            ->first();
                                                    @endphp
                                                    <small class="text-muted d-block mb-1">Status</small>
                                                    @if ($appPayment && $appPayment->payment_status == 1)
                                                        <span class="badge bg-success">Paid</span>
                                                    @elseif($appPayment && $appPayment->payment_status == 0)
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger">Not Paid</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="border rounded p-3 h-100">
                                                    <small class="text-muted d-block mb-1">Acceptance Fee</small>
                                                    <strong>{{ $application->applicationSetting->acceptance_fee ?? 'N/A' }}</strong>
                                                    <hr class="my-2">
                                                    @php
                                                        $acceptPayment = $application->transactions
                                                            ->where('payment_type', 'acceptance')
                                                            ->where('payment_status', '1')
                                                            ->first();
                                                    @endphp
                                                    <small class="text-muted d-block mb-1">Status</small>
                                                    @if ($acceptPayment && $acceptPayment->payment_status == 1)
                                                        <span class="badge bg-success">Paid</span>

                                                        {{-- Tuition portal info --}}
                                                        <div class="alert alert-info mt-3 mb-0 p-2 small">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Acceptance complete.
                                                            <a href="{{ route('student.login') }}" class="alert-link">
                                                                Log in to the Student Portal
                                                            </a>
                                                            to pay your tuition fee.
                                                        </div>
                                                    @elseif($acceptPayment && $acceptPayment->payment_status == 0)
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @else
                                                        <span class="badge bg-danger">Not Paid</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <div
                                        class="card-footer bg-light d-flex justify-content-between align-items-center py-3">
                                        <a href="{{ route('application.form', ['user_application_id' => $application->id]) }}"
                                            class="theme-btn">
                                            <i class="fas fa-arrow-right me-1"></i> Continue Your Application
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle me-2"></i> No applications found yet.
                    </div>
                @endif




                <div class="team-details-info application-form">
                    <form action="{{ route('application.start') }}" method="POST">
                        @csrf

                        <!-- HEADER SECTION -->
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Start Your Application</h3>
                            <p class="text-muted">Choose the application round that applies to you. You’ll see all the
                                details before continuing.</p>
                            <hr />
                        </div>

                        <!-- APPLICATION SELECTION -->
                        <div class="mb-4">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <label for="application_setting" class="form-label">
                                        <strong>Select Application Round</strong>
                                    </label>
                                    <select name="application_setting_id" id="application_setting"
                                        class="form-select form-select-lg" required>
                                        <option value="">-- Please select an option --</option>
                                        @foreach ($applicationSettings as $setting)
                                            <option value="{{ $setting->id }}"
                                                data-description="{{ $setting->description }}"
                                                data-fee="{{ $setting->application_fee }}"
                                                data-acceptance="{{ $setting->acceptance_fee }}"
                                                data-modules="{{ $setting->modules_enable }}">
                                                {{ $setting->academic_session }} – {{ $setting->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="academic_session"
                                        value="{{ $setting->academic_session }}">
                                    <small class="text-muted d-block mt-2" id="application_hint" style="display:none;">
                                        Great — we’ll load the details below.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- LOADING INDICATOR -->
                        <div id="loading_details" class="text-center text-muted" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading details...
                        </div>

                        <!-- DETAILS SECTION -->
                        <div id="application_instructions" style="display:none;">
                            <div class="alert alert-info text-center mt-4">
                                <h5><i class="fas fa-info-circle"></i> Application Details</h5>
                            </div>

                            <div class="row">
                                <!-- LEFT COLUMN -->
                                <div class="col-md-8 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="fas fa-clipboard-list"></i> What You’ll Need
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Description -->
                                            <div class="mb-3">
                                                <h6>About This Application</h6>
                                                <p id="instructions_text" class="text-muted"></p>
                                            </div>

                                            <!-- Modules -->
                                            <div id="modules_section" style="display:none;">
                                                <h6>Application Steps</h6>
                                                <div id="modules_list" class="mb-3"></div>
                                            </div>

                                            <!-- Documents -->
                                            <div id="documents_section" style="display:none;">
                                                <h6>Required Documents</h6>
                                                <div class="alert alert-warning p-2">
                                                    <small><strong>Note:</strong> Upload PDF files only (max 2MB
                                                        each).</small>
                                                </div>
                                                <ul id="documents_list" class="ps-3"></ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT COLUMN -->
                                <div class="col-md-4 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Fees Overview
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>ApplicationFee:</span> <strong id="application_fee">--</strong>
                                            </div>
                                            {{-- <div class="d-flex justify-content-between mb-2" style="display:none;"> 
                                                <span>Acceptance Fee:</span> <strong id="acceptance_fee">--</strong> 
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Total:</strong></span> <strong id="total_fees"
                                                    class="text-success">--</strong>
                                            </div> --}}
                                            <div class="alert alert-danger mt-3 p-2">
                                                <small><i class="fas fa-exclamation-triangle"></i> Please note: all
                                                    fees are non-refundable.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="text-center mt-4">
                            <button type="submit" class="theme-btn w-10" id="save_button" disabled>
                                <i class="fas fa-arrow-right"></i> Continue to Application
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help Section -->
                <div class="row mt-5">
                </div>
            </div>
        </div>
        <!-- team single end -->
    </main>

    @include('applications.partials.footer')

    <!-- scroll-top -->
    <a href="index-2.html#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->

    <!-- js -->
    @include('applications.partials.js')
    <script>
        document.getElementById('application_setting').addEventListener('change', function() {
            const selected = this.value !== "";
            const hint = document.getElementById('application_hint');
            const loading = document.getElementById('loading_details');
            const details = document.getElementById('application_instructions');
            const button = document.getElementById('save_button');

            hint.style.display = selected ? 'block' : 'none';
            button.disabled = !selected;

            if (selected) {
                details.style.display = 'none';
                loading.style.display = 'block';
                setTimeout(() => {
                    loading.style.display = 'none';
                    details.style.display = 'block';
                }, 800); // adds a little anticipation, like it's fetching info
            } else {
                details.style.display = 'none';
            }
        });
    </script>
    <script>
        const select = document.getElementById('application_setting');
        const instructionsSection = document.getElementById('application_instructions');
        const instructionsText = document.getElementById('instructions_text');
        const modulesSection = document.getElementById('modules_section');
        const modulesList = document.getElementById('modules_list');
        const documentsSection = document.getElementById('documents_section');
        const documentsList = document.getElementById('documents_list');
        const applicationFee = document.getElementById('application_fee');
        const acceptanceFee = document.getElementById('acceptance_fee');
        const totalFees = document.getElementById('total_fees');
        const saveButton = document.getElementById('save_button');

        // Pass application settings from Blade to JS
        const settings = @json($applicationSettings);

        select.addEventListener('change', function() {
            const selectedId = this.value;

            if (!selectedId) {
                // Hide instructions and disable button
                instructionsSection.style.display = 'none';
                saveButton.disabled = true;
                return;
            }

            // Find the selected setting
            const setting = settings.find(s => s.id == selectedId);
            if (!setting) return;

            // Show instructions section
            instructionsSection.style.display = 'block';
            saveButton.disabled = false;

            // Show simple description
            instructionsText.textContent = setting.description;

            // Show modules in simple format
            const modules = JSON.parse(setting.modules_enable);
            modulesList.innerHTML = '';
            modulesSection.style.display = 'none';

            let hasModules = false;
            const moduleNames = {
                'personal_info': 'Personal Information',
                'academic_records': 'Academic Records',
                'medical_exam': 'Medical Exam',
                'interview': 'Interview',
                'references': 'References',
                'entrance_exam': 'Entrance Exam'
            };

            for (const [key, value] of Object.entries(modules)) {
                if (key === 'documents') continue;

                if (value) {
                    hasModules = true;
                    modulesSection.style.display = 'block';

                    const stepDiv = document.createElement('div');
                    stepDiv.className = 'mb-2';
                    stepDiv.innerHTML =
                        `<i class="fas fa-circle text-primary me-2"></i> ${moduleNames[key] || key.replace(/_/g, ' ').toUpperCase()}`;
                    modulesList.appendChild(stepDiv);
                }
            }

            // Show documents in simple format
            if (modules.documents && modules.documents.length) {
                documentsSection.style.display = 'block';
                documentsList.innerHTML = '';

                modules.documents.forEach(doc => {
                    const li = document.createElement('li');
                    li.innerHTML = `<i class="fas fa-file-pdf text-danger me-2"></i> ${doc}`;
                    documentsList.appendChild(li);
                });
            } else {
                documentsSection.style.display = 'none';
            }

            // Show fees
            applicationFee.textContent = setting.application_fee || '₦0';
            acceptanceFee.textContent = setting.acceptance_fee || '₦0';

            // Calculate total - handle decimal numbers properly
            const appFeeNum = parseFloat((setting.application_fee || '0').replace(/[^\d.]/g, '')) || 0;
            const accFeeNum = parseFloat((setting.acceptance_fee || '0').replace(/[^\d.]/g, '')) || 0;
            const total = appFeeNum + accFeeNum;

            totalFees.textContent = total > 0 ? `₦${total.toLocaleString()}` : '₦0';

            // Smooth scroll to instructions
            instructionsSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!select.value) {
                e.preventDefault();
                alert('Please select an application round first.');
                select.focus();
                return false;
            }

            // Show loading state
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveButton.disabled = true;
        });
    </script>
</body>

</html>
