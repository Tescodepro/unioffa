<!DOCTYPE html>
<html lang="en">

@include('website.partials.head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<body class="">

    <!-- header area -->
    @include('website.partials.menu')
    <!-- header area end -->

    <main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb" style="background: url(assets/img/breadcrumb/01.jpg)">
            <div class="container">
                <h2 class="breadcrumb-title">Agent Application Form</h2>
                <ul class="breadcrumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="active">Agent Application Form</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->

        <!-- introduction section -->
        <section class="affiliate-intro py-80 mt-5">
            <div class="container">
                <div class="intro-text">
                    <div class="row">
                        <div class="col-md-4">
                            <h2>Affiliate (Agent) Programme</h2>
                            <p class="mt-3">
                                Welcome to our Affiliate Programme — a simple and rewarding way to earn while helping
                                new students join us.
                                Once your application is approved, you’ll receive your unique <strong>Referrer
                                    Code</strong>.
                            </p>
                            <p class="mt-3">
                                For every applicant who uses your code and completes their registration by paying the
                                acceptance fee,
                                you’ll earn <strong>₦20,000</strong>.
                                Payments are sent directly to the <strong>bank account</strong> you provide below,
                                so please ensure your account details are valid and correct.
                            </p>
                            <p class="mt-3">
                                Each time someone uses your referral code, you’ll receive an email notification.
                                From there, simply follow up with the applicant to encourage them to complete their
                                application
                                and pay their acceptance fee — once that’s done, your reward will be processed.
                            </p>
                            <h4>How to Apply</h4>
                            <ol class="text-start mx-auto" style="max-width: 700px;">
                                <li>Fill out the form below with your correct personal and bank details.</li>
                                <li>Agree to the Terms and Conditions before submitting.</li>
                                <li>Wait for your application to be reviewed and approved.</li>
                                <li>Once approved, your <strong>unique referrer code</strong> will be sent to your
                                    email.</li>
                                <li>Share your code with prospective students and earn ₦20,000 for every successful
                                    referral.
                                </li>
                            </ol>
                        </div>
                        <div class="col-md-8">
                            <!-- application -->
                            <div class="application">
                                <div class="contaier">
                                    <div class="application-form">
                                        <h3>Agent Application Form</h3>
                                        @include('layouts.flash-message')
                                        <form action="{{ route('agent.application.submit') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <h5 class="mb-3">Personal Information</h5>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>First Name <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('first_name') is-invalid @enderror"
                                                            name="first_name" placeholder="Enter First Name"
                                                            value="{{ old('first_name') }}" required>
                                                        @error('first_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Middle Name <span
                                                                class="text-muted">(Optional)</span></label>
                                                        <input type="text"
                                                            class="form-control @error('middle_name') is-invalid @enderror"
                                                            name="middle_name" placeholder="Enter Middle Name"
                                                            value="{{ old('middle_name') }}">
                                                        @error('middle_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Last Name <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('last_name') is-invalid @enderror"
                                                            name="last_name" placeholder="Enter Last Name"
                                                            value="{{ old('last_name') }}" required>
                                                        @error('last_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Email Address <span class="text-danger">*</span></label>
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email" placeholder="Enter Email Address"
                                                            value="{{ old('email') }}" required>
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Phone Number <span class="text-danger">*</span></label>
                                                        <input type="tel"
                                                            class="form-control @error('phone') is-invalid @enderror"
                                                            name="phone" placeholder="Enter Phone Number"
                                                            value="{{ old('phone') }}" required>
                                                        @error('phone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <h5 class="mt-4 mb-3">Location Information</h5>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>State <span class="text-danger">*</span></label>
                                                        <select class="form-select @error('state') is-invalid @enderror"
                                                            name="state" id="state" required>
                                                            <option value="">Select State</option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->id }}"
                                                                    {{ old('state') == $state->id ? 'selected' : '' }}>
                                                                    {{ $state->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('state')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Local Government Area <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select @error('lga') is-invalid @enderror"
                                                            name="lga" id="lga" required>
                                                            <option value="">Select LGA</option>
                                                            @if (old('state'))
                                                                @foreach ($lgas->where('state_id', old('state')) as $lga)
                                                                    <option value="{{ $lga->id }}"
                                                                        {{ old('lga') == $lga->id ? 'selected' : '' }}>
                                                                        {{ $lga->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('lga')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <h5 class="mt-4 mb-3">Bank Account Information</h5>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Bank Name <span class="text-danger">*</span></label>
                                                        <select
                                                            class="form-select @error('bank_name') is-invalid @enderror select2-bank"
                                                            name="bank_name" required>
                                                            <option value="">Select Bank</option>
                                                            <!-- (existing bank list remains unchanged) -->
                                                            @include('website.partials.bank-options')
                                                        </select>
                                                        @error('bank_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Account Number <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('account_number') is-invalid @enderror"
                                                            name="account_number" placeholder="Enter Account Number"
                                                            value="{{ old('account_number') }}" maxlength="10"
                                                            required>
                                                        @error('account_number')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Account Name <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('account_name') is-invalid @enderror"
                                                            name="account_name" placeholder="Enter Account Name"
                                                            value="{{ old('account_name') }}" required>
                                                        @error('account_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input @error('agree') is-invalid @enderror"
                                                            type="checkbox" id="agree" name="agree"
                                                            value="1" {{ old('agree') ? 'checked' : '' }}
                                                            required>
                                                        <label class="form-check-label" for="agree">
                                                            I agree to the <a href="#">Terms & Conditions</a> and
                                                            <a href="#">Privacy Policy</a>. <span
                                                                class="text-danger">*</span>
                                                        </label>
                                                        @error('agree')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <button type="submit" class="theme-btn">
                                                        Submit Application <i class="fas fa-arrow-right-long"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- application end-->
                        </div>
                    </div>
                </div>
        </section>
        <!-- introduction section end -->



    </main>

    <!-- footer area -->
    @include('website.partials.footer')
    <!-- footer area end -->

    <!-- scroll-top -->
    <a href="#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->

    <!-- js -->
    @include('website.partials.js')

    <script>
        $(document).ready(function() {
            // State to LGA dynamic load
            $('#state').change(function() {
                var stateId = $(this).val();
                var lgaSelect = $('#lga');
                lgaSelect.empty().append('<option value="">Select LGA</option>');
                if (stateId) {
                    $.get('{{ route('lgas.by.state', ':id') }}'.replace(':id', stateId), function(data) {
                        $.each(data, function(index, lga) {
                            lgaSelect.append('<option value="' + lga.id + '">' + lga.name +
                                '</option>');
                        });
                    }).fail(function() {
                        alert('Error loading LGAs. Please try again.');
                    });
                }
            });

            // Initialize Select2 for bank dropdown
            $('.select2-bank').select2({
                placeholder: "Search and select a bank",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5'
            });

            @if ($errors->any())
                $('.select2-bank').trigger('change.select2');
            @endif
        });
    </script>

</body>

</html>
