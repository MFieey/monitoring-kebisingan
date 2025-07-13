<?php
$to = "fikrynj2339@gmail.com";
$subject = "Tes Email dari XAMPP";
$message = "Ini email pengujian dari XAMPP menggunakan sendmail dan Gmail SMTP.";
$headers = "From: Fikry Test <fikrynj2339@gmail.com>\r\n";
$headers .= "Reply-To: fikrynj2339@gmail.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo "Email berhasil dikirim!";
} else {
    echo "Gagal mengirim email.";
}
?>
