<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses_OLD = [
            # --- Political Science 400 ---
            ['POL 401', 'Civil Military Relations', 3, 'C', 'POL', 400, '1st'],
            ['POL 403', 'Development Administration', 3, 'C', 'POL', 400, '1st'],
            ['POL 405', 'Third World and Dependency', 3, 'C', 'POL', 400, '1st'],
            ['POL 409', 'International Law and Organization', 2, 'C', 'POL', 400, '1st'],
            ['POL 411', 'Political Sociology', 2, 'C', 'POL', 400, '1st'],
            ['SSC 401', 'Research Methodology', 3, 'C', 'POL', 400, '1st'],
            ['IRS 407', 'Race and Ethnicity in International Relations', 2, 'C', 'POL', 400, '1st'],

            # --- Mass Communication 400 ---
            ['MAC 401', 'Mass Media and Law of Ethics', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 403', 'Data Analysis in Communication Research', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 405', 'Drama and Production', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 407', 'Rural Broadcasting', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 407', 'Documentary', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 411', 'Market Publications', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 413', 'Organization and Management of Advertising and P.R Agency', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 415', 'International Advertising', 2, 'C', 'MAC', 400, '1st'],
            ['MAC 417', 'Cinema Management', 2, 'C', 'MAC', 400, '1st'],
            ['SSC 401', 'Research Methodology', 3, 'C', 'MAC', 400, '1st'],

            # --- Accounting 400 ---
            ['ACC 411', 'Advanced Financial Accounting & Reporting', 3, 'C', 'ACC', 400, '1st'],
            ['ACC 413', 'Advanced Taxation 2', 3, 'C', 'ACC', 400, '1st'],
            ['ACC 415', 'International Accounting 1', 3, 'C', 'ACC', 400, '1st'],
            ['ACC 423', 'Extortion, Bankruptcy and Liquidation', 3, 'C', 'ACC', 400, '1st'],
            ['BUA 401', 'Business Policy and Strategy', 3, 'C', 'ACC', 400, '1st'],
            ['BUS 421', 'Business Decision Analysis', 3, 'C', 'ACC', 400, '1st'],
            ['SSC 401', 'Research Methodology', 3, 'C', 'ACC', 400, '1st'],
            ['ACC 317', 'Business Law', 3, 'C', 'ACC', 400, '1st'],

            # --- Business Administration 400 ---
            ['BUA 401', 'Business Policy and Strategy', 3, 'C', 'BUA', 400, '1st'],
            ['BUA 407', 'Business Communication Skills 1', 3, 'C', 'BUA', 400, '1st'],
            ['BUA 409', 'Management Information System', 3, 'C', 'BUA', 400, '1st'],
            ['BUA 411', 'Analysis for Business Decision', 3, 'C', 'BUA', 400, '1st'],
            ['BUA 413', 'Contemporary Management', 3, 'C', 'BUA', 400, '1st'],
            ['SSC 401', 'Research Methodology', 3, 'C', 'BUA', 400, '1st'],
            ['ECO 307', 'International Economics', 2, 'C', 'BUA', 400, '1st'],

            # --- Economics 400 ---
            ['ECO 401', 'Advanced Macroeconomics', 4, 'C', 'ECO', 400, '1st'],
            ['ECO 403', 'Comparative Economic System', 2, 'C', 'ECO', 400, '1st'],
            ['ECO 405', 'Problems and Policies DEV', 2, 'C', 'ECO', 400, '1st'],
            ['ECO 407', 'Economics of Production', 2, 'C', 'ECO', 400, '1st'],
            ['ECO 409', 'Operation Research', 2, 'C', 'ECO', 400, '1st'],
            ['SSC 401', 'Research Methodology', 3, 'C', 'ECO', 400, '1st'],

            # ----------------- B.Sc Accounting 100 -----------------
            ['GST 111', 'Communication in English', 2, 'C', 'ACC', 100, '1st'],
            ['AMS 101', 'Principles of Management', 2, 'C', 'ACC', 100, '1st'],
            ['AMS 103', 'Introduction to Computer', 2, 'C', 'ACC', 100, '1st'],
            ['ACC 101', 'Introduction to Financial Accounting 1', 3, 'C', 'ACC', 100, '1st'],
            ['ACC 105', 'Accounting as a Profession', 3, 'C', 'ACC', 100, '1st'],
            ['ACC 107', 'Introduction to Business', 3, 'C', 'ACC', 100, '1st'],

            # ----------------- B.Sc Economics 100 -----------------
            ['GST 111', 'Communication in English', 2, 'C', 'ECO', 100, '1st'],
            ['ECO 101', 'Principle of Economics 1', 2, 'C', 'ECO', 100, '1st'],
            ['ECO 103', 'Introductory Mathematics 1', 2, 'C', 'ECO', 100, '1st'],
            ['ECO 105', 'Introduction to Finance 1', 3, 'C', 'ECO', 100, '1st'],
            ['ECO 107', 'Introduction to Financial Accounting', 3, 'C', 'ECO', 100, '1st'],
            ['ECO 109', 'Introduction to Political Economy', 3, 'C', 'ECO', 100, '1st'],

            # ----------------- B.Sc Business Administration 100 -----------------
            ['GST 111', 'Communication in English', 2, 'C', 'BUS', 100, '1st'],
            ['AMS 101', 'Principle of Management', 2, 'C', 'BUS', 100, '1st'],
            ['AMS 103', 'Introduction to Computer', 2, 'C', 'BUS', 100, '1st'],
            ['BUA 101', 'Introduction to Business 1', 3, 'C', 'BUS', 100, '1st'],
            ['BUA 105', 'Introduction to Economics 1', 3, 'C', 'BUS', 100, '1st'],
            ['BUS 107', 'Corporate Governance and Business Ethics', 3, 'C', 'BUS', 100, '1st'],

            # ----------------- B.Sc Mass Communication 100 -----------------
            ['GST 111', 'Communication in English', 2, 'C', 'MAC', 100, '1st'],
            ['CMS 101', 'Introduction to Human Communication', 2, 'C', 'MAC', 100, '1st'],
            ['MCM 101', 'Foundations of Broadcasting and Film', 3, 'C', 'MAC', 100, '1st'],
            ['MCM 103', 'Introduction to Advertising', 2, 'C', 'MAC', 100, '1st'],
            ['MCM 105', 'Introduction to Book Publishing', 2, 'C', 'MAC', 100, '1st'],
            ['MCM 107', 'Introduction to Photojournalism', 2, 'C', 'MAC', 100, '1st'],
            ['MCM 109', 'Public Relations Campaigns', 3, 'C', 'MAC', 100, '1st'],
            ['MCM 111', 'Media Literacy and Education 1', 3, 'C', 'MAC', 100, '1st'],

            # ----------------- B.Sc Political Science 100 -----------------
            ['GST 111', 'Communication in English', 2, 'C', 'POL', 100, '1st'],
            ['POL 101', 'Introduction to Political Science', 2, 'C', 'POL', 100, '1st'],
            ['POL 103', 'Organization of Government', 2, 'C', 'POL', 100, '1st'],
            ['POL 105', 'Nigerian Constitutional Development', 2, 'C', 'POL', 100, '1st'],
            ['POL 107', 'Political Science as a Profession', 3, 'C', 'POL', 100, '1st'],
            ['POL 109', 'Administrative System in the Old Oyo Empire', 3, 'C', 'POL', 100, '1st'],
            ['POL 111', 'Civic Education', 3, 'C', 'POL', 100, '1st'],

            # --- B.Sc Accounting 200 - 1st Semester ---
            ['ACC 201', 'Introduction to Financial Accounting 1', 3, 'C', 'ACC', 200, '1st'],
            ['ACC 203', 'Foundations of Taxation', 3, 'C', 'ACC', 200, '1st'],
            ['ACC 205', 'Introduction to Finance', 2, 'C', 'ACC', 200, '1st'],
            ['BUS 201', 'Introduction to Business', 3, 'C', 'ACC', 200, '1st'],
            ['BUS 203', 'Business Communication', 3, 'C', 'ACC', 200, '1st'],
            ['ECO 201', 'Micro Economic Theory', 3, 'C', 'ACC', 200, '1st'],
            ['SMS 201', 'Statistics for Management Sciences 1', 3, 'C', 'ACC', 200, '1st'],
            ['GNS 201', "Nigerian People's and Culture", 2, 'C', 'ACC', 200, '1st'],
            ['GNS 203', 'Entrepreneurship and Innovation', 2, 'C', 'ACC', 200, '1st'],

            # --- B.Sc Economics 200 - 1st Semester ---
            ['ACC 205', 'Introduction to Finance', 2, 'C', 'ECO', 200, '1st'],
            ['BUS 201', 'Introduction to Business', 3, 'C', 'ECO', 200, '1st'],
            ['ECO 201', 'Micro-economic Theory 1', 3, 'C', 'ECO', 200, '1st'],
            ['ECO 203', 'Statistics for Economists 1', 3, 'C', 'ECO', 200, '1st'],
            ['ECO 205', 'Mathematics for Economists 1', 2, 'C', 'ECO', 200, '1st'],
            ['ECO 207', 'The Nigerian Economy in Perspective', 2, 'C', 'ECO', 200, '1st'],
            ['SMS 201', 'Statistics for Management Sciences 1', 3, 'C', 'ECO', 200, '1st'],
            ['GNS 201', "Nigerian People's and Culture", 2, 'C', 'ECO', 200, '1st'],
            ['GNS 203', 'Entrepreneurship and Innovation', 2, 'C', 'ECO', 200, '1st'],

            # --- B.Sc Business Administration 200 - 1st Semester ---
            ['ACC 201', 'Introduction to Financial Accounting 1', 3, 'C', 'BUS', 200, '1st'],
            ['ACC 205', 'Introduction to Finance', 2, 'C', 'BUS', 200, '1st'],
            ['BUS 201', 'Introduction to Business', 3, 'C', 'BUS', 200, '1st'],
            ['BUS 203', 'Business Communication', 3, 'C', 'BUS', 200, '1st'],
            ['ECO 201', 'Micro Economic Theory', 3, 'C', 'BUS', 200, '1st'],
            ['SMS 201', 'Statistics for Management Sciences 1', 3, 'C', 'BUS', 200, '1st'],
            ['GNS 201', "Nigerian People's and Culture", 2, 'C', 'BUS', 200, '1st'],
            ['GNS 203', 'Entrepreneurship and Innovation', 2, 'C', 'BUS', 200, '1st'],

            # --- B.Sc Mass Communication 200 - 1st Semester ---
            ['MAC 201', 'Theories of Mass Communication', 3, 'C', 'MAC', 200, '1st'],
            ['MAC 203', 'Foundations of Communication Research', 3, 'C', 'MAC', 200, '1st'],
            ['MAC 205', 'Editing and Graphics of Communication', 3, 'C', 'MAC', 200, '1st'],
            ['MAC 207', 'Feature and Magazine Art Writing', 3, 'C', 'MAC', 200, '1st'],
            ['MAC 209', 'News Writing and Reporting', 3, 'C', 'MAC', 200, '1st'],
            ['SMS 201', 'Statistics for Management Sciences 1', 3, 'C', 'MAC', 200, '1st'],
            ['GNS 201', "Nigerian People's and Culture", 2, 'C', 'MAC', 200, '1st'],
            ['GNS 203', 'Entrepreneurship and Innovation', 2, 'C', 'MAC', 200, '1st'],

            # --- B.Sc Political Science 200 - 1st Semester ---
            ['POL 201', 'Introduction to Political Analysis', 3, 'C', 'POL', 200, '1st'],
            ['POL 203', 'Perspective on Nigerian Politics', 3, 'C', 'POL', 200, '1st'],
            ['POL 205', 'Foundations of Political Economy', 3, 'C', 'POL', 200, '1st'],
            ['POL 207', 'Introduction to International Relations', 3, 'C', 'POL', 200, '1st'],
            ['POL 209', 'Introduction to Political Psychology', 3, 'C', 'POL', 200, '1st'],
            ['SMS 201', 'Statistics for Management Sciences 1', 3, 'C', 'POL', 200, '1st'],
            ['GNS 201', "Nigerian People's and Culture", 2, 'C', 'POL', 200, '1st'],
            ['GNS 203', 'Entrepreneurship and Innovation', 2, 'C', 'POL', 200, '1st'],

            # --- B.Sc Accounting 300 - 1st Semester ---
            ['GST 311', 'Peace and Conflict Resolution 1', 2, 'C', 'ACC', 300, '1st'],
            ['ACC 301', 'Financial Reporting 1', 3, 'C', 'ACC', 300, '1st'],
            ['ACC 303', 'Management Accounting', 3, 'C', 'ACC', 300, '1st'],
            ['ACC 305', 'Taxation 1', 3, 'C', 'ACC', 300, '1st'],
            ['ACC 307', 'Auditing and Assurance 1', 3, 'C', 'ACC', 300, '1st'],
            ['ACC 311', 'Entrepreneurship in Accounting', 3, 'C', 'ACC', 300, '1st'],

            # --- B.Sc Economics 300 - 1st Semester ---
            ['GST 311', 'Peace and Conflict Resolution 1', 2, 'C', 'ECO', 300, '1st'],
            ['ECO 301', 'Intermediate Microeconomics 1', 3, 'C', 'ECO', 300, '1st'],
            ['ECO 303', 'Intermediate Macroeconomics 1', 3, 'C', 'ECO', 300, '1st'],
            ['ECO 305', 'History of Economic Thought', 2, 'C', 'ECO', 300, '1st'],
            ['ECO 307', 'Project Evaluation', 2, 'C', 'ECO', 300, '1st'],
            ['ECO 309', 'Public Sector Economics', 3, 'C', 'ECO', 300, '1st'],
            ['SSC 301', 'Innovation in Social Sciences', 2, 'C', 'ECO', 300, '1st'],

            # --- B.Sc Business Administration 300 - 1st Semester ---
            ['GST 311', 'Peace and Conflict Resolution 1', 2, 'C', 'BUS', 300, '1st'],
            ['BUA 303', 'Management Theory', 3, 'C', 'BUS', 300, '1st'],
            ['BUA 305', 'Financial Management', 3, 'C', 'BUS', 300, '1st'],
            ['BUA 311', 'Production and Operation Management', 3, 'C', 'BUS', 300, '1st'],
            ['BUA 313', 'Innovation Management', 3, 'C', 'BUS', 300, '1st'],
            ['BUA 319', 'E-commerce', 3, 'C', 'BUS', 300, '1st'],

            # --- B.Sc Political Science 300 - 1st Semester ---
            ['GST 311', 'Peace Studies and Conflict Resolution 1', 2, 'C', 'POL', 300, '1st'],
            ['POL 301', 'History of Political Thought', 3, 'C', 'POL', 300, '1st'],
            ['POL 303', 'Contemporary Political Analysis', 3, 'C', 'POL', 300, '1st'],
            ['POL 305', 'Public Policy Analysis', 2, 'C', 'POL', 300, '1st'],
            ['POL 307', 'Statistics for Political Science', 2, 'C', 'POL', 300, '1st'],
            ['POL 309', 'Theories of International Relations', 2, 'C', 'POL', 300, '1st'],
            ['SSC 301', 'Innovation in Social Sciences', 2, 'C', 'POL', 300, '1st'],

            # --- B.Sc Mass Communication 300 - 1st Semester ---
            ['CMS 303', 'Data Analysis in Comm.', 2, 'GN', 'MAC', 300, '1st'],
            ['MCM 301', 'Mass Communication and Politics', 2, 'GN', 'MAC', 300, '1st'],
            ['MCM 303', 'Gender and Communication', 2, 'GN', 'MAC', 300, '1st'],
            ['MCM 305', 'Newspaper/Magazine Mgt. & Prod.', 2, 'GN', 'MAC', 300, '1st'],
            ['MCM 307', 'Photojournalism Research & Mgt.', 2, 'GN', 'MAC', 300, '1st'],
            ['MCM 309', 'Commentary, Critical Writing & Public Affairs', 2, 'BD', 'MAC', 300, '1st'],
            ['MCM 311', 'Film Production & Screen Directing', 2, 'BD', 'MAC', 300, '1st'],
            ['MCM 313', 'Advertising & Public Relation Research', 2, 'PR', 'MAC', 300, '1st'],
            ['MCM 315', 'Consumer Affairs', 2, 'PR', 'MAC', 300, '1st'],
            ['MCM 317', 'Radio/Television Programme Writing', 2, 'BD', 'MAC', 300, '1st'],
            ['MCM 319', 'Marketing Foundation for PRAD.', 2, 'PR', 'MAC', 300, '1st']
        ];

        $courses = [
            ['BUA 401', 'Business Policy and Strategy', 3, 'C', 'BUS', 400, '1st'],
            ['BUA 407', 'Business Communication Skills 1', 3, 'C', 'BUS', 400, '1st'],
            ['BUA 409', 'Management Information System', 3, 'C', 'BUS', 400, '1st'],
            ['BUA 411', 'Analysis for Business Decision', 3, 'C', 'BUS', 400, '1st'],
            ['BUA 413', 'Contemporary Management', 3, 'C', 'BUS', 400, '1st'],
            ['SSC 401', 'Research Methodology', 3, 'C', 'BUS', 400, '1st'],
            ['ECO 307', 'International Economics', 2, 'C', 'BUS', 400, '1st']
        ];

        foreach ($courses as $course) {
            [$code, $title, $unit, $status, $deptCode, $level, $semester] = $course;
            $department = Department::where('department_code', $deptCode)->first();
            if (! $department) {
                $this->command->warn("⚠️ Department {$deptCode} not found. Skipping course {$code}.");
                continue;
            }
            DB::table('courses')->updateOrInsert(
                ['course_code' => $code],
                [
                    'id' => Str::uuid(),
                    'course_title' => $title,
                    'course_code' => $code,
                    'course_unit' => $unit,
                    'course_status' => $status,
                    'department_id' => $department->id,
                    'level' => $level,
                    'semester' => $semester,
                    'active_for_register' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ ' . count($courses) . ' courses seeded successfully.');
    }
}
