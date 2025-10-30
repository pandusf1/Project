<?php
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// Simulasi data user gudang
$username = $_SESSION['username'];
$role = "Departemen Gudang";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gudang</title>
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
      <a href="persediaan.php" style="text-decoration: none;" class="card">
        <div class="icon">📦</div>
        <div>
          <p class="number">Persediaan</p>
        </div>
      </a>

      <a href="mutasi-barang.php" style="text-decoration:none" class="card">
        <div class="icon">⚠️</div>
        <div>
          <p class="number">Mutasi Barang</p>
        </div>
      </a>
    </div>
  </main>
    <section class="management">
      <h2>Manajemen Gudang</h2>
      <p>Kelola stok, barang masuk, dan barang keluar di gudang.</p>
    </section>
</body>
</html>
