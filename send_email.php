<?php
// ✅ Ensure headers are sent first
header('Content-Type: application/json');

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // ✅ Initialize environment variables
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad();

        // ✅ Sanitize inputs
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
        $surname = htmlspecialchars($_POST['surname'], ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars($_POST['phone'] ?? 'Not provided', ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars($_POST['subject'] ?? 'No subject', ENT_QUOTES, 'UTF-8');
        $message = nl2br(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8'));

        // 📧 Configure PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)($_ENV['SMTP_PORT'] ?? 587);

        // 📨 Set email content
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Contact Form');
        $mail->addAddress($_ENV['SMTP_ADMIN_EMAIL'], 'Edward Henn');
        $mail->addReplyTo($email, "$name $surname");
        $mail->Subject = "New message from $name $surname";
        $mail->Body = sprintf(
            "Name: %s %s\nPhone: %s\nEmail: %s\nSubject: %s\nMessage:\n%s",
            $name,
            $surname,
            $phone,
            $email,
            $subject,
            $message
        );

        // 🚀 Send email
        if (!$mail->send()) {
            throw new Exception("❌ Failed to send email!");
        }

        // ✅ Success response
        echo json_encode(["success" => true, "message" => "Email sent successfully!"]);

    } catch (Exception $e) {
        // ❌ Error response
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
exit();