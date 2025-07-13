<?php
session_start();
include 'db_connection.php';
header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT value FROM settings WHERE name='threshold'");
$stmt->execute();
$stmt->bind_result($val);
if ($stmt->fetch()) {
  echo json_encode(["success" => true, "threshold" => floatval($val)]);
} else {
  echo json_encode(["success" => false, "message" => "Ambang batas tidak ditemukan."]);
}
