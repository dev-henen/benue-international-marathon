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
            if($_ENV['MAIL_DRIVER'] == 'smtp') {
                $mail->isSMTP();
    
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = filter_var($_ENV['MAIL_SMTP_AUTH'], FILTER_VALIDATE_BOOLEAN);
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
                $mail->Port = $_ENV['MAIL_PORT'];
            } else {
                $mail->isMail();
            }

            // Set sender info
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;

            // Load the template
            $templatePath = __DIR__ . '/mail_template.html';
            if (!file_exists($templatePath)) {
                throw new \Exception("Email template not found: $templatePath");
            }

            $template = file_get_contents($templatePath);

            if(empty($data['action_link'])) {
                $data['action_link'] = 'https://benueinternationalmarathon.com';
            }
            if(empty($data['action_text'])) {
                $data['action_text'] = 'Visit our website';
            }

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
