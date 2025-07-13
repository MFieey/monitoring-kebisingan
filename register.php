<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = strtolower(trim($_POST['role']));

    // Validasi
    if ($password !== $confirm) {
        echo "<script>alert('Konfirmasi password tidak cocok.'); window.history.back();</script>";
        exit;
    }

    // Cek apakah username sudah digunakan
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username sudah terdaftar.'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
