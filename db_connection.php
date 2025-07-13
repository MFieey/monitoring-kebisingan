<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Ganti dengan password MySQL Anda jika ada
$db_name = 'noise_monitoring_3'; // Ganti dengan nama database Anda

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
