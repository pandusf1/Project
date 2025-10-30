<?php
include '../Database/database.php';
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// Simulasi data user gudang
$username = $_SESSION['username'];
$role = "Administrator";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator</title>
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

<div class="center">
  <ul class="nav">
    <li>
      <a href="#">Pembelian</a>
      <ul class="dropdown">
        <li><a href="data_pembelian.php">Data Pembelian</a></li>
        <li><a href="vendor_master.php">Data Vendor</a></li>
      </ul>
    </li>
    <li>
      <a href="#">Penjualan</a>
      <ul class="dropdown">
        <li><a href="data_penjualan.php">Data Penjualan</a></li>
        <li><a href="customer_master.php">Data Customer</a></li>
      </ul>
    </li>
    <li>
      <a href="#">Persediaan</a>
      <ul class="dropdown">
        <li><a href="data_persediaan.php">Data Persediaan</a></li>
        <li><a href="mutasi_master.php">Mutasi Persediaan</a></li>
      </ul>
    </li>
    <li>
      <a href="#">Akuntansi</a>
      <ul class="dropdown">
        <li><a href="jurnal.php">Jurnal Umum</a></li>
        <li><a href="buku_besar.php">Buku Besar</a></li>
        <li><a href="laporan_keuangan.php">Laporan Keuangan</a></li>
      </ul>
    </li>
    <li>
      <a href="#">HRD</a>
      <ul class="dropdown">
        <li><a href="data_karyawan.php">Data Karyawan</a></li>
        <li><a href="absensi.php">Absensi</a></li>
      </ul>
    </li>
  </ul>
</div>
    <div class="right">
      <div class="user-info">
        <div class="user-text">
          <strong><?php echo $role; ?></strong><br>
        </div>
        <a href="../logout.php" class="logout-btn">Keluar</a>
      </div>
    </div>
  </header>

    <section class="management" id="management">
      <h2>Manajemen Toko</h2>
      <p>Grafik</p>
    </section>
</body>
</html>