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

// Query gabungan mutasi
$sql = "
  (
    SELECT 
      p.tgl_pembelian AS tanggal,
      p.nomor_faktur,
      dp.id_barang,
      b.nama_barang,
      dp.qty,
      b.satuan,
      dp.harga_satuan,
      dp.sub_total,
      p.total AS total,
      'pembelian' AS jenis_transaksi
    FROM detail_pembelian dp
    JOIN pembelian p ON dp.nomor_faktur = p.nomor_faktur
    JOIN barang b ON dp.id_barang = b.id_barang
  )
  UNION ALL
  (
    SELECT 
      pj.tgl_penjualan AS tanggal,
      pj.nomor_faktur,
      dpj.id_barang,
      b.nama_barang,
      dpj.qty,
      b.satuan,
      dpj.harga_satuan,
      dpj.sub_total,
      pj.total AS total,
      'penjualan' AS jenis_transaksi
    FROM detail_penjualan dpj
    JOIN penjualan pj ON dpj.nomor_faktur = pj.nomor_faktur
    JOIN barang b ON dpj.id_barang = b.id_barang
  )
  ORDER BY tanggal DESC
";

$result = $conn->query($sql);

function rupiah($angka) {
  return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mutasi Barang</title>
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
        <a href="gudang.php" class="logout-btn">Kembali</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <section class="management">
    <div id="barang">
      <h3>Riwayat Mutasi Barang</h3>
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nomor Faktur</th>
            <th>ID Barang</th>
            <th>Nama Barang</th>
            <th>Quantity</th>
            <th>Satuan</th>
            <th>Harga Satuan</th>
            <th>Sub Total</th>
            <th>Total</th>
            <th>Jenis Transaksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result && $result -> num_rows > 0): ?>
            <?php while($row=$result -> fetch_assoc()): ?>
                      <td><?= $row['tanggal']?></td>
                      <td><?= $row['nomor_faktur']?></td>
                      <td><?= $row['id_barang']?></td>
                      <td><?= $row['nama_barang']?></td>
                      <td><?= $row['qty']?></td>
                      <td><?= $row['satuan']?></td>
                      <td><?= rupiah ($row['harga_satuan'])?></td>
                      <td><?= rupiah ($row['sub_total'])?></td>
                      <td><?= rupiah ($row['total'])?></td>
                      <td class="<?= $row['jenis_transaksi'] == 'pembelian' ? 'masuk' : 'keluar' ?>">
                               <?= ucfirst($row['jenis_transaksi']) ?>
                             </>
                           </tr>
                         <?php endwhile; ?>
                       <?php else: ?>
                         <tr><td colspan="10">Tidak ada data mutasi.</td></tr>
                       <?php endif; ?>            
        </tbody>
      </table>
    </div>
    </section>
  </main>
</body>
</html>
