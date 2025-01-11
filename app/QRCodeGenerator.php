<?php

namespace App;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeGenerator {

    /**
     * Generates a QR code from the provided text and returns the temporary file URL.
     *
     * @param string $data The text to encode in the QR code.
     * @return string The temporary file URL where the QR code is stored.
     */
    public static function generateTempFile(string $data): string {
        // Set up QR code options
        $options = new QROptions([
            'eccLevel' => QRCode::ECC_L, // Error correction level
            'outputType' => QRCode::OUTPUT_IMAGE_PNG, // Output format as PNG
            'imageBase64' => false, // No Base64 encoding
        ]);

        // Create a unique temporary filename
        $tempFile = tempnam(sys_get_temp_dir(), 'qrcode_') . '.png';

        // Generate the QR code and save it to the temporary file
        $qrCode = (new QRCode($options))->render($data);
        file_put_contents($tempFile, $qrCode);

        // Return the temporary file URL
        return $tempFile;
    }
}

