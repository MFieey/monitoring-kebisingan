<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit();
}

require('fpdf186/fpdf.php'); // Pastikan library FPDF sudah terinstal
include 'db_connection.php'; // Ganti jika nama file koneksi berbeda

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Laporan Riwayat Kebisingan', 0, 1, 'C');
$pdf->Ln(5);

// Header kolom
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Waktu', 1);
$pdf->Cell(40, 10, 'Username', 1);
$pdf->Cell(55, 10, 'Status', 1);
$pdf->Cell(55, 10, 'Volume (%)', 1);
$pdf->Ln();

// Ambil data dari tabel noise_history
$query = "SELECT timestamp, username, status, volume FROM noise_history ORDER BY id DESC";
$result = $conn->query($query);

// Isi tabel PDF
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['timestamp'], 1);
    $pdf->Cell(40, 10, $row['username'], 1);
    $pdf->Cell(55, 10, $row['status'], 1);
    $pdf->Cell(55, 10, $row['volume'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'riwayat_kebisingan.pdf');
?>
