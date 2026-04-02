<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class BrevoMailService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY');
        $this->client = new Client();
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        try {
            $response = $this->client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'api-key'      => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
                'json' => [
                    'sender' => [
                        'name'  => env('APP_NAME'),
                        'email' => env('MAIL_FROM_ADDRESS'),
                    ],
                    'to' => [
                        ['email' => $toEmail, 'name' => $toName]
                    ],
                    'subject'     => $subject,
                    'htmlContent' => $htmlContent,
                ],
            ]);

            return $response->getStatusCode() === 201;

        } catch (Exception $e) {
            \Log::error('Brevo Mail Error: ' . $e->getMessage());
            return false;
        }
    }
}