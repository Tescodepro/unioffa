@extends('layouts.app')

@section('title', 'Summer Registration Payment')

@section('content')
<div class="container mx-auto p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Summer Registration Payment</h1>
        <p class="text-gray-600 mt-2">Complete your payment to finalize summer registration.</p>
    </div>

    @include('components.alert')

    <div class="bg-white rounded-lg shadow max-w-2xl mx-auto overflow-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <i class="fas fa-credit-card fa-3x text-blue-600 mb-4"></i>
                <h2 class="text-xl font-semibold">Payment Details</h2>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-md mb-6">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Base Registration Fee:</span>
                    <span class="font-medium">₦30,000.00</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Course Registration Fee ({{ count($summerRegistration->courses) }} courses):</span>
                    <span class="font-medium">₦{{ number_format(count($summerRegistration->courses) * 20000, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 mt-2">
                    <span class="text-lg font-bold text-gray-800">Total Amount:</span>
                    <span class="text-lg font-bold text-blue-600">₦{{ number_format($summerRegistration->total_fee, 2) }}</span>
                </div>
            </div>

            <div class="text-center">
                <form action="{{ route('student.summer.payment.simulate', $summerRegistration->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out text-lg">
                        <i class="fas fa-lock mr-2"></i> Pay ₦{{ number_format($summerRegistration->total_fee, 2) }} Now
                    </button>
                    <p class="text-sm text-gray-500 mt-4">
                        <i class="fas fa-info-circle"></i> For demonstration purposes, this will simulate a successful payment and register you for the selected courses.
                    </p>
                </form>
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('student.summer.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Registration
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
