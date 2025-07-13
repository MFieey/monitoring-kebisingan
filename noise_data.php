<?php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit();
}

$query = "SELECT timestamp, volume FROM noise_history ORDER BY timestamp DESC LIMIT 50";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'timestamp' => $row['timestamp'],
        'volume' => (float)$row['volume']
    ];
}

echo json_encode(array_reverse($data)); // agar urut dari awal ke terbaru

if ($volume > $threshold) {
    // Kirim notifikasi WA
    include 'wa_notif.php'; // isinya skrip pengiriman WA
}
?>