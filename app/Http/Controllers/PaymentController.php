<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Str;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Campus;
use App\Models\User;
use App\Models\{AdmissionList, ApplicationSetting, UserType, UserApplications, Faculty, PaymentSetting, StudentApplication};
use App\Models\Student;
use App\Mail\{GeneralMail};

class PaymentController extends Controller
{
    /**
     * Initiate a payment
     */
    public function initiatePayment(Request $request)
    {
        $gateway = $request->input('gateway', 'oneapp'); // default to oneapp
        $paymentService = new PaymentService($gateway);

        $user = $request->user();

        // Generate unique reference number
        $reference = $this->generateReference($request->fee_type);

        // Log transaction as pending
        $transaction = Transaction::create([
            'id'              => Str::uuid(),
            'user_id'         => $user->id,
            'description'     => $request->fee_type ?? 'Payment',
            'refernce_number' => $reference,
            'amount'          => $request->amount,
            'payment_status'  => 0, // pending
            'payment_type'    => $request->fee_type ?? 'tuition',
            'payment_method'  => $gateway,
            'session'         => activeSession()->name ?? '---',
            'meta_data'       => json_encode([
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]),
        ]);

        // Prepare gateway data
        $data = [
            'amount'       => $request->amount,
            'email'        => $user->email,
            'name'         => $user->name,
            'reference'    => $reference,
            'callback_url' => route('payment.callback'),
            'metadata'     => [
                'user_id'        => $user->id,
                'fee_type'       => $request->fee_type,
                'transaction_id' => $transaction->id,
            ],
        ];

        // Generate payment link
        $response = $paymentService->generatePaymentLink($data);
        // dd($response);

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
        $gateway = $request->input('gateway', 'oneapp'); // default
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
            if($paymentType == 'acceptance'){
                $this->migrationStudent();
            }
            $backRoute = route('application.dashboard');
        } else {
            $backRoute = route('students.load_payment');
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
        $reference = $payment_type . '-' . date('YmdHis') . '-' . Str::uuid()->toString();
        return $reference;
    } 

    private function migrationStudent(){

        $user = Auth::user();
        $studentType = UserType::where('name', 'student')->first();

        // update user type to student
        $user->user_type_id = $studentType->id;
        $user->save();

        $current_session = activeSession()->name ?? null;

         $user_application = UserApplications::where('user_id', $user->id)
         ->where('academic_session', $current_session)->first();

        $applicationSetting = ApplicationSetting::find($user_application->application_setting_id);
        $admission = AdmissionList::where(['user_id' => $user->id]);

        if($applicationSetting->programme_code == 'DE') {
            $studentData = [
                'programme' => 'DE',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => $user_application->academic_session,
                'sex' => $user->gender,
            ];
        } elseif($applicationSetting->programme_code == 'TOPUP') {
            $studentData = [
                'programme' => 'TOPUP',
                'entry_mode' => 'TOPUP',
                'level' => '300',
                'admission_session' => $user_application->academic_session,
            ];
        } elseif($applicationSetting->programme_code == 'TRANSFER') {
            $studentData = [
                'programme' => 'TRANSFER',
                'entry_mode' => 'TRANSFER',
                'level' => '200',
                'admission_session' => $user_application->academic_session,
            ];
        } elseif($applicationSetting->programme_code == 'IDELUTME') {
            $studentData = [
                'programme' => 'IDELUTME',
                'entry_mode' => 'UTME',
                'level' => '100',
                'admission_session' => $user_application->academic_session,
            ];
        }elseif($applicationSetting->programme_code == 'IDELDE') {
            $studentData = [
                'programme' => 'IDELDE',
                'entry_mode' => 'DE',
                'level' => '200',
                'admission_session' => $user_application->academic_session,
            ];
        }elseif($applicationSetting->programme_code == 'UMTE') {
            $studentData = [
                'programme' => 'UTME',
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
                'sex' => $studentData['sex'],
            ]);

        $migrate_student->save();

        return $migrate_student;
    }
}
