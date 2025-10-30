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
$role = "HRD";
?>

<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HRD</title>
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
          <strong><?php echo $role; ?></strong><br>
        </div>
        <a href="../logout.php" class="logout-btn">Keluar</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <div class="cards">
      <a href="tabelkar.php" style="text-decoration: none;" class="card">
        <div class="icon">📦</div>
        <div>
          <p class="number">Data Karyawan</p>
        </div>
      </a>

      <a href="gaji.php" style="text-decoration:none" class="card">
        <div class="icon">⚠️</div>
        <div>
          <p class="number">Input Gaji</p>
        </div>
      </a>
    </div>
    </main>
</body>
</html>
