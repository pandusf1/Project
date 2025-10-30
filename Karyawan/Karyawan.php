<?php
session_start();
include '../database/database.php';

// Cek login dulu
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// Ambil username dari session
$username = $_SESSION['username'];


// Ambil nama dari tabel karyawan (berdasarkan nama = username)
$sql = "SELECT karyawan.nama_karyawan
        FROM users
        INNER JOIN karyawan ON karyawan.id_user = users.id
        WHERE users.username = '$username'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $nama = $data['nama_karyawan'];
} else {
    $nama = "Nama tidak ditemukan";
}

$role = "Departemen Pembelian";
// Ambil data absensi dari tabel absensi berdasarkan nama karyawan yang login
$sql_absen = "SELECT tanggal, jam_masuk, jam_keluar, status 
              FROM absensi 
              WHERE nama_karyawan = '$nama'
              ORDER BY tanggal DESC";
$result_absen = $conn->query($sql_absen);

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Karyawan</title>
  <link rel="stylesheet" href="../aset/css/general.css">
</head>
<body>
  <!-- Header -->
  <header class="navbar">
    <div class="left">
      <div class="logo">HC</div>
      <div class="title">
        <h1>Hitech Computer</h1>
      </div>
    </div>

    <div class="right">
      <div class="user-info">
        <div class="user-text">
          <strong><?php echo htmlspecialchars($nama); ?></strong></a><br>
        </div>
        <a href="../logout.php" class="logout-btn">Keluar</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <div class="cards">
      <a href="absen.php" class="card" style="text-decoration: none;">
        <div class="icon">📅</div>
        <div>
          <p class="number">Absensi</p>
        </div>
      </a>

      <a href="data_kar.php" class="card" style="text-decoration: none;">
        <div class="icon">🙍</div>
        <div>
          <p class="number">Data Diri</p>
        </div>
      </a>
    </div>
  </main>


    <section class="management">
      <h2>Riwayat Absensi</h2>
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Waktu Masuk</th>
            <th>Waktu Keluar</th>
            <th>Status</th>
          </tr>
        </thead>
<tbody>
  <?php
  if ($result_absen && $result_absen->num_rows > 0) {
      while ($row = $result_absen->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
          echo "<td>" . htmlspecialchars($row['jam_masuk'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($row['jam_keluar'] ?? '-') . "</td>";
          echo "<td>" . htmlspecialchars($row['status']) . "</td>";
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='4' style='text-align:center;'>Belum ada data absensi.</td></tr>";
  }
  ?>
</tbody>
      </table>
    </section>
</body>
</html>
