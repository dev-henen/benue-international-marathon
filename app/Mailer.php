<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Send an email using PHPMailer and a default template.
     *
     * @param string $to      Recipient email
     * @param string $subject Subject of the email
     * @param array  $data    Key-value pairs to replace in the template
     *
     * @return bool True if sent successfully, false otherwise
     */
    public static function send($to, $subject, $data = [])
    {

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'e950bed8fa3a6d';
            $mail->Password = '6fa08997aab70e';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            // Use the local mail() function (default PHP mailer)
            //$mail->isMail();

            // Set sender info
            $mail->setFrom('0f4ed0614d-0cc719+1@inbox.mailtrap.io', 'Benue International Marathon');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;

            // Load the template
            $templatePath = __DIR__ . '/mail_template.html';
            if (!file_exists($templatePath)) {
                throw new \Exception("Email template not found: $templatePath");
            }

            $template = file_get_contents($templatePath);

            // Replace placeholders in the template with provided data
            foreach ($data as $key => $value) {
                $template = str_replace("{{ $key }}", $value, $template);
            }

            $mail->Body = $template;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email failed to send: " . $mail->ErrorInfo);
            return false;
        }
    }
}
