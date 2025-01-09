<?php

namespace App\Controllers\API;

use App\Models\Register;

require ROOT_PATH . '/bootstrap/bootstrap.php';
require ROOT_PATH . '/bootstrap/functions.php';

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
            // Extract the date part (YYYY-MM-DD) by removing the time portion
            $datePart = preg_replace('/T.*$/', '', $data['birthday']);

            // Check if the date part is valid (YYYY-MM-DD format)
            $date = \DateTime::createFromFormat('Y-m-d', $datePart);

            if (!$date || $date->format('Y-m-d') !== $datePart) {
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

            $responseData = [];

            $passport = validateAndSaveImage($data['passport']['base64']);

            if ($passport['success'] === false) {
                $this->sendResponse(false, ['errors' => ['passport' => $passport['error']]], 400);
                return;
            }

            $passport_name = $passport['filename'];

            $register = new Register();
            $register->surname = $data['surname'];
            $register->firstname = $data['firstName'];
            $register->gender = strtolower($data['gender']);
            $register->blood_group = $data['bloodGroup'];
            $register->birthday = date('Y-m-d', strtotime($data['birthday']));
            $register->country = $data['nationality'];
            $register->state_of_origin = $data['stateOfOrigin'];
            $register->state_of_residence = $data['stateOfResidence'];
            $register->email = $data['email'];
            $register->phone_number = $data['phoneNumber'];
            $register->contact_address = $data['contactAddress'];
            $register->my_height = $data['myHeight'];
            $register->my_weight = $data['myWeight'];
            $register->emergency_phone_number = $data['emergencyPhoneNumber'];
            $register->passport = $passport_name;
            $register->save();

            $this->sendResponse(true, [
                'message' => 'Registration successful!',
                'data' => $responseData
            ], 201);
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
