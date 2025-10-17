@extends('layouts.app')
@section('title', 'Add Student')

@section('content')
    <div id="global-loader">
        <div class="page-loader"></div>
    </div>

    <div class="main-wrapper">
        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="page-title mb-1">Add New Student</h3>
                        <p class="text-muted mb-0">Fill out the details below to create a new student record</p>
                    </div>
                    <a href="{{ route('ict.students.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back
                    </a>
                </div>
                @include('layouts.flash-message')
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('ict.students.store') }}">
                            @csrf
                            <div class="row g-3">
                                {{-- First Name --}}
                                <div class="col-md-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                                        class="form-control" required>
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                                        class="form-control" required>
                                </div>

                                {{-- Middle Name --}}
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                        class="form-control" >
                                </div>

                                {{-- Email --}}
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                        required>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control"
                                        required>
                                </div>

                                {{-- Department --}}
                                <div class="col-md-4">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select" required>
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->department_name }} ({{ $dept->faculty->faculty_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Level --}}
                                <div class="col-md-4">
                                    <label class="form-label">Level</label>
                                    <select name="level" class="form-select" required>
                                        <option value="">Select Level</option>
                                        @foreach ([100, 200, 300, 400, 500] as $lvl)
                                            <option value="{{ $lvl }}"
                                                {{ old('level') == $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Gender --}}
                                <div class="col-md-4">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>

                                {{-- Admission Year --}}
                                <div class="col-md-4">
                                    <label class="form-label">Admission Year (e.g. 2023/2024)</label>
                                    <select name="admission_year" class="form-select" required>
                                        <option value="">Select Academic Year</option>
                                        @php
                                            $startYear = 2019;
                                            $currentYear = date('Y');
                                        @endphp
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                            @php
                                                $nextYear = $year + 1;
                                                $academicYear = "$year/$nextYear";
                                            @endphp
                                            <option value="{{ $academicYear }}"
                                                {{ old('admission_year') == $academicYear ? 'selected' : '' }}>
                                                {{ $academicYear }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>


                                {{-- Entry Mode --}}
                                <div class="col-md-4">
                                    <label class="form-label">Entry Mode</label>
                                    <select name="entry_mode" class="form-select" required>
                                        <option value="">Select Entry Mode</option>
                                        @foreach (['TOPUP', 'IDELUTME', 'IDELDE', 'UTME', 'TRANSFER', 'DIPLOMA','DE'] as $mode)
                                            <option value="{{ $mode }}"
                                                {{ old('entry_mode') == $mode ? 'selected' : '' }}>{{ $mode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Date of Birth --}}
                                <div class="col-md-4">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date"  name="dob" value="{{ old('dob') }}" class="form-control"
                                        placeholder="e.g. 12/03/2002" required>
                                </div>

                                {{-- Stream --}}
                                <div class="col-md-4">
                                    <label class="form-label">Stream (Optional)</label>
                                    <input type="number" name="stream" value="{{ old('stream') }}" class="form-control">
                                </div>

                                {{-- Campus ID --}}
                                <div class="col-md-4">
                                    <label class="form-label">Campus</label>
                                    <select name="campus_id" class="form-select" required>
                                        <option value="">Select Campus</option>
                                        @foreach ($campuses as $campus)
                                            <option value="{{ $campus->id }}"
                                                {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="ti ti-plus"></i> Add Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
