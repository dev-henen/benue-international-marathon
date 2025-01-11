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
                'terms_conditions' => 'I declare my understanding that running is a potentially Hazardous activity in which I am only permitted to participate in with sufficient training and a good level of overall health. I hereby accept all risks involved which may include injury or death. I agree to abide by the T & C as well as rules and regulations of TUM 2025. I grant permission to TUM and unicentral R.G Nigeria LTD for the use of my personal data provided by registration and any of my Photographs, passports or Recordings of me and others recorded during this event for any legitimate purpose. ',
                'app_name' => 'Benue International Marathon ' . REGISTRATION_DETAILS['year'],
            ]);

            // Output the PDF
            $slip->downloadPDF();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            http_response_code(400); // Return 400 for client error
            echo "Error: " . $e->getMessage();
        }
    }

    public function verify() {
        global $twig;
        echo $twig->render('verify-slip.twig',);
    }
}
