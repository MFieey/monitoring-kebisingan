<?php
session_start();
if ($_SESSION['role'] != 'admin') exit('Akses ditolak');
include 'db_connection.php';

$id = $_GET['id'];
$conn->query("DELETE FROM noise_history WHERE id=$id");
header("Location: admin_dashboard.php");
exit();
