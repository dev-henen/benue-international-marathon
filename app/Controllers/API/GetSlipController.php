<?php

namespace App\Controllers\API;

use App\Models\Register;
use App\Mailer;
use App\SimpleEncryption;

require ROOT_PATH . '/bootstrap/database.php';

session_start();


class GetSlipController
{

    public function sendLinkToEmail(): void
    {

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
                $getSlipLink = "<a href='" . htmlspecialchars($getSlipUrl, ENT_QUOTES, 'UTF-8') . "'>Click here to download your registration slip</a>";

                $data = [
                    'subject' => sprintf("%s, you requested to download your registration slip", $registration->firstname),
                    'title' => "Here is your registration slip",
                    'message' => sprintf(
                        'Please click this link to download your registration slip: %s',
                        $getSlipLink
                    ),
                ];


                if (Mailer::send($email, $data['subject'], $data)) {
                    echo "Email sent successfully!";
                } else {
                    throw new \Exception("Failed to send email.");
                }
            } else {
                throw new \Exception("No registration found.");
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());

            $this->sendResponse(false, [
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    public function getRegistrationInfo(): void
    {
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
