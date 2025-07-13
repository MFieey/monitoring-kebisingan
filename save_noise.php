<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start();
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$status = $data['status'] ?? '';
$volume = $data['volume'] ?? 0;
$username = $_SESSION['username'] ?? 'anonymous';

// Ambil threshold dari tabel settings
$threshold = 40; // default
$thresholdResult = $conn->query("SELECT value FROM settings WHERE name = 'threshold' LIMIT 1");
if ($thresholdResult && $thresholdResult->num_rows > 0) {
    $row = $thresholdResult->fetch_assoc();
    $threshold = floatval($row['value']);
}

// Simpan ke tabel noise_history
$stmt = $conn->prepare("INSERT INTO noise_history (username, status, volume, timestamp) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("ssd", $username, $status, $volume);
$stmt->execute();

// Kirim email jika volume melebihi threshold
$emailSent = false;
if ($volume > $threshold) { 

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fikrynj2339@gmail.com';
        $mail->Password   = 'zqondwfztqfnelhx'; // Sandi aplikasi
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('fikrynj2339@gmail.com', 'Noise Monitoring System');
        $mail->addAddress('fikrynj2339@gmail.com');

        $mail->Subject = 'Peringatan Kebisingan Dari Sistem';
        $mail->Body    = "Halo $username,\n\nKebisingan melebihi ambang batas.\nStatus: $status\nVolume: $volume%\nThreshold: $threshold%";

        $mail->send();
        $emailSent = true;
    } catch (Exception $e) {
        error_log("Gagal mengirim email: {$mail->ErrorInfo}");
    }
}

echo json_encode([
    'success' => true,
    'email_sent' => $emailSent
]);


if ($volume > $threshold) {
    // Kirim notifikasi WA
    include 'wa_notif.php'; // isinya skrip pengiriman WA
}
?>
