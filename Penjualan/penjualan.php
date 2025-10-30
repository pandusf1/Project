<?php
include '../Database/database.php';
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// Simulasi data user Penjualan
$username = $_SESSION['username'];
$role = "Departemen Penjualan";

$sql_jual = 'SELECT * FROM penjualan ORDER BY nomor_faktur DESC;';
$result_jual = $conn->query($sql_jual);

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

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penjualan</title>
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
      <a href="fpenjualan.php" class="card" style="text-decoration:none">
        <div class="icon">🪙</div>
        <div>
          <p class="number">Penjualan</p>
        </div>
      </a>

      <a href="customer.php" style="text-decoration:none" class="card">
        <div class="icon">⚠️</div>
        <div>
          <p class="number">Customer</p>
        </div>
      </a>
      </div>
  </main>
    <section class="management">
      <h2>Riwayat Penjualan</h2>
      <table>
        <thead>
          <tr>
            <th>Nomor Faktur</th>
            <th>Tanggal Penjualan</th>
            <th>ID Customer</th>
            <th>Nama Barang</th>   
            <th>ID Barang</th>   
            <th>Kategori</th>   
            <th>Kuantitas</th>   
            <th>Harga Satuan</th>   
            <th>Sub Total</th>   
            <th>Total</th>   
            <th>Status</th>   
          </tr>
        </thead>
<tbody>
      <?php
      // 🔍 Query gabungan antara tabel penjualan, detail_penjualan, dan barang
      $query = mysqli_query($conn, "
        SELECT 
            p.nomor_faktur,
            p.tgl_penjualan,
            p.id_customer,
            b.nama_barang,
            b.id_barang,
            b.kategori,
            d.qty,
            d.harga_satuan,
            d.sub_total,
            p.total,
            p.status
        FROM penjualan p
        JOIN detail_penjualan d ON p.nomor_faktur = d.nomor_faktur
        JOIN barang b ON d.id_barang = b.id_barang
        ORDER BY p.nomor_faktur DESC
      ");

      if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
          echo "
          <tr id='tr' onclick=\"window.location.href='detail_penjualan.php?nomor_faktur=" . urlencode($row['nomor_faktur']) . "'\" style='cursor:pointer;'>
            <td>{$row['nomor_faktur']}</td>
            <td>{$row['tgl_penjualan']}</td>
            <td>{$row['id_customer']}</td>
            <td>{$row['nama_barang']}</td>
            <td>{$row['id_barang']}</td>
            <td>{$row['kategori']}</td>
            <td>{$row['qty']}</td>
            <td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['sub_total'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>
            <td>{$row['status']}</td>
          </tr>
          ";
        }
      } else {
        echo "<tr><td colspan='11'>Belum ada data penjualan.</td></tr>";
      }
      ?>
    </tbody>      
  </table>
    </section>
</body>
</html>
