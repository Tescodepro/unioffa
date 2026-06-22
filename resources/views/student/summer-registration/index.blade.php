@extends('layouts.app')

@section('title', 'Summer Registration')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('student.partials.header')
        @include('student.partials.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="container mx-auto p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Summer Registration</h1>
        <p class="text-gray-600 mt-2">Register for summer courses. The summer application fee is ₦30,000, and each course costs ₦20,000.</p>
    </div>

    @include('layouts.flash-message')

    @if($summerRegistration && in_array($summerRegistration->status, ['pending_vc_approval', 'pending_payment', 'registered']))
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Current Registration Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium text-lg">
                        @if($summerRegistration->status == 'pending_vc_approval')
                            <span class="text-yellow-600">Pending VC Approval</span>
                        @elseif($summerRegistration->status == 'pending_payment')
                            <span class="text-blue-600">Pending Payment</span>
                        @elseif($summerRegistration->status == 'registered')
                            <span class="text-green-600">Registered</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Courses</p>
                    <p class="font-medium text-lg">{{ is_array($summerRegistration->courses) ? count($summerRegistration->courses) : 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Fee</p>
                    <p class="font-medium text-lg">₦{{ number_format($summerRegistration->total_fee, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Status</p>
                    <p class="font-medium text-lg capitalize {{ $summerRegistration->payment_status == 'paid' ? 'text-green-600' : 'text-red-600' }}">{{ $summerRegistration->payment_status }}</p>
                </div>
            </div>

            @if($summerRegistration->status == 'pending_payment' && $summerRegistration->payment_status == 'pending')
                <div class="mt-6">
                    <a href="{{ route('student.summer.payment', $summerRegistration->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Proceed to Payment
                    </a>
                </div>
            @endif
        </div>

        @if($summerRegistration->status == 'registered' && !empty($registeredCourses))
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Registered Summer Courses</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Code</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($registeredCourses as $reg)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $reg->course_code }}</td>
                                <td class="px-6 py-4">{{ $reg->course_title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $reg->course_unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @else

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <form action="{{ route('student.summer.store') }}" method="POST" id="summerRegistrationForm">
                @csrf
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Select Courses</h2>
                    
                    <div class="mb-4 bg-blue-50 p-4 rounded-md border border-blue-200">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> You can select up to 6 courses. If you select more than 6, your request will be sent to the Vice Chancellor for approval before you can make payment.
                        </p>
                    </div>

                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-md">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 shadow-sm">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                        Select
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course Code
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Units
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($courses as $course)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="courses[]" value="{{ $course->id }}" class="course-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $course->course_code }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $course->course_title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $course->course_unit }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 hidden" id="reasonContainer">
                        <label for="reason_for_increase" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for more than 6 courses <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason_for_increase" id="reason_for_increase" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2" placeholder="Please provide a reason for requesting more than 6 courses..."></textarea>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Selected Courses: <span id="courseCount" class="font-bold text-gray-900">0</span></p>
                                <p class="text-sm text-gray-600">Estimated Total: <span id="estimatedTotal" class="font-bold text-gray-900">₦30,000.00</span></p>
                            </div>
                            <button type="submit" id="submitBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50" disabled>
                                Proceed
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    @endif
</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            const courseCountSpan = document.getElementById('courseCount');
            const estimatedTotalSpan = document.getElementById('estimatedTotal');
            const reasonContainer = document.getElementById('reasonContainer');
            const reasonTextarea = document.getElementById('reason_for_increase');
            const submitBtn = document.getElementById('submitBtn');

            const summaryFee = 30000;
            const perCourseFee = 20000;

            function updateSummary() {
                let count = 0;
                checkboxes.forEach(cb => {
                    if(cb.checked) count++;
                });

                courseCountSpan.textContent = count;
                
                if(count > 0) {
                    const total = summaryFee + (count * perCourseFee);
                    estimatedTotalSpan.textContent = '₦' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
                    submitBtn.disabled = false;
                } else {
                    estimatedTotalSpan.textContent = '₦' + summaryFee.toLocaleString('en-US', {minimumFractionDigits: 2});
                    submitBtn.disabled = true;
                }

                if(count > 6) {
                    reasonContainer.classList.remove('hidden');
                    reasonTextarea.required = true;
                    submitBtn.textContent = 'Submit for VC Approval';
                } else {
                    reasonContainer.classList.add('hidden');
                    reasonTextarea.required = false;
                    submitBtn.textContent = 'Proceed to Payment';
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSummary);
            });
            
            updateSummary();
        });
    </script>
@endpush
