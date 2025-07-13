<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h2>User Dashboard</h2>
<p>Selamat datang, <?php echo $_SESSION['username']; ?> (User)</p>

<!-- Grafik Kebisingan -->
<canvas id="noiseChart" width="600" height="300"></canvas>
<script>
fetch('noise_data.php')
    .then(response => response.json())
    .then(data => {
        const ctx = document.getElementById('noiseChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(row => row.timestamp),
                datasets: [{
                    label: 'Tingkat Kebisingan (dB)',
                    data: data.map(row => row.noise_level),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    tension: 0.1
                }]
            }
        });
    });
</script>

<p><a href="export_csv.php">Export CSV</a> | <a href="export_pdf.php">Export PDF</a></p>
</body>
</html>
