<?php
session_start();
if ($_SESSION['role'] != 'admin') exit('Akses ditolak');
include 'db_connection.php';

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $volume = $_POST['volume'];
    $stmt = $conn->prepare("UPDATE noise_history SET status=?, volume=? WHERE id=?");
    $stmt->bind_param("sdi", $status, $volume, $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

$result = $conn->query("SELECT * FROM noise_history WHERE id=$id");
$data = $result->fetch_assoc();
?>
<link rel="stylesheet" href="style3.css">
<h2>Edit Data Kebisingan</h2>
<form method="POST">
  Status: <input name="status" value="<?= $data['status'] ?>"><br>
  Volume: <input name="volume" type="number" value="<?= $data['volume'] ?>"><br>
  <button type="submit">Simpan</button>
</form>
