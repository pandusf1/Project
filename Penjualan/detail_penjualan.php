<?php
include '../Database/database.php';
session_start();

// ✅ Cek login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// ✅ Ambil nomor faktur dari URL
$nomor_faktur = $_GET['nomor_faktur'] ?? '';

if ($nomor_faktur == '') {
  die("<h3>❌ Nomor faktur tidak ditemukan di URL.</h3><a href='penjualan.php'>Kembali</a>");
}

// ✅ Ambil data header penjualan
$sql = "SELECT * FROM penjualan WHERE nomor_faktur = '$nomor_faktur'";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
  die("<h3>❌ Data penjualan tidak ditemukan untuk faktur <b>$nomor_faktur</b>.</h3><a href='penjualan.php'>Kembali</a>");
}

$data = $result->fetch_assoc();

// ✅ Ambil data detail barang
$sql_detail = "
  SELECT 
    dp.id_barang,
    b.nama_barang,
    b.kategori,
    b.merk,
    dp.qty,
    b.satuan,
    dp.harga_satuan,
    dp.sub_total
  FROM detail_penjualan dp
  INNER JOIN barang b ON dp.id_barang = b.id_barang
  WHERE dp.nomor_faktur = '$nomor_faktur'
";
$detail_result = $conn->query($sql_detail);

// ✅ Fungsi format rupiah
function rupiah($angka) {
  return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Penjualan <?= htmlspecialchars($nomor_faktur) ?></title>
  <link rel="stylesheet" href="../aset/css/detail.css">
<link rel="stylesheet" href="../aset/css/detail.css"></head>
<body>
  <div class="box">
    <a href="penjualan.php" class="logout-btn">Kembali</a>
  </div>

  <section class="management">
    <h2>Nomor Faktur: <?= htmlspecialchars($data['nomor_faktur']) ?></h2>

    <div class="row">
      <p><b>Tanggal Penjualan:</b> <?= htmlspecialchars($data['tgl_penjualan']) ?></p>
      <p><b>Status:</b> <?= htmlspecialchars($data['status']) ?></p>
    </div>
    <div class="row row-2">
      <p><b>ID Customer:</b> <?= htmlspecialchars($data['id_customer']) ?></p>
      <p><b>Termin:</b> <?= htmlspecialchars($data['termin']) ?></p>
    </div>

    <h3>DETAIL BARANG:</h3>
    <table>
      <thead>
        <tr>
          <th>ID Barang</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Merk</th>
          <th>Qty</th>
          <th>Satuan</th>
          <th>Harga Satuan</th>
          <th>Sub Total</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($detail_result && $detail_result->num_rows > 0): ?>
          <?php while ($row = $detail_result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id_barang']) ?></td>
              <td><?= htmlspecialchars($row['nama_barang']) ?></td>
              <td><?= htmlspecialchars($row['kategori']) ?></td>
              <td><?= htmlspecialchars($row['merk']) ?></td>
              <td><?= htmlspecialchars($row['qty']) ?></td>
              <td><?= htmlspecialchars($row['satuan']) ?></td>
              <td><?= rupiah($row['harga_satuan']) ?></td>
              <td><?= rupiah($row['sub_total']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8">Tidak ada data barang untuk faktur ini.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <table class="summary">
      <tr><td>Diskon</td><td class="right">: <?= rupiah($data['diskon'] ?? 0) ?></td></tr>
      <tr><td>Pajak</td><td class="right">: <?= rupiah($data['pajak'] ?? 0) ?></td></tr>
      <tr><td>Ongkir</td><td class="right">: <?= rupiah($data['ongkir'] ?? 0) ?></td></tr>
      <tr class="total"><td><b>TOTAL</b></td><td class="right"><b>: <?= rupiah($data['total'] ?? 0) ?></b></td></tr>
    </table>

    <div style="clear:both;"></div>
  </section>

    <div style="text-align:center;">
      <button id="cetakPDF">Cetak PDF</button>
    </div>

  <!-- ✅ Tambahkan library html2pdf -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
    const tombolCetak = document.getElementById("cetakPDF");
    const elemenInvoice = document.querySelector(".management");

    tombolCetak.addEventListener("click", async function() {
      if (!elemenInvoice) {
        alert("Elemen invoice tidak ditemukan!");
        return;
      }

      // Tunggu layout stabil
      await new Promise(resolve => setTimeout(resolve, 400));

      const opt = {
        margin: 0.3,
        filename: 'Invoice_<?= htmlspecialchars($data['nomor_faktur']) ?>.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 2, useCORS: true, backgroundColor: "#ffffff" },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
      };

      html2pdf()
        .set(opt)
        .from(elemenInvoice)
        .toPdf()
        .get('pdf')
        .then(function(pdf) {
          const blob = pdf.output('blob');
          const blobUrl = URL.createObjectURL(blob);
          const win = window.open(blobUrl, '_blank');
          if (!win) {
            alert('Izinkan pop-up di browser agar bisa membuka PDF.');
          }
        });
    });
  });
  </script>

</body>
</html>
