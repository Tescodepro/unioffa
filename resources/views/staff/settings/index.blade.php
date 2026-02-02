@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
    <div class="main-wrapper">

        @include('staff.layouts.header')
        @include('staff.layouts.sidebar')

        <div class="page-wrapper">
            <div class="content">
                <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
                    <div class="my-auto mb-2">
                        <h3 class="page-title mb-1">System Configuration</h3>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <ul class="nav nav-tabs card-header-tabs" id="settingsTab" role="tablist">
                            @foreach($settings as $group => $items)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $group }}-tab"
                                        data-toggle="tab" href="#{{ $group }}" role="tab">
                                        {{ ucfirst($group) }}
                                    </a>
                                </li>
                            @endforeach
                            <li class="nav-item">
                                <a class="nav-link" id="grading-tab" data-toggle="tab" href="#grading" role="tab">Grading
                                    System</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="settingsTabContent">

                            {{-- System Settings Tabs --}}
                            @foreach($settings as $group => $items)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $group }}"
                                    role="tabpanel">
                                    <form action="{{ route('ict.system_settings.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <h5 class="mb-3">{{ ucfirst($group) }} Settings</h5>

                                        @foreach($items as $setting)
                                            <div class="form-group row mb-3">
                                                <label
                                                    class="col-sm-3 col-form-label font-weight-bold">{{ $setting->description ?? str_replace('_', ' ', $setting->key) }}</label>
                                                <div class="col-sm-9">
                                                    @if($setting->type == 'file')
                                                        @if($setting->value)
                                                            <div class="mb-2">
                                                                <img src="{{ asset($setting->value) }}" alt="Current Image"
                                                                    style="max-height: 50px;" class="img-thumbnail">
                                                            </div>
                                                        @endif
                                                        <input type="file" name="{{ $setting->key }}" class="form-control-file">
                                                        <small class="text-muted d-block">Upload new file to replace current
                                                            one.</small>
                                                    @elseif($setting->type == 'boolean')
                                                        <select name="{{ $setting->key }}" class="form-control">
                                                            <option value="1" {{ $setting->value ? 'selected' : '' }}>Enabled</option>
                                                            <option value="0" {{ !$setting->value ? 'selected' : '' }}>Disabled</option>
                                                        </select>
                                                    @else
                                                        <input type="text" name="{{ $setting->key }}" class="form-control"
                                                            value="{{ $setting->value }}">
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="text-right mt-3">
                                            <button type="submit" class="btn btn-primary">Save {{ ucfirst($group) }}
                                                Settings</button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach

                            {{-- Grading System Tab --}}
                            <div class="tab-pane fade" id="grading" role="tabpanel">
                                <form action="{{ route('ict.system_settings.grading.update') }}" method="POST">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="gradingTable">
                                            <thead>
                                                <tr>
                                                    <th>Grade Config</th>
                                                    <th>Min Score</th>
                                                    <th>Max Score</th>
                                                    <th>Points (GP)</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($gradingSystem as $grade)
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="grades[{{ $grade->id }}][grade]"
                                                                class="form-control font-weight-bold"
                                                                value="{{ $grade->grade }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="grades[{{ $grade->id }}][min_score]"
                                                                class="form-control" value="{{ $grade->min_score }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="grades[{{ $grade->id }}][max_score]"
                                                                class="form-control" value="{{ $grade->max_score }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01"
                                                                name="grades[{{ $grade->id }}][point]" class="form-control"
                                                                value="{{ $grade->point }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="grades[{{ $grade->id }}][description]"
                                                                class="form-control" value="{{ $grade->description }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                {{-- Placeholder for new grade --}}
                                                <tr>
                                                    <td colspan="5"
                                                        class="bg-light text-center font-weight-bold text-muted">Add New
                                                        Grade Level</td>
                                                </tr>
                                                <tr>
                                                    <td><input type="text" name="grades[new][0][grade]" class="form-control"
                                                            placeholder="e.g. A"></td>
                                                    <td><input type="number" name="grades[new][0][min_score]"
                                                            class="form-control" placeholder="0"></td>
                                                    <td><input type="number" name="grades[new][0][max_score]"
                                                            class="form-control" placeholder="100"></td>
                                                    <td><input type="number" step="0.01" name="grades[new][0][point]"
                                                            class="form-control" placeholder="5.00"></td>
                                                    <td><input type="text" name="grades[new][0][description]"
                                                            class="form-control" placeholder="Excellent"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary">Save Grading System</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection