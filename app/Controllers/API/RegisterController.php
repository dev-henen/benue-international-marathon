<?php

namespace App\Controllers\API;

use App\Models\Register;
use App\PaystackPayment;
use App\ImageHandler;

require ROOT_PATH . '/bootstrap/database.php';

session_start();

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

    private const ALLOWED_BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
    private const ALLOWED_GENDERS = ['male', 'female', 'other'];
    private const MAX_FIELD_LENGTHS = [
        'surname' => 100,
        'firstName' => 100,
        'contactAddress' => 255,
        'email' => 255,
        'phoneNumber' => 20,
        'emergencyPhoneNumber' => 20
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

        // Validate field lengths
        foreach (self::MAX_FIELD_LENGTHS as $field => $maxLength) {
            if (!empty($data[$field]) && mb_strlen($data[$field]) > $maxLength) {
                $errors[$field] = ucfirst($field) . " must be less than $maxLength characters";
            }
        }

        // Validate email
        if (!empty($data['email'])) {
            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                $errors['email'] = 'Invalid email format';
            } else if (mb_strlen($email) > 255) {
                $errors['email'] = 'Email must be less than 255 characters';
            }
        }

        // Validate phone numbers with strict pattern
        $phonePattern = '/^[\+]?[0-9]{10,15}$/';
        if (!empty($data['phoneNumber']) && !preg_match($phonePattern, preg_replace('/\s+/', '', $data['phoneNumber']))) {
            $errors['phoneNumber'] = 'Invalid phone number format';
        }
        if (!empty($data['emergencyPhoneNumber']) && !preg_match($phonePattern, preg_replace('/\s+/', '', $data['emergencyPhoneNumber']))) {
            $errors['emergencyPhoneNumber'] = 'Invalid emergency phone number format';
        }

        // Validate date format and range
        if (!empty($data['birthday'])) {
            $datePart = preg_replace('/T.*$/', '', $data['birthday']);
            $date = \DateTime::createFromFormat('Y-m-d', $datePart);

            if (!$date || $date->format('Y-m-d') !== $datePart) {
                $errors['birthday'] = 'Invalid date format';
            } else {
                $minDate = new \DateTime('-100 years');
                $maxDate = new \DateTime('today');
                if ($date > $maxDate || $date < $minDate) {
                    $errors['birthday'] = 'Birthday must be between 100 years ago and today';
                }
            }
        }

        // Validate blood group
        if (!empty($data['bloodGroup']) && !in_array($data['bloodGroup'], self::ALLOWED_BLOOD_GROUPS, true)) {
            $errors['bloodGroup'] = 'Invalid blood group';
        }

        // Validate gender
        if (!empty($data['gender']) && !in_array(strtolower($data['gender']), self::ALLOWED_GENDERS, true)) {
            $errors['gender'] = 'Invalid gender';
        }

        // Validate height and weight as positive numbers
        if (!empty($data['myHeight']) && (!is_numeric($data['myHeight']) || $data['myHeight'] <= 0 || $data['myHeight'] > 300)) {
            $errors['myHeight'] = 'Height must be a positive number less than 300 cm';
        }
        if (!empty($data['myWeight']) && (!is_numeric($data['myWeight']) || $data['myWeight'] <= 0 || $data['myWeight'] > 500)) {
            $errors['myWeight'] = 'Weight must be a positive number less than 500 kg';
        }

        return $errors;
    }

    private function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                // Remove any HTML tags and encode special characters
                $cleaned = strip_tags($value);
                return htmlspecialchars($cleaned, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            return $value;
        }, $data);
    }

    public function register(): void
    {
        try {
            // Validate Content-Type
            if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                throw new \RuntimeException('Invalid Content-Type. Expected application/json');
            }

            // Get and decode input data with error checking
            $rawData = file_get_contents('php://input');
            if ($rawData === false) {
                throw new \RuntimeException('Failed to read input data');
            }

            // Limit input size to prevent DOS attacks
            if (strlen($rawData) > 1000000) { // 1MB limit
                throw new \RuntimeException('Request body too large');
            }

            $data = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Sanitize input
            $data = $this->sanitizeInput($data);

            // Validate input
            $errors = $this->validateInput($data);
            if (!empty($errors)) {
                $this->sendResponse(false, ['errors' => $errors], 400);
                return;
            }

            $passport = ImageHandler::validateAndSaveImage($data['passport']['base64'], false);

            if ($passport['success'] === false) {
                $this->sendResponse(false, ['errors' => ['passport' => $passport['error']]], 400);
                return;
            }

            // Store only necessary data in session
            $_SESSION['registration_data'] = array_intersect_key($data, array_flip(self::REQUIRED_FIELDS));

            $this->sendResponse(true, [
                'message' => 'Make a payment!',
                'data' => null
            ], 201);
        } catch (\JsonException $e) {
            $this->sendResponse(false, ['message' => 'Invalid JSON format'], 400);
        } catch (\Throwable $e) {
            error_log(sprintf(
                "Registration error: %s\nStack trace: %s",
                $e->getMessage(),
                $e->getTraceAsString()
            ));

            $this->sendResponse(false, [
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    public function verifyRegistration(): void
    {
        try {
            if (!isset($_SESSION['registration_data'])) {
                throw new \RuntimeException('Registration data not found');
            }

            // Validate Content-Type
            if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                throw new \RuntimeException('Invalid Content-Type. Expected application/json');
            }

            $rawData = file_get_contents('php://input');
            if ($rawData === false) {
                throw new \RuntimeException('Failed to read input data');
            }

            if (strlen($rawData) > 1000000) { // 1MB limit
                throw new \RuntimeException('Request body too large');
            }

            $rawData = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);

            if (empty($rawData['reference']) || !is_string($rawData['reference'])) {
                throw new \RuntimeException('Invalid payment reference');
            }

            $data = $_SESSION['registration_data'];
            $transaction_reference = htmlspecialchars($rawData['reference'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $passport = ImageHandler::validateAndSaveImage($data['passport']['base64'], true);
            $passport_name = $passport['filename'];

            // Verify payment with amount validation
            $result = PaystackPayment::verifyTransaction($transaction_reference);
            if ($result['success'] !== true || $result['data']['amount'] !== 200000) {
                throw new \RuntimeException('Transaction verification failed');
            }

            $register = new Register();
            $register->surname = substr($data['surname'], 0, 100);
            $register->firstname = substr($data['firstName'], 0, 100);
            $register->gender = strtolower($data['gender']);
            $register->blood_group = $data['bloodGroup'];
            $register->birthday = date('Y-m-d', strtotime($data['birthday']));
            $register->country = substr($data['nationality'], 0, 100);
            $register->state_of_origin = substr($data['stateOfOrigin'], 0, 100);
            $register->state_of_residence = substr($data['stateOfResidence'], 0, 100);
            $register->email = substr($data['email'], 0, 255);
            $register->phone_number = substr($data['phoneNumber'], 0, 20);
            $register->contact_address = substr($data['contactAddress'], 0, 255);
            $register->my_height = floatval($data['myHeight']);
            $register->my_weight = floatval($data['myWeight']);
            $register->emergency_phone_number = substr($data['emergencyPhoneNumber'], 0, 20);
            $register->passport = $passport_name;
            $register->payment_reference = $transaction_reference;
            $register->reg_year = REGISTRATION_DETAILS['year'];
            $register->save();

            // Clear sensitive data from session
            unset($_SESSION['registration_data']);

            $this->sendResponse(true, [
                'message' => 'Registration created!',
                'data' => null
            ], 201);
        } catch (\JsonException $e) {
            $this->sendResponse(false, ['message' => 'Invalid JSON format'], 400);
        } catch (\Throwable $e) {
            error_log(sprintf(
                "Registration verification error: %s\nStack trace: %s",
                $e->getMessage(),
                $e->getTraceAsString()
            ));
            $this->sendResponse(false, ['message' => 'An unexpected error occurred'], 500);
        }
    }

    private function sendResponse(bool $success, array $data, int $statusCode): void
    {
        // Set security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Content-Security-Policy: default-src \'none\'; frame-ancestors \'none\'');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => $success,
            ...$data
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
