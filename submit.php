<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'libs/PHPMailer/src/Exception.php';
require 'libs/PHPMailer/src/PHPMailer.php';
require 'libs/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars($_POST['fullname']);
    $phone    = htmlspecialchars($_POST['phone']);
    $reason   = htmlspecialchars($_POST['reason']);
    $email    = htmlspecialchars($_POST['email']);
    $message  = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

        // === 1. ADMIN EMAIL (to you) ===
    $adminTemplate = '
    <div style="background-color: #121212; color: #ffffff; font-family: \'Segoe UI\', sans-serif; padding: 30px; border-radius: 12px; max-width: 600px; margin: auto; box-shadow: 0 0 15px rgba(255,255,255,0.1);">
      <h2 style="font-size: 24px; margin-bottom: 10px; color: #FD6363;">New Contact Form Submission</h2>
      <span style="font-size: 16px; margin-bottom: 20px; color: #cccccc;">
        A new inquiry was submitted through your website. Here are the details:
      </span><br>
      <table style="width: 100%; border-collapse: collapse; border: 1px solid #2c2c2c; border-radius: 8px; overflow: hidden;">
        <thead>
          <tr style="background-color: #1e1e1e;">
            <th align="left" style="padding: 12px; color: #FD6363; font-weight: 600;"><b>Field</b></th>
            <th align="left" style="padding: 12px; color: #FD6363; font-weight: 600;"><b>Value</b></th>
          </tr>
        </thead>
        <tbody>
          <tr><td style="padding: 10px; color: #bbbbbb;">Full Name</td><td style="padding: 10px; color: #ffffff;">'.$fullname.'</td></tr>
          <tr style="background-color: #1a1a1a;"><td style="padding: 10px; color: #bbbbbb;">Contact Number</td><td style="padding: 10px; color: #ffffff;">'.$phone.'</td></tr>
          <tr><td style="padding: 10px; color: #bbbbbb;">Reason</td><td style="padding: 10px; color: #ffffff;">'.$reason.'</td></tr>
          <tr style="background-color: #1a1a1a;"><td style="padding: 10px; color: #bbbbbb;">Email Address</td><td style="padding: 10px; color: #ffffff;">'.$email.'</td></tr>
          <tr><td style="padding: 10px; color: #bbbbbb;">Message</td><td style="padding: 10px; color: #ffffff;">'.$message.'</td></tr>
        </tbody>
      </table><br>
      <span style="margin-top: 30px; font-size: 15px; color: #cccccc;">Please respond to the inquiry at your earliest convenience.</span>
      <br><br><span style="margin-top: 20px; color: #999999;">Regards,<br><strong style="color: #FD6363;">Your Portfolio Website Team</strong></span>
    </div>';

    // === 2. CUSTOMER EMAIL (auto-reply) ===
    $customerTemplate = '
    <div style="background-color:#f9f9f9; padding:20px; border-radius:10px; font-family:Arial, sans-serif; color:#333; max-width:600px; margin:auto;">
      <h2 style="color:#FD6363;">Hi, '.$fullname.'!</h2>
      <p>Thank you for reaching out! I have received your message regarding '.$reason.'</p>
      <p>I will get back to you as soon as possible.</p>
      <p style="margin-top:30px; font-size:14px; color: #FD6363;">Best Regards,<br>Malav Shah</p>
    </div>';

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'malavshahprofessional@gmail.com'; 
        $mail->Password   = 'jnkmfjmkzkpuhkkl'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // --- Send to Admin (You) ---
        $mail->setFrom('malavshahprofessional@gmail.com', 'Portfolio Website');
        $mail->addAddress('malavshahprofessional@gmail.com'); // Your inbox
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission: $reason";
        $mail->Body    = $adminTemplate;
        $mail->send();

        // --- Send Auto-Reply to Customer ---
        $reply = new PHPMailer(true);
        $reply->isSMTP();
        $reply->Host       = 'smtp.gmail.com';
        $reply->SMTPAuth   = true;
        $reply->Username   = 'malavshahprofessional@gmail.com';
        $reply->Password   = 'jnkmfjmkzkpuhkkl';
        $reply->SMTPSecure = 'tls';
        $reply->Port       = 587;

        $reply->setFrom('malavshahprofessional@gmail.com', 'Malav Shah');
        $reply->addAddress($email, $fullname); // Send to customer
        $reply->isHTML(true);
        $reply->Subject = "Thanks for contacting me!";
        $reply->Body    = $customerTemplate;
        $reply->send();

        echo "<script>alert('✅ Message sent successfully! A confirmation email has also been sent to you.'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('❌ Message could not be sent. Error: {$mail->ErrorInfo}'); window.location.href='index.php';</script>";
    }
}
?>
