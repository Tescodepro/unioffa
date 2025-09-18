<?php

use App\Models\AcademicSession;
use App\Models\AcademicSemester;

if (! function_exists('activeSession')) {
    function activeSession()
    {
        return AcademicSession::where('status', '1')->first();
    }
}

if (! function_exists('activeSemester')) {
    function activeSemester()
    {
        return AcademicSemester::where('status', '1')->first();
    }
}
