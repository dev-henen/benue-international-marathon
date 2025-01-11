<?php
namespace App;

use FPDF;

class SlipGenerator
{
    private $pdf;

    public function __construct()
    {
        // Initialize the FPDF object
        $this->pdf = new FPDF();
        $this->pdf->AddPage();
    }
   /**
     * Generate the slip with the provided design and details.
     *
     * @param array $details - Associative array containing details for the slip.
     */
    public function generateSlip(array $details)
    {
        // Fonts, Colors, and Background
        $this->pdf->SetFont('Arial', 'B', 20);
        $this->pdf->Image(ROOT_PATH . '/public_html/assets/images/hero.png', 5, 5, 200, 287, 'png'); // Background Image
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->Rect(10, 10, 190, 278, 'F');
        $this->pdf->Image(ROOT_PATH . '/public_html/assets/images/logo-dark.png', 15, 15, 28, 18, 'png');

        // Title Section
        $this->pdf->SetFont('Times');
        $this->pdf->Cell(0, 50, strtoupper($details['app_name'] ?? 'APPLICATION NAME'), 0, 1, 'C', false);
        $this->pdf->Cell(0, -25, 'CONFIRMATION SLIP', 0, 1, 'C', false);
        $this->pdf->SetFont('Arial', 'B', 12);

        // Images Section
        $this->pdf->Image($details['passport'] ?? ROOT_PATH . '/public_html/assets/images/profile.png', 15, 45, 30, 30, 'png');
        $this->pdf->Cell(0, 45, '', 0, 0, 'L');
        // $this->pdf->Image($details['qrcode_location'] ?? 'images/default_qrcode.png', 165, 45, 30, 30, 'png');
        // $this->pdf->Cell(0, 45, '', 0, 0, 'L');

        // Personal Details
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->SetTextColor(20, 20, 20);
        $this->pdf->SetDrawColor(200, 200, 200);
        $this->pdf->Ln();
        $this->pdf->SetLeftMargin(13);

        $this->generateRow('SURNAME', $details['surname'] ?? '');
        $this->generateRow('FIRST NAME', $details['first_name'] ?? '', 80, 'RIGHT');
        $this->generateRow('GENDER', $details['gender'] ?? '', 95, 'LEFT', true);
        $this->generateRow('BLOOD GROUP', $details['blood_group'] ?? '', 95, 'CENTER', true);
        $this->generateRow('DATE OF BIRTH', $details['dob'] ?? '', 95, 'RIGHT', true);
        $this->generateRow('STATE OF ORIGIN', $details['state_of_origin'] ?? '', 110, 'LEFT', true);
        $this->generateRow('STATE OF RESIDENCE', $details['state_of_residence'] ?? '', 110, 'CENTER', true);
        $this->generateRow('NATIONALITY', $details['nationality'] ?? '', 110, 'RIGHT', true);
        $this->generateRow('EMAIL', $details['email'] ?? '', 125, 'LEFT');
        $this->generateRow('PHONE NO.', $details['phone'] ?? '', 125, 'RIGHT');
        $this->generateRow('CONTACT ADDRESS', $details['contact_address'] ?? '', 140, 'FULL');
        $this->generateRow('HEIGHT', $details['height'] ?? '', 155, 'LEFT', true);
        $this->generateRow('WEIGHT', $details['weight'] ?? '', 155, 'CENTER', true);
        $this->generateRow('EMERGENCY PHONE NO.', $details['emergency_phone'] ?? '', 155, 'RIGHT', true);
        $this->generateRow('GENDER', $details['gender'] ?? '', 170, 'FULL');

        // Terms & Conditions Section
        $this->pdf->SetY(195);
        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell(184, 4, 'TERMS & CONDITIONS', 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->MultiCell(184, 8, $details['terms_conditions'] ?? '', 1, 'L', 0);
    }

    /**
     * Generate a single row for the slip.
     *
     * @param string $label - The label for the row.
     * @param string $value - The value to display.
     * @param int $y - The Y-coordinate for the row (optional).
     * @param string $alignment - LEFT, CENTER, RIGHT, or FULL alignment (optional).
     */
    private function generateRow(string $label, string $value, int $y = null, string $alignment = 'LEFT', $smallBox = false)
    {
        if ($y !== null) {
            $this->pdf->SetY($y);
        }

        $xPositionMap = [
            'LEFT' => 13,
            'CENTER' => 60,
            'RIGHT' => 107,
            'FULL' => 13,
        ];

        if ($smallBox === true) {
            $xPositionMap['CENTER'] = 78;
            $xPositionMap['RIGHT'] = 142;
        }

        if (isset($xPositionMap[$alignment])) {
            $this->pdf->SetX($xPositionMap[$alignment]);
        }

        $width = 184;
        if ($smallBox === true) {
            $width = 55;
        } else if ($alignment === 'FULL') {
            $width = 184;
        } else {
            $width = 90;
        }

        $this->pdf->SetFont('Arial', 'B', 10);
        $this->pdf->Cell($width, 4, $label, 0, 0, 'L');
        $this->pdf->Ln();

        if (isset($xPositionMap[$alignment])) {
            $this->pdf->SetX($xPositionMap[$alignment]);
        }

        $this->pdf->SetFont('Arial', '', 10);
        $this->pdf->Cell($width, 8, $value, 1, 0, 'L');
        $this->pdf->Ln();
    }
    /**
     * Output the PDF to the browser
     */
    public function outputPDF()
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="slip.pdf"');
        $this->pdf->Output();
    }

    /**
     * Save the PDF to a file
     *
     * @param string $filePath
     */
    public function savePDF($filePath)
    {
        $this->pdf->Output('F', $filePath);
    }

        /**
     * Download the PDF as a file.
     *
     * @param string $fileName - The name of the file to download.
     */
    public function downloadPDF($fileName = 'slip.pdf')
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $this->pdf->Output('D', $fileName);
    }
}
