<?php

namespace App\Controllers\API;

use Payment\PaystackPayment;

require ROOT_PATH . '/bootstrap/payment.php';

session_start();

class GenerateTransactionReferenceController
{

    public function registration(): void
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
            $amount = 200000; // Amount in kobo

            if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this::sendResponse(false, ['error' => 'Invalid email address'], 400);
                return;
            }

            $result = PaystackPayment::generateTransactionReference($email, $amount);

            if ($result['success']) {
                $this::sendResponse(true, ['data' => $result['data']], 200);
            } else {
                $this::sendResponse(false, ['error' => $result['error']], 500);
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