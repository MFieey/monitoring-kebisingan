<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style3.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/luxon@3/build/global/luxon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1"></script>

  <?php include 'db_connection.php'; session_start(); ?>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: center;
    }
    th {
      background-color:rgb(91, 86, 93);
    }
    .btn {
      margin: 5px;
      padding: 8px 12px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      text-decoration: none;
    }
    .btn-danger {
      background-color: #e74c3c;
    }
    .btn-warning {
      background-color: #f39c12;
    }
    .pagination {
      margin-top: 10px;
      text-align: center;
    }
    .pagination a {
      margin: 0 5px;
      text-decoration: none;
      padding: 5px 10px;
      background-color: #ddd;
      color: #333;
      border-radius: 4px;
    }
  </style>
</head>
<body>
<div id="app">
  <div id="dashboard">
    <header>
      <h2>Dashboard Admin</h2>
      <p>Selamat datang, <strong><?php echo $_SESSION['username']; ?></strong> (Admin)</p>
      <button onclick="location.href='logout.php'">Keluar</button>
    </header>

    <main>
      <section>
        <h3>Pengaturan Ambang Batas</h3>
        <form method="POST" action="update_threshold.php">
          <input type="number" name="threshold" min="0" max="150" required />
          <button type="submit">Simpan</button>
        </form>
      </section>

      <section>

      <section id="chartSection">
        <h3>Grafik Kebisingan</h3>
        <canvas id="noiseChart" height="150"></canvas>
      </section>

      <section>
        <h3>Export Data</h3>
        <a href="export_csv.php" class="btn">Export CSV</a>
        <a href="export_pdf.php" class="btn">Export PDF</a>
      </section>

      <section>
        <h3>Log Aktivitas</h3>
        <ul>
          <?php
          $result = $conn->query("SELECT * FROM logs ORDER BY timestamp DESC LIMIT 10");
          while ($row = $result->fetch_assoc()) {
              echo "<li>{$row['activity']} - {$row['timestamp']}</li>";
          }
          ?>
        </ul>
      </section>
    </main>

    <aside class="sidebar">
      <section>
        <h3>Manajemen Riwayat Kebisingan</h3>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Status</th>
              <th>Volume</th>
              <th>Waktu</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $res = $conn->query("SELECT * FROM noise_history ORDER BY timestamp DESC LIMIT $offset, $limit");
            while ($row = $res->fetch_assoc()) {
              echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['status']}</td>
                <td>{$row['volume']}%</td>
                <td>{$row['timestamp']}</td>
                <td>
                  <a href='edit_noise.php?id={$row['id']}' class='btn btn-warning'>Edit</a>
                  <a href='delete_noise.php?id={$row['id']}' class='btn btn-danger'>Hapus</a>
                </td>
              </tr>";
            }
            ?>
          </tbody>
        </table>
        <div class="pagination">
          <?php
          $total = $conn->query("SELECT COUNT(*) as total FROM noise_history")->fetch_assoc()['total'];
          $pages = ceil($total / $limit);
          for ($i = 1; $i <= $pages; $i++) {
              echo "<a href='?page=$i'>$i</a>";
          }
          ?>
        </div>
      </section>
    </aside>
  </div>
</div>

<<script>
fetch('noise_data.php')
  .then(res => res.json())
  .then(data => {
    if (!Array.isArray(data)) {
      console.error("Data grafik tidak valid:", data);
      return;
    }

    const ctx = document.getElementById('noiseChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(d => d.timestamp),
        datasets: [{
          label: 'Kebisingan (dB)',
          data: data.map(d => d.volume),
          borderColor: 'rgba(75,192,192,1)',
          backgroundColor: 'rgba(250, 250, 250, 0.85)',
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Grafik Kebisingan' }
        },
        scales: {
          x: {
            type: 'time',
            time: {
              tooltipFormat: 'HH:mm:ss',
              unit: 'minute'
            }
          }
        }
      }
    });
  })
  .catch(err => {
    console.error("Gagal memuat data grafik:", err);
  });
</script>
</body>
</html>