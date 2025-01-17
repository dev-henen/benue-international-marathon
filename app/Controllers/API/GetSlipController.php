<?php

namespace App\Controllers\API;

use App\Models\Register;
use App\Mailer;
use App\SimpleEncryption;
use App\CustomRateLimiter;

require ROOT_PATH . '/bootstrap/database.php';

session_start();


class GetSlipController
{

    public function sendLinkToEmail(): void
    {

        $limiter = new CustomRateLimiter();

        $isLimited = $limiter->isLimitExceeded(
            'send_register_link',
            allowedJobs: 50,  // allow 50 attempts
            windowInSeconds: 60  // in a 60-second window
        );
        if ($isLimited) {
            $this::sendResponse(false, ['message' => 'Too many requests. Please try again later.'], 429);
            return;
        }

        try {
            $input = file_get_contents('php://input');
            if ($input === false) {
                throw new \RuntimeException('Failed to read input data');
            }

            $input = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON data');
            }

            $email = trim($input['email']);
            $regYear = REGISTRATION_DETAILS['year'];

            if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this::sendResponse(false, ['error' => 'Invalid email address'], 400);
                return;
            }

            $registration = Register::where('email', $email)
                ->where('reg_year', $regYear)
                ->first();

            if ($registration) {

                $get_slip_details = [
                    'id' => $registration->id,
                    'link_expiration_time' => time() + (60 * 60 * 1), // 1 hour
                ];

                $encryptionKey = ENCRYPTION_KEY;
                $encryption = new SimpleEncryption($encryptionKey);
                $encrypted_link = $encryption->encrypt(json_encode($get_slip_details));

                $getSlipUrl = "http://localhost:8000/getSlip?hash=" . urlencode($encrypted_link);

                $data = [
                    'subject' => sprintf("%s, you requested to download your registration slip", $registration->firstname),
                    'title' => "Here is your registration slip",
                    'message' => sprintf(
                        'Please click the link/button bellow to download your registration slip. Note: this link will expire in 1 hour.',
                    ),
                    'action_link' => htmlspecialchars($getSlipUrl, ENT_QUOTES, 'UTF-8'),
                    'action_text' => 'Download',
                ];


                if (Mailer::send($email, $data['subject'], $data)) {
                    $this->sendResponse(false, [
                        'message' => 'Email sent successfully!'
                    ], 400);
                } else {
                    $this->sendResponse(false, [
                        'message' => 'Failed to send email.'
                    ], 400);
                }
            } else {
                $this->sendResponse(false, [
                    'message' => 'No registration found.'
                ], 400);
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());

            $this->sendResponse(false, [
                'message' => 'An unexpected error occurred'
            ], 200);
        }
    }

    public function getRegistrationInfo(): void
    {

        $limiter = new CustomRateLimiter();

        $isLimited = $limiter->isLimitExceeded(
            'get_register_link',
            allowedJobs: 50,  // allow 5 attempts
            windowInSeconds: 60  // in a 60-second window
        );
        if ($isLimited) {
            $this::sendResponse(false, ['message' => 'Too many requests. Please try again later.'], 429);
            return;
        }

        try {
            $input = file_get_contents('php://input');
            if ($input === false) {
                throw new \RuntimeException('Failed to read input data');
            }

            $input = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON data');
            }

            $hashCode = $input['hashCode'];

            $encryptionKey = ENCRYPTION_KEY;
            $encryption = new SimpleEncryption($encryptionKey);
            $decrypted_data = $encryption->decrypt($hashCode);

            settype($decrypted_data, 'integer');
            if (!is_int($decrypted_data)) {
                throw new \RuntimeException('Invalid hashCode');
            }

            $registration = Register::where('id', $decrypted_data)
                ->first();
            if ($registration) {
                echo json_encode($registration);
            } else {
                throw new \RuntimeException('No registration found');
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());

            $this->sendResponse(false, [
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    private function sendResponse(bool $success, array $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            ...$data
        ], JSON_THROW_ON_ERROR);
    }
}
