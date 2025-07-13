<?php
session_start();
header("Content-Type: application/json");
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
    exit;
}

$username = $conn->real_escape_string($data["username"]);
$password = $data["password"];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        echo json_encode([
            "success" => true,
            "user" => ["username" => $user['username'], "role" => $user['role']]
        ]);
        exit;
    }
}

http_response_code(401);
echo json_encode(["success" => false, "message" => "Username atau password salah."]);