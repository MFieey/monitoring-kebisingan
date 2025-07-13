<?php
session_start();

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan."]);
    exit();
}

include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Anda belum login."]);
    exit();
}

if (!isset($_POST['threshold'])) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    exit();
}

$threshold = intval($_POST['threshold']);

// Simpan threshold ke database, misalnya di tabel "settings"
$query = "UPDATE settings SET value = ? WHERE name = 'threshold'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $threshold);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Ambang batas berhasil diperbarui."]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui ambang batas."]);
}
?>
