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

$sql_beli = 'SELECT * FROM pembelian ORDER BY nomor_faktur DESC;';
$result_beli = $conn->query($sql_beli);

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
      <h2>Riwayat Pembelian</h2>
      <table>
        <thead>
          <tr>
            <th>Nomor Faktur</th>
            <th>Tanggal Pembelian</th>
            <th>ID Vendor</th>
            <th>Nama Barang</th>   
            <th>ID Barang</th>   
            <th>Kategori</th>   
            <th>Kuantitas</th>   
            <th>Harga Satuan</th>   
            <th>Sub Total</th>   
            <th>Total</th>   
            <th>Jumlah Belum Bayar</th>   
            <th>Status</th>   
          </tr>
        </thead>
<tbody>
      <?php
      // 🔍 Query gabungan antara tabel pembelian, detail_pembelian, dan barang
      $query = mysqli_query($conn, "
        SELECT 
            p.nomor_faktur,
            p.tgl_pembelian,
            p.id_vendor,
            b.nama_barang,
            b.id_barang,
            b.kategori,
            d.qty,
            d.harga_satuan,
            d.sub_total,
            p.total,
            p.jmlh_blm_bayar,
            p.status
        FROM pembelian p
        JOIN detail_pembelian d ON p.nomor_faktur = d.nomor_faktur
        JOIN barang b ON d.id_barang = b.id_barang
        ORDER BY p.nomor_faktur DESC
      ");

      if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
          echo "
          <tr>
            <td>{$row['nomor_faktur']}</td>
            <td>{$row['tgl_pembelian']}</td>
            <td>{$row['id_vendor']}</td>
            <td>{$row['nama_barang']}</td>
            <td>{$row['id_barang']}</td>
            <td>{$row['kategori']}</td>
            <td>{$row['qty']}</td>
            <td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['sub_total'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['total'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['jmlh_blm_bayar'], 0, ',', '.') . "</td>
            <td>{$row['status']}</td>
          </tr>
          ";
        }
      } else {
        echo "<tr><td colspan='11'>Belum ada data pembelian.</td></tr>";
      }
      ?>
    </tbody>      
      </table>
    </section>

</body>
</html>
