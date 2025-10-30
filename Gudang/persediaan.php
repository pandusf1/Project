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
$role = "Gudang";

function rupiah($angka) {
    // Jika kosong atau null, anggap 0
    if ($angka == '' || $angka === null) {
        $angka = 0;
    }

    // Pastikan angka dalam bentuk numerik
    $angka = floatval($angka);

    return 'Rp ' . number_format($angka, 0, ',', '.');
}

?>

<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Persediaan</title>
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
        <a href="../gudang/gudang.php" class="logout-btn">Kembali</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <section class="management">
    <div id="barang">
      <h3>Data Persediaan</h3>
      <table>
        <thead>
          <tr>
            <th>ID Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Merk</th>
            <th>Harga Satuan</th>
            <th>Quantity</th>
            <th>Satuan</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $barang = $conn->query("SELECT * FROM barang");
          if ($barang->num_rows > 0) {
            while ($row = $barang->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['id_barang']}</td>
                      <td>{$row['nama_barang']}</td>
                      <td>{$row['kategori']}</td>
                      <td>{$row['merk']}</td>
                      <td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>
                      <td>{$row['qty']}</td>
                      <td>{$row['satuan']}</td>
                      <td>Rp " . number_format($row['sub_total'], 0, ',', '.') . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Belum ada data barang.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
    </section>
  </main>
</body>
</html>
