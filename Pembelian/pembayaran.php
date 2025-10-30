<?php
 include '../database/database.php';


if(isset($_POST['submit'])) {
    $nomor_pembayaran = $_POST['nomor_pembayaran'];
    $nomor_faktur = $_POST['nomor_faktur'];
    $bank = $_POST['bank'];
    $jumlah_dibayar = $_POST['jumlah_dibayar'];
    $status = $_POST['status'];

    // 1️⃣ Simpan ke tabel pembayaran
    $query = "INSERT INTO pembayaran (nomor_pembayaran, nomor_faktur, bank, jumlah_dibayar, status) 
              VALUES ('$nomor_pembayaran', '$nomor_faktur', '$bank', '$jumlah_dibayar','$status')";
    
    if (mysqli_query($conn, $query)) {

        // 2️⃣ Ambil sisa belum bayar dari tabel pembelian
        $result = mysqli_query($conn, "SELECT jmlh_blm_bayar FROM pembelian WHERE nomor_faktur = '$nomor_faktur'");
        $row = mysqli_fetch_assoc($result);
        $sisa = $row['jmlh_blm_bayar'];

        // 3️⃣ Kurangi sesuai jumlah yang dibayar
        $sisa_baru = $sisa - $jumlah_dibayar;
        if ($sisa_baru < 0) $sisa_baru = 0; // biar gak minus

        // 4️⃣ Update jmlh_blm_bayar & status di tabel pembelian
        if ($sisa_baru == 0) {
            $update = "UPDATE pembelian SET jmlh_blm_bayar = 0, status = 'Lunas' WHERE nomor_faktur = '$nomor_faktur'";
        } else {
            $update = "UPDATE pembelian SET jmlh_blm_bayar = '$sisa_baru', status = 'Belum Lunas' WHERE nomor_faktur = '$nomor_faktur'";
        }

        mysqli_query($conn, $update);

        echo "<script>
                alert('Pembayaran berhasil disimpan dan status diperbarui!');
                window.location='pembelian.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil nomor faktur terakhir dari tabel pembelian
$result = mysqli_query($conn, "SELECT MAX(nomor_pembayaran) AS last_pembayaran FROM pembayaran");
$data = mysqli_fetch_assoc($result);
$last_pembayaran = $data['last_pembayaran'];

// Jika belum ada faktur, mulai dari PO001
if ($last_pembayaran == null) {
    $new_pembayaran = "PAY001";
} else {
// Ambil angka dari faktur terakhir (misal PO005 → 5)
    $num = (int) substr($last_pembayaran, 3);
    $num++;
    // Format jadi 3 digit (001, 002, dst)
    $new_pembayaran = "PAY" . str_pad($num, 3, "0", STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Pembayaran</title>
  <link rel="stylesheet" href="../aset/css/invoice.css">
</head>
<body>
<main class="content">
<div class="card">
  <a href="../pembelian/pembelian.php" class="logout-btn">Kembali</a>
  <h2>Form Input Pembayaran</h2>
  <form action="pembayaran.php" method="POST">
    <label>Nomor Pembayaran:</label>
    <input type="text" name="nomor_pembayaran" value="<?= $new_pembayaran ?>" readonly><br><br>

    <label>Nomor Faktur:</label>
  <select name="nomor_faktur" id="nomor_faktur" required>
    <option value="">-- Pilih Nomor Faktur Belum Bayar --</option>
    <?php
    $query_faktur = "SELECT nomor_faktur FROM pembelian WHERE status IN ('Belum Bayar', 'Belum Lunas')";
    $result = mysqli_query($conn, $query_faktur);

    if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['nomor_faktur'] . "'>" . $row['nomor_faktur'] . "</option>";
      }
    } else {
      echo "<option value=''>Tidak ada faktur belum bayar</option>";
    }
    ?>
  </select>

    <label>Bank:</label>
<select name="bank" required>
      <option value="">-- Pilih Bank --</option>
      <?php
      $bank = mysqli_query($conn, "SELECT nama_bank FROM bank ORDER BY nama_bank ASC");
      while ($b = mysqli_fetch_assoc($bank)) {
          echo "<option value='{$b['nama_bank']}'>{$b['nama_bank']}</option>";
      }
      ?>
    </select><br><br>

    <label>Jumlah Dibayar:</label>
    <input type="text" name="jumlah_dibayar"><br><br>

    <label>Status:</label>
    <select name="status" required>
        <option value="">-- Pilih Status Bayar --</option>
        <option value="Belum Lunas">Belum Lunas</option>
        <option value="Lunas">Lunas</option>
    </select><br><br>

    <input class="button" type="submit" name="submit" value="Simpan">
  </form>
</div>
</main>
</body>
</html>
