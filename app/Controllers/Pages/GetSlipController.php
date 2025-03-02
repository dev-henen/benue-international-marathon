<?php

namespace App\Controllers\Pages;

use App\Models\Register;
use App\SimpleEncryption;
use App\SlipGenerator;
use App\QRCodeGenerator;

require ROOT_PATH . '/bootstrap/database.php';

class GetSlipController
{
    public function index()
    {
        global $twig;
        echo $twig->render('get-slip.twig',);
    }

    public function download(): void
    {

        try {
            $encrypted_hash = $_GET['hash'] ?? null;

            if (!$encrypted_hash) {
                throw new \Exception("No hash provided.");
            }

            $encryptionKey = ENCRYPTION_KEY;
            $encryption = new SimpleEncryption($encryptionKey);
            $decrypted_data = $encryption->decrypt($encrypted_hash);

            $encrypted_link = json_decode($decrypted_data);

            if (!$encrypted_link) {
                throw new \Exception("Decryption failed or invalid JSON.");
            }

            $registration_id = $encrypted_link->id ?? null;
            $link_expiration_time = $encrypted_link->link_expiration_time ?? null;

            // Validate required fields
            if (!$registration_id || !$link_expiration_time) {
                throw new \Exception("Missing required fields in the decrypted link.");
            }

            // Check if the link has expired
            $current_time = time();
            if ($current_time > $link_expiration_time) {
                throw new \Exception("The link has expired.");
            }


            $registration = Register::where('id', $registration_id)
                ->first();
            if (!$registration) {
                throw new \Exception("Invalid registration ID.");
            }

            $encryptionKey = ENCRYPTION_KEY;
            $encryption = new SimpleEncryption($encryptionKey);
            $encrypted_id = $encryption->encrypt($registration->id);

            // Initialize the SlipGenerator
            $slip = new SlipGenerator();
            $tempQRCodeUrl = QRCodeGenerator::generateTempFile($encrypted_id);

            // Generate the slip with details
            $slip->generateSlip([
                'surname' => $registration->surname,
                'first_name' => $registration->firstname,
                'dob' => date('d/m/Y', strtotime($registration->birthday)),
                'nationality' => ucwords($registration->country),
                'state_of_origin' => $registration->state_of_origin,
                'state_of_residence' => $registration->state_of_residence,
                'email' => $registration->email,
                'contact_address' => $registration->contact_address,
                'phone' => $registration->phone_number,
                'emergency_phone' => $registration->emergency_phone_number,
                'height' => $registration->my_height,
                'weight' => $registration->my_weight,
                'gender' => ucwords($registration->gender),
                'blood_group' => $registration->blood_group,
                'passport' => ROOT_PATH . '/public_html/uploads/' . $registration->passport,
                'qrcode_location' => $tempQRCodeUrl,
                'terms_conditions' => 'By participating in this event, I acknowledge that running is a potentially hazardous activity that requires adequate training and a good overall level of health. I understand and assume all risks associated with participation, including the possibility of injury or death. I agree to comply with the Terms and Conditions, rules, and regulations of Benue International Marathon 2025. I hereby grant permission to Benue International Marathon LTD to collect, use, and disclose my personal data, photographs, passport, and recordings taken during the event for legitimate purposes."',
                'app_name' => 'Benue International Marathon ' . REGISTRATION_DETAILS['year'],
            ]);

            // Output the PDF
            $slip->downloadPDF();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            http_response_code(400); // Return 400 for client error
            echo "Oops! They was an error generating your slip. Please try again later.";
        }
    }

    public function verify() {
        global $twig;
        echo $twig->render('verify-slip.twig',);
    }
}
