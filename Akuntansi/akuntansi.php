<?php
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// Simulasi data user gudang
$username = $_SESSION['username'];
$role = "Departemen Akuntansi";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akuntansi</title>
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
      <div class="card">
        <div class="icon">📦</div>
        <div>
          <h3>Total Stok Barang</h3>
          <p class="number">120</p>
        </div>
      </div>

      <div class="card">
        <div class="icon">⚠️</div>
        <div>
          <h3>Stok Menipis</h3>
          <p class="number">8</p>
        </div>
      </div>

      <div class="card">
        <div class="icon">🚚</div>
        <div>
          <h3>Barang Masuk Hari Ini</h3>
          <p class="number">5</p>
        </div>
      </div>
    </div>

    <section class="management">
      <h2>Manajemen Akuntansi</h2>
      <p>Kelola stok, barang masuk, dan barang keluar di gudang.</p>
    </section>
  </main>
</body>
</html>
