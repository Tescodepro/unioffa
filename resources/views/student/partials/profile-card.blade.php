<div class="col-xl-12 d-flex">
    <div class="flex-fill">
        <div class="card bg-dark position-relative">
            <div class="card-body">
                <div class="d-flex align-items-center row-gap-3 mb-3">

                    <!-- Avatar -->
                    <div class="avatar avatar-xxl rounded flex-shrink-0 me-3">
                        @php
                            $passport =
                                auth()->user()->profile_picture &&
                                file_exists(public_path(auth()->user()->profile_picture))
                                    ? asset(auth()->user()->profile_picture)
                                    : asset('portal_assets/img/users/placeholder.jpeg');
                        @endphp
                        <img src="{{ $passport }}" alt="Passport">
                    </div>

                    <!-- User Info -->
                    <div class="d-block">
                        <span class="badge bg-transparent-primary text-primary mb-1">
                            {{ $user->username }}
                        </span>
                        <h3 class="text-truncate text-white mb-1">
                            {{ $user->full_name }}
                        </h3>

                        <div class="d-flex align-items-center flex-wrap row-gap-2 text-gray-2">
                            <span class="border-end me-2 pe-2">
                                Faculty: {{ $user->student->department->faculty->faculty_name ?? 'N/A' }}
                            </span>
                            <span class="border-end me-2 pe-2">
                                Department: {{ $user->student->department->department_name ?? 'N/A' }}
                            </span>
                            <span class="border-end me-2 pe-2">
                                Level: {{ $user->student->level ?? 'N/A' }}
                            </span>
                            <span class="border-end me-2 pe-2">
                                Programme: {{ $user->student->programme ?? 'N/A' }}
                            </span>
                            <span class="me-2 pe-2">
                                Gender: {{ $user->student->sex ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
