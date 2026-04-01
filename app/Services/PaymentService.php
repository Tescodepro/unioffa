<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private string $gateway;

    private array $config;

    public function __construct(string $gateway = 'paystack')
    {
        $this->gateway = $gateway;

        $this->config = match ($gateway) {
            'paystack' => [
                'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
                'secret' => env('PAYSTACK_AUTH_KEY'),
            ],
            'oneapp' => [
                'base_url' => env('ONEAPP_BASE_URL', 'https://api.oneappgo.com/v1'),
                'secret' => env('ONEAPP_SECRET_KEY'),
            ],
            default => throw new Exception("Unsupported payment gateway: {$gateway}"),
        };
    }

    /**
     * Generate payment link (init transaction)
     */
    public function generatePaymentLink(array $data): array
    {
        try {
            $reference = $data['reference'];

            if ($reference === '') {
                return [
                    'status' => false,
                    'message' => 'refrence number is empty',
                    'checkout_url' => null,
                    'reference' => null,
                    'success' => false,
                    'raw' => [],
                ];
            }

            return match ($this->gateway) {
                'paystack' => $this->paystackPayment($data, $reference),
                'oneapp' => $this->oneappPayment($data, $reference),
                default => throw new Exception('Unsupported gateway'),
            };
        } catch (Exception $e) {
            Log::error("{$this->gateway} payment error: ".$e->getMessage());

            return [
                'status' => false,
                'message' => $e->getMessage(),
                'checkout_url' => null,
                'reference' => null,
                'success' => false,
                'raw' => [],
            ];
        }
    }

    /**
     * Paystack Init
     */
    private function paystackPayment(array $data, string $reference): array
    {
        $payload = [
            'amount' => $data['amount'] * 100, // kobo
            'email' => $data['email'],
            'reference' => $reference,
            'callback_url' => $data['callback_url'],
            'metadata' => $data['metadata'] ?? [],
        ];

        if (isset($data['split_code'])) {
            $payload['split_code'] = $data['split_code'];
        }

        $response = Http::withToken($this->config['secret'])
            ->post("{$this->config['base_url']}/transaction/initialize", $payload)
            ->json();

        return [
            'status' => $response['status'] ?? false,
            'message' => $response['message'] ?? 'Unable to initiate Paystack payment',
            'checkout_url' => $response['data']['authorization_url'] ?? null,
            'reference' => $response['data']['reference'] ?? $reference,
            'success' => false, // success is only for verify
            'raw' => $response,
        ];
    }

    /**
     * OneApp Init
     */
    private function oneappPayment(array $data, string $reference): array
    {
        $payload = [
            'reference' => $reference,
            'amount' => $data['amount'],
            'customer_email' => $data['email'],
            'phone' => $data['phone'] ?? '08000000000',
            'currency' => 'NGN',
            'redirecturl' => $data['callback_url'],
            'fname' => $data['fname'] ?? ($data['name'] ?? 'First'),
            'lname' => $data['lname'] ?? 'Last',
        ];

        $response = Http::withToken($this->config['secret'])
            ->asMultipart() // 🔑 makes it behave like cURL form-data
            ->post("{$this->config['base_url']}/business/initiatetrans", $payload)
            ->json();

        return [
            'status' => $response['status'] ?? false,
            'message' => $response['message'] ?? 'Unable to initiate Oneapp payment',
            'checkout_url' => $response['authorization_url'] ?? null,
            'reference' => $response['reference'] ?? $reference,
            'success' => $response['status'] ?? false,
            'raw' => $response,
        ];
    }

    /**
     * Verify a payment
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = match ($this->gateway) {
                'paystack' => $this->verifyPaystack($reference),
                'oneapp' => $this->verifyOneapp($reference),
                default => throw new Exception('Unsupported gateway'),
            };

            return $response;

        } catch (Exception $e) {
            Log::error("{$this->gateway} verification error: ".$e->getMessage());

            return [
                'status' => false,
                'message' => $e->getMessage(),
                'checkout_url' => null,
                'reference' => $reference,
                'success' => false,
                'raw' => [],
            ];
        }
    }

    private function verifyPaystack(string $reference): array
    {
        $response = Http::withToken($this->config['secret'])
            ->get("{$this->config['base_url']}/transaction/verify/{$reference}")
            ->json();

        $isSuccess = $response['status'] === true
            && isset($response['data']['status'])
            && $response['data']['status'] === 'success';

        return [
            'status' => $response['status'] ?? false,
            'message' => $response['message'] ?? 'Verification failed',
            'checkout_url' => null,
            'reference' => $response['data']['reference'] ?? $reference,
            'success' => $isSuccess,
            'raw' => $response,
        ];
    }

    private function verifyOneapp(string $reference): array
    {
        $response = Http::withToken($this->config['secret'])
            ->attach(
                'reference', $reference // forces multipart/form-data
            )
            ->post("{$this->config['base_url']}/business/verifytrans")
            ->json();

        $isSuccess = ($response['status'] ?? false) === true
        && isset($response['data']['trans_status'])
        && strcasecmp($response['data']['trans_status'], 'Successful') === 0
        && ($response['data']['responsecode'] ?? null) === '01';

        return [
            'status' => $response['status'] ?? false,
            'message' => $response['message'] ?? 'Verification failed',
            'checkout_url' => null,
            'reference' => $response['data']['reference'] ?? $reference,
            'success' => $isSuccess,
            'raw' => $response,
        ];
    }
}
