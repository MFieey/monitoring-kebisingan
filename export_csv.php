<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit();
}

include 'db_connection.php';

// Set header untuk CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="noise_history.csv"');

// Buka stream output
$output = fopen('php://output', 'w');

// Header kolom CSV
fputcsv($output, array('Timestamp', 'Username', 'Status', 'Volume (%)'));

// Ambil data dari tabel noise_history
$result = $conn->query("SELECT timestamp, username, status, volume FROM noise_history ORDER BY id DESC");

// Tulis data ke CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Tutup stream
fclose($output);
?>
