<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS to a recipient.
     *
     * @param string $to Recipient phone number (e.g. +1234567890)
     * @param string $message The message content
     * @return array ['success' => bool, 'message' => string]
     */
    public static function send(string $to, string $message): array
    {
        $provider = Setting::getValue('sms_gateway_provider', 'disabled');

        if ($provider === 'disabled' || !$provider) {
            return [
                'success' => false,
                'message' => 'SMS Gateway is disabled.'
            ];
        }

        try {
            switch ($provider) {
                case 'twilio':
                    return self::sendTwilio($to, $message);
                case 'vonage':
                    return self::sendVonage($to, $message);
                case 'custom':
                    return self::sendCustom($to, $message);
                default:
                    return [
                        'success' => false,
                        'message' => 'Unknown SMS provider.'
                    ];
            }
        } catch (\Throwable $e) {
            Log::error('SMS Dispatch Failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }

    private static function sendTwilio(string $to, string $message): array
    {
        $sid = Setting::getValue('twilio_sid');
        $token = Setting::getValue('twilio_token');
        $from = Setting::getValue('twilio_from');

        if (!$sid || !$token || !$from) {
            return ['success' => false, 'message' => 'Twilio configurations are missing.'];
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post($url, [
                'To' => $to,
                'From' => $from,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            return ['success' => true, 'message' => 'SMS sent successfully via Twilio.'];
        }

        return ['success' => false, 'message' => 'Twilio Error: ' . $response->body()];
    }

    private static function sendVonage(string $to, string $message): array
    {
        $key = Setting::getValue('vonage_api_key');
        $secret = Setting::getValue('vonage_api_secret');
        $from = Setting::getValue('vonage_from', 'Aetheria');

        if (!$key || !$secret) {
            return ['success' => false, 'message' => 'Vonage configurations are missing.'];
        }

        $url = "https://rest.nexmo.com/sms/json";

        $response = Http::post($url, [
            'api_key' => $key,
            'api_secret' => $secret,
            'to' => $to,
            'from' => $from,
            'text' => $message,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $messages = $data['messages'] ?? [];
            if (!empty($messages) && $messages[0]['status'] === '0') {
                return ['success' => true, 'message' => 'SMS sent successfully via Vonage.'];
            }
            return ['success' => false, 'message' => 'Vonage Error: ' . ($messages[0]['error-text'] ?? 'Unknown error')];
        }

        return ['success' => false, 'message' => 'Vonage Error: ' . $response->body()];
    }

    private static function sendCustom(string $to, string $message): array
    {
        $urlTemplate = Setting::getValue('custom_sms_url');
        $method = strtoupper(Setting::getValue('custom_sms_method', 'POST'));
        $headersJson = Setting::getValue('custom_sms_headers', '{}');
        $payloadJson = Setting::getValue('custom_sms_payload', '{}');

        if (!$urlTemplate) {
            return ['success' => false, 'message' => 'Custom SMS API URL is missing.'];
        }

        // Replace placeholders in URL
        $url = str_replace(
            ['{to}', '{message}'],
            [urlencode($to), urlencode($message)],
            $urlTemplate
        );

        $headers = json_decode($headersJson, true) ?: [];
        $payloadRaw = str_replace(
            ['{to}', '{message}'],
            [$to, $message],
            $payloadJson
        );
        $payload = json_decode($payloadRaw, true) ?: [];

        $request = Http::withHeaders($headers);

        if ($method === 'GET') {
            $response = $request->get($url, $payload);
        } else {
            $isJson = false;
            foreach ($headers as $k => $v) {
                if (strtolower($k) === 'content-type' && str_contains(strtolower($v), 'json')) {
                    $isJson = true;
                    break;
                }
            }
            if ($isJson) {
                $response = $request->post($url, $payload);
            } else {
                $response = $request->asForm()->post($url, $payload);
            }
        }

        if ($response->successful()) {
            return ['success' => true, 'message' => 'SMS sent successfully via Custom Gateway.'];
        }

        return ['success' => false, 'message' => 'Custom SMS Gateway Error: ' . $response->body()];
    }
}
