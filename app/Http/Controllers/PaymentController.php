<?php

namespace App\Http\Controllers;

use App\Models\AdmissionList;
use App\Models\AgentApplication;
use App\Models\ApplicationSetting;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserApplications;
use App\Models\UserType;
use App\Models\Campus;
use Carbon\Carbon;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\{HostelAssignmentService, MatricNumberGenerationService};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    /**
     * Initiate a payment
     */
    public function initiatePayment(Request $request)
    {
        $gateway = env('DEFULT_PAYMENT_GATEWAY');
        $paymentService = new PaymentService($gateway);

        $user = $request->user();

        // Generate unique reference number
        $reference = $this->generateReference($request->fee_type);

        // Log transaction as pending
        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'description' => $request->fee_type ?? 'Payment',
            'refernce_number' => $reference,
            'amount' => $request->amount,
            'payment_status' => 0, // pending
            'payment_type' => $request->fee_type ?? 'tuition',
            'payment_method' => $gateway,
            'session' => activeSession()->name ?? '---',
            'meta_data' => json_encode([
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]),
        ]);

        $center_id_generated = $user->student->campus_id ?? $user->campus_id ?? null;
        if (!$center_id_generated) {
            return back()->with('error', 'Campus ID missing for this user. Please contact support.');
        }
        $campusDetail = Campus::getCampusDetail($center_id_generated);
        if (!$campusDetail) {
            return back()->with('error', 'There an error in your information kindly contact support to update your campus details.');
        }

        if (in_array($request->fee_type, ['application', 'acceptance'])) {
            $getuserstype = UserApplications::where('user_id', Auth::id())
                ->join('application_settings', 'user_applications.application_setting_id', '=', 'application_settings.id')
                ->select('application_settings.application_code AS programme')
                ->first();
            if (!$getuserstype) {
                return back()->with('error', 'Application record not found for this user.');
            }

            // Normalize programme
            if (in_array($getuserstype->programme, ['TRANSFER', 'DIPLOMA', 'DE', 'UTME'])) {
                $programme = 'REGULAR';
            } else {
                $programme = $getuserstype->programme;
            }

            $split_code = $this->splitGet($request->fee_type, $programme, $campusDetail->slug);
            if ($request->fee_type === 'acceptance' && $user->referee_code) {
                $agentSplit = AgentApplication::where('unique_code', $user->referee_code)->where('status', 'approved')->value('split_code');
                $split_code = $agentSplit ?? $split_code;
            }
        } else {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return back()->with('error', 'Student record not found for this user.');
            }
            $split_code = $this->splitGet($request->fee_type, $student->programme, $campusDetail->slug);
        }
        // Prepare gateway data

        // dd($split_code);
        $data = [
            'amount' => $request->amount,
            'email' => $user->email,
            'name' => $user->name,
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'user_id' => $user->id,
                'fee_type' => $request->fee_type,
                'transaction_id' => $transaction->id,
            ],
        ];

        if ($split_code !== null) {
            $data['split_code'] = $split_code;
        }

        // Generate payment link
        $response = $paymentService->generatePaymentLink($data);
        if ($response['status'] && !empty($response['checkout_url'])) {
            return redirect()->away($response['checkout_url']);
        }

        return back()->with('error', $response['message'] ?? 'Unable to start payment');
    }
    /**
     * Handle gateway callback
     */
    public function handleCallback(Request $request)
    {
        $gateway = env('DEFULT_PAYMENT_GATEWAY'); // default
        $paymentService = new PaymentService($gateway);

        if ($request->has('reference')) {
            $reference = $request->input('reference');
        } elseif ($request->has('transref')) {
            $reference = $request->input('transref');
        } else {
            return redirect()->route('payment.status.page')->with('error', 'Invalied payment: reference is missing');
        }
        // Verify payment
        $response = $paymentService->verifyPayment($reference);

        // Find the transaction
        $transaction = Transaction::where('refernce_number', $reference)->first();
        // get payment type
        $paymentType = $transaction->payment_type;
        // decide redirect route
        if (in_array($paymentType, ['application', 'acceptance'])) {
            if ($paymentType == 'acceptance') {
                $this->migrationStudent();
            }
            $backRoute = route('application.dashboard');
        } else {
            if (in_array($paymentType, ['accommodation', 'hostel', 'maintenance'])) {
                $backRoute = route('students.hostel.index');
            } else {
                if ($paymentType == 'tuition' && !Student::hasMatricNumber()) {
                    $user = $transaction->user; // Already authenticated!
                    $student = $user->student;
                    if ($student) {
                        $year = $year = $student->admission_session;
                        $newMatricNo = Student::generateMatricNo($student->department->department_code, $year, $student->entry_mode);
                        $student->update(['matric_no' => $newMatricNo]);
                        $student->user->update(['username' => $newMatricNo]);
                    }
                }

                $backRoute = route('students.load_payment');
            }
        }

        if ($response['success']) {

            if ($transaction) {
                $transaction->update(['payment_status' => 1]); // success
            }

            return view('payment-status-page', compact('paymentType', 'transaction', 'backRoute'))->with('success', 'Payment successful');
        }

        if ($transaction) {
            $transaction->update(['payment_status' => 2]); // failed
        }

        return view('payment-status-page', compact('paymentType', 'transaction', 'backRoute'))->with('error', 'Payment failed or canceled');
    }

    /**
     * Show payment status message
     */
    public function paymentStatusPage()
    {
        return view('payment-status-page');
    }

    private function generateReference($payment_type): string
    {
        $reference = $payment_type . '-' . uniqid() . substr(md5(rand()), 0, 8);

        return $reference;
    }

    private function migrationStudent()
    {

        $user = Auth::user();
        $studentType = UserType::where('name', 'student')->first();

        // update user type to student
        $user->user_type_id = $studentType->id;
        $user->save();

        $current_session = activeSession()->name ?? null;

        $user_application = UserApplications::where('user_id', $user->id)
            ->where('academic_session', $current_session)->first();

        $applicationSetting = ApplicationSetting::find($user_application->application_setting_id);

        $admission = AdmissionList::where(['user_id' => $user->id])->first();

        if ($applicationSetting->application_code == 'DE') {
            $studentData = [
                'programme' => 'REGULAR',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => $user_application->academic_session,
                'sex' => $user->gender,
            ];
        } elseif ($applicationSetting->application_code == 'TOPUP') {
            $studentData = [
                'programme' => 'TOPUP',
                'entry_mode' => 'TOPUP',
                'level' => '300',
                'admission_session' => $user_application->academic_session,
            ];
        } elseif ($applicationSetting->application_code == 'TRANSFER') {
            $studentData = [
                'programme' => 'REGULAR',
                'entry_mode' => 'TRANSFER',
                'level' => '200',
                'admission_session' => $user_application->academic_session,
            ];
        } elseif ($applicationSetting->application_code == 'IDELUTME') {
            $studentData = [
                'programme' => 'IDELUTME',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => $user_application->academic_session,
            ];
        } elseif ($applicationSetting->application_code == 'IDELDE') {
            $studentData = [
                'programme' => 'IDELDE',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => $user_application->academic_session,
            ];
        } elseif ($applicationSetting->application_code == 'UTME') {
            $studentData = [
                'programme' => 'REGULAR',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => $user_application->academic_session,
            ];
        }

        // migrate to student table
        $migrate_student = Student::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'campus_id' => $user->campus_id,
            'department_id' => $admission->approved_department_id,
            'matric_no' => $user->registration_no,
            'programme' => $studentData['programme'],
            'entry_mode' => $studentData['entry_mode'],
            'level' => $studentData['level'],
            'admission_session' => $studentData['admission_session'],
            'admission_date' => now(),
            'status' => 1,
            'sex' => $user->sex,
        ]);

        $migrate_student->save();

        return $migrate_student;
    }
    // payment receipt
    public function downloadReceipt($transaction_id)
    {
        $user = Auth::user();

        $transaction = Transaction::where('id', $transaction_id)
            ->where('user_id', $user->id)
            ->where('payment_status', 1) // only successful payments
            ->first();

        if (! $transaction) {
            return redirect()->back()->with('error', 'Transaction not found or not successful.');
        }

        $data = [
            'user' => $user,
            'transaction' => $transaction,
            'date' => now()->format('F d, Y'),
        ];

        $pdf = Pdf::loadView('general-payment-receipt', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download('Payment_Receipt_' . $transaction->refernce_number . '.pdf');
    }

    public function verifyReceipt($ref)
    {
        $transaction = Transaction::where('refernce_number', $ref)
            ->where('payment_status', 1) // only successful
            ->with('user.student.department')
            ->first();

        if (! $transaction) {
            return view('verify-receipt', [
                'transaction' => null,
                'ref' => $ref,
            ]);
        }

        return view('verify-receipt', [
            'transaction' => $transaction,
            'ref' => $ref,
        ]);
    }

    private function hostelApply()
    {
        $student = Auth::user()->student;

        if (! $student) {
            return back()->with('error', 'Student profile not found.');
        }

        $hostelService = new HostelAssignmentService();

        $result = $hostelService->autoAssign($student);

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    private function splitGet($payment_type, $student_type, $center_id)
    {
        return DB::table('payment_splits')
            ->whereJsonContains('payment_type', $payment_type)
            ->whereJsonContains('student_type', $student_type)
            ->where('center', $center_id)
            ->value('split_code');
    }
}
