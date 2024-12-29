<?php

namespace App\Controllers\API;

class RegisterController
{
    private const REQUIRED_FIELDS = [
        'surname',
        'firstName',
        'gender',
        'bloodGroup',
        'birthday',
        'nationality',
        'stateOfOrigin',
        'stateOfResidence',
        'email',
        'phoneNumber',
        'contactAddress',
        'myHeight',
        'myWeight',
        'emergencyPhoneNumber',
        'passport',
        'confirm'
    ];

    private function validateInput(array $data): array
    {
        $errors = [];

        // Validate required fields
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('my', '', $field)) . ' is required';
            }
        }

        // Additional validation rules
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (!empty($data['phoneNumber']) && !preg_match('/^\+?[\d\s-]{10,}$/', $data['phoneNumber'])) {
            $errors['phoneNumber'] = 'Invalid phone number format';
        }

        if (!empty($data['birthday'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['birthday']);
            if (!$date || $date->format('Y-m-d') !== $data['birthday']) {
                $errors['birthday'] = 'Invalid date format';
            }
        }

        return $errors;
    }

    private function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_string($value) ? trim(strip_tags($value)) : $value;
        }, $data);
    }

    public function register(): void
    {
        try {
            // Get and decode input data with error checking
            $rawData = file_get_contents('php://input');
            if ($rawData === false) {
                throw new \RuntimeException('Failed to read input data');
            }

            $data = json_decode($rawData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON data');
            }

            // Sanitize input
            $data = $this->sanitizeInput($data);

            // Validate input
            $errors = $this->validateInput($data);
            if (!empty($errors)) {
                $this->sendResponse(false, ['errors' => $errors], 400);
                return;
            }

            // TODO: Add your database logic here
            // For demonstration, we'll use a mock user ID
            $userId = rand(1000, 9999);

            // Prepare response data (only include necessary fields)
            $responseData = [
                'id' => $userId,
                'surname' => $data['surname'],
                'firstName' => $data['firstName'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'birthday' => $data['birthday'],
                'nationality' => $data['nationality']
            ];

            $this->sendResponse(true, [
                'message' => 'Registration successful!',
                'data' => $responseData
            ], 201);
        } catch (\Throwable $e) {
            // Log the error (implement proper logging)
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
