<?php 
include '../database/database.php';
session_start();

// Pastikan karyawan sudah login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: ../index.php");
    exit;
}

// Atur timezone ke WIB
date_default_timezone_set('Asia/Jakarta');
$conn->query("SET time_zone = '+07:00'");


// Ambil data dari session
$id_karyawan = $_SESSION['id_karyawan'];
$nama = $_SESSION['nama_karyawan'];
$departemen = $_SESSION['departemen'];
$nama_user = $_SESSION['username'];


// Ambil waktu sekarang
$tanggal = date('Y-m-d');
$jam = date('H:i:s');
// Jika tombol Absen Keluar ditekan
if (isset($_POST['absen_keluar'])) {
    $sql = "UPDATE absensi SET jam_keluar='$jam'
            WHERE id_karyawan='$id_karyawan' AND tanggal='$tanggal'";
    $conn->query($sql);

    // Setelah berhasil → kembali ke halaman karyawan.php
    header("Location: karyawan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Absensi Karyawan</title>
  <link rel="stylesheet" href="../aset/css/absensi.css">
  <script>
    function tampilkanForm() {
      const masuk = document.getElementById("masuk");
      const keluar = document.getElementById("keluar");
      const formMasuk = document.getElementById("formMasuk");
      const formKeluar = document.getElementById("formKeluar");

      if (masuk.checked) {
        formMasuk.classList.add("active");
        formKeluar.classList.remove("active");
      } else if (keluar.checked) {
        formKeluar.classList.add("active");
        formMasuk.classList.remove("active");
      }
    }
  </script>

</head>
<body>
<main class="content">
<div class="card">
  <a href="../karyawan/karyawan.php" class="logout-btn">Kembali</a>
  <h2>Form Absensi Karyawan</h2>
  <p>Halo, <b><?= htmlspecialchars($nama); ?></b></p>
  <p>Tanggal: <?= $tanggal; ?> — Waktu: <?= $jam; ?> WIB</p>

  <div class="options">
    <label><input type="radio" name="pilihan" id="masuk" onclick="tampilkanForm()"> Absen Masuk</label>
    <label><input type="radio" name="pilihan" id="keluar" onclick="tampilkanForm()"> Absen Keluar</label>
  </div>

  <!-- Form Absen Masuk -->
  <form method="POST" id="formMasuk">
    <input type="hidden" name="id_karyawan" value="<?= $id_karyawan; ?>">
    <label>Nama Karyawan:</label>
    <input type="text" name="nama_karyawan" value="<?= $nama; ?>" readonly>

    <label>Departemen:</label>
    <input type="text" name="departemen" value="<?= $departemen; ?>" readonly>

    <button type="submit" name="absen_masuk">Absen Masuk</button>
  </form>

  <!-- Form Absen Keluar -->
  <form method="POST" id="formKeluar">
    <input type="hidden" name="id_karyawan" value="<?= $id_karyawan; ?>">
    <label>Nama Karyawan:</label>
    <input type="text" name="nama_karyawan" value="<?= $nama; ?>" readonly>

    <label>Departemen:</label>
    <input type="text" name="departemen" value="<?= $departemen; ?>" readonly>

    <button type="submit" name="absen_keluar">Absen Keluar</button>
  </form>
  <?php
  // ========== PROSES ABSEN MASUK ==========
  if (isset($_POST['absen_masuk'])) {
      $tgl = date("Y-m-d");
      $jam = date("H:i:s");

      // Cek apakah sudah absen hari ini
      $cek = $conn->query("SELECT * FROM absensi WHERE id_karyawan='$id_karyawan' AND tanggal='$tgl'");
      if ($cek->num_rows == 0) {
          $conn->query("INSERT INTO absensi (id_karyawan, nama_karyawan, departemen, tanggal, jam_masuk)
                        VALUES ('$id_karyawan', '$nama', '$departemen', '$tgl', '$jam')");
          header("location:karyawan.php");
      } else {
          echo "<p style='color:red; margin-top:10px;'>⚠️ Anda sudah absen masuk hari ini.</p>";
      }
  }

  // ========== PROSES ABSEN KELUAR ==========
  if (isset($_POST['absen_keluar'])) {
      $tgl = date("Y-m-d");
      $jam = date("H:i:s");

      // Update jam keluar
      $update = $conn->query("UPDATE absensi SET jam_keluar='$jam' 
                              WHERE id_karyawan='$id_karyawan' AND tanggal='$tgl'");

      if ($conn->affected_rows > 0) {
          echo "<p style='color:blue; margin-top:10px;'>✅ Absen keluar berhasil!</p>";
      } else {
          echo "<p style='color:red; margin-top:10px;'>⚠️ Anda belum absen masuk hari ini.</p>";
      }
  }

  ?>
  </main>
</div>

</body>
</html>
