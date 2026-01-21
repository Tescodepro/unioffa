@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="main-wrapper">
    @include('staff.layouts.header')
    @include('staff.layouts.sidebar')

    <div class="page-wrapper">
        <div class="content">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Edit Student - {{ $student->matric_no }}</h4>
                <a href="{{ route('ict.students.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>

            @include('layouts.flash-message')

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ict.students.update', $student->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" 
                                    value="{{ old('first_name', $student->user->first_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" 
                                    value="{{ old('last_name', $student->user->last_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                    value="{{ old('email', $student->user->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" 
                                    value="{{ old('phone', $student->user->phone) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Matric Number</label>
                                <input type="text" name="matric_no" class="form-control" 
                                    value="{{ old('matric_no', $student->matric_no) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select" required>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" 
                                            {{ $dept->id == $student->department_id ? 'selected' : '' }}>
                                            {{ $dept->department_name }} ({{ $dept->faculty->faculty_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Level</label>
                                <select name="level" class="form-select" required>
                                    @foreach([100,200,300,400,500] as $lvl)
                                        <option value="{{ $lvl }}" {{ $lvl == $student->level ? 'selected' : '' }}>
                                            {{ $lvl }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="sex" class="form-select" required>
                                    <option value="male" {{ $student->sex == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $student->sex == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Entry Mode</label>
                                <input type="text" name="entry_mode" class="form-control" 
                                    value="{{ old('entry_mode', $student->entry_mode) }}" required>
                                <select name="entry_mode" class="form-select" required>
                                        <option value="">Select Entry Mode</option>
                                        @foreach (['TOPUP', 'IDELUTME', 'IDELDE', 'UTME', 'TRANSFER', 'DIPLOMA','DE'] as $mode)
                                            <option value="{{ $mode }}"
                                                {{ old('entry_mode') == $mode ? 'selected' : '' }}>{{ $mode }}
                                            </option>
                                        @endforeach
                                    </select>
                            </div>
                            

                            <div class="col-md-6">
                                <label class="form-label">Admission Year</label>
                                <input type="text" name="admission_year" class="form-control"
                                    value="{{ old('admission_year', $student->admission_session) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date of Birth (dd/mm/yyyy)</label>
                                <input type="date" name="dob" class="form-control"
                                    value="{{ old('dob', $student->user->date_of_birth) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Programme</label>
                                {{-- <input type="text" name="programme" class="form-control"
                                    value="{{ old('programme', $student->programme) }}"> --}}
                                <select name="programme" class="form-select" required>
                                        <option value="">Select Entry Mode</option>
                                        @foreach (['TOPUP', 'IDELUTME', 'IDELDE', 'REGULAR'] as $mode)
                                            <option value="{{ $mode }}"
                                                {{ old('programme') == $mode ? 'selected' : '' }}>{{ $mode }}
                                            </option>
                                        @endforeach
                                    </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stream</label>
                                <input type="text" name="stream" class="form-control"
                                    value="{{ old('stream', $student->stream) }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check"></i> Update Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
