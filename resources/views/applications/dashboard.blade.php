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
                    @if($applications && $applications->count() > 0)
                        <div class="row">
                            @foreach($applications as $application)
                                <div class="col-md-12 mb-3">
                                    <div class="card application-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">
                                                    <i class="fas fa-graduation-cap text-success"></i>
                                                    {{ $application->applicationSetting->academic_session }} - {{ $application->applicationSetting->name }}
                                                </h6>
                                            </div>
                                            <div>
                                                <div class="mb-2">
                                                    <small class="text-muted">Admission Status:</small>
                                                    @if($application->is_approved == 1)
                                                        <span class="badge bg-success"><i class="fas fa-check"></i> Admitted</span>
                                                    @elseif($application->is_approved == 0)
                                                        <span class="badge bg-warning"><i class="fas fa-clock"></i> Application Not Submitted </span>
                                                    @elseif($application->is_approved == 2)
                                                        <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending </span>
                                                    @endif
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Started:</small><br>
                                                    <strong>{{ $application->created_at->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    @if($application->submitted_by)
                                                        <div class="mt-2">
                                                            <small class="text-muted">Submitted:</small><br>
                                                            <strong>{{ \Carbon\Carbon::parse($application->submitted_by)->format('M d, Y \a\t g:i A') }}</strong>
                                                        </div>
                                                    @else
                                                        <div class="mt-2">
                                                            <small class="text-muted">Submitted:</small><br>
                                                            <strong  class="badge bg-warning"> Not Submitted </strong>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <!-- Payment Info -->
                                                    <div class="mb-2">
                                                        <small class="text-muted">Application Fee:</small><br>
                                                        <strong>{{ $application->applicationSetting->application_fee ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">Payment Status:</small><br>
                                                        @if($application->transactions->where('payment_type', 'application')->where('payment_status', 1)->first())
                                                            <span class="badge bg-success"><i class="fas fa-check"></i> Paid</span>
                                                        @elseif($application->transactions->where('payment_type', 'application')->where('payment_status', 0)->first())
                                                            <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>
                                                        @else
                                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Not Paid</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <!-- Payment Info -->
                                                    <div class="mb-2">
                                                        <small class="text-muted">Acceptance Fee:</small><br>
                                                        <strong>{{ $application->applicationSetting->acceptance_fee ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">Payment Status:</small><br>
                                                        @if($application->transactions->where('payment_type', 'acceptance')->where('payment_status', 1)->first())
                                                            <span class="badge bg-success"><i class="fas fa-check"></i> Paid</span>
                                                        @elseif($application->transactions->where('payment_type', 'acceptance')->where('payment_status', 0)->first())
                                                            <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>
                                                        @else
                                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Not Paid</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-light">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <a href="{{ route('application.form', ['user_application_id' => $application->id ]) }}" class="btn btn-outline-info" text-white>
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="team-details-info application-form">
                        <form action="{{ route('application.start') }}" method="POST">
                            @csrf
                            
                            <!-- Application Selection -->
                            <div class="mb-4">
                                <center>
                                    <h4 class="mb-3">Start Your Application</h4>
                                    <p class="text-muted">Select an application round below to begin</p>
                                    <hr/>
                                </center>
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <label for="application_setting" class="form-label">
                                            <strong>Select Application Round</strong>
                                        </label>
                                        <select name="application_setting_id" id="application_setting" class="form-select form-select-lg" required>
                                            <option value="">Choose Application Round</option>
                                            @foreach($applicationSettings as $setting)
                                                <option value="{{ $setting->id }}"
                                                    data-description="{{ $setting->description }}"
                                                    data-fee="{{ $setting->application_fee }}"
                                                    data-acceptance="{{ $setting->acceptance_fee }}"
                                                    data-modules="{{ $setting->modules_enable }}">
                                                    {{ $setting->academic_session }} - {{ $setting->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="academic_session" value="{{ $setting->academic_session }}">
                                    </div>
                                </div>
                                
                                <!-- Save Button (Always Visible) -->
                                <div class="text-center mt-2">
                                    <button type="submit" class="theme-btn w-10" id="save_button" disabled>
                                        <i class="fas fa-sign-in-alt"></i> Save & Continue
                                    </button>
                                </div>
                            </div>

                            <!-- Instructions Section (Hidden by default) -->
                            <div id="application_instructions" class="mb-4" style="display: none;">
                                <div class="alert alert-info text-center">
                                    <h5><i class="fas fa-info-circle"></i> Application Details</h5>
                                </div>
                                
                                <div class="row">
                                    <!-- Left Column - Requirements -->
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-clipboard-list"></i> What You Need to Complete</h6>
                                            </div>
                                            <div class="card-body">
                                                <!-- Description -->
                                                <div class="mb-3">
                                                    <h6>About This Application:</h6>
                                                    <p id="instructions_text" class="text-muted"></p>
                                                </div>
                                                
                                                <!-- Modules -->
                                                <div id="modules_section" style="display: none;">
                                                    <h6>Application Steps:</h6>
                                                    <div id="modules_list" class="mb-3"></div>
                                                </div>

                                                <!-- Documents -->
                                                <div id="documents_section" style="display: none;">
                                                    <h6>Documents to Upload:</h6>
                                                    <div class="alert alert-warning">
                                                        <small><strong>Important:</strong> All documents must be PDF files, max 2MB each</small>
                                                    </div>
                                                    <ul id="documents_list"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Column - Fees -->
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Fees</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Application Fee:</span>
                                                    <strong id="application_fee">--</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Acceptance Fee:</span>
                                                    <strong id="acceptance_fee">--</strong>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>Total:</strong></span>
                                                    <strong id="total_fees" class="text-success">--</strong>
                                                </div>
                                                <div class="alert alert-danger mt-3 p-2">
                                                    <small><i class="fas fa-exclamation-triangle"></i> All fees are non-refundable</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                    stepDiv.innerHTML = `<i class="fas fa-circle text-primary me-2"></i> ${moduleNames[key] || key.replace(/_/g, ' ').toUpperCase()}`;
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
