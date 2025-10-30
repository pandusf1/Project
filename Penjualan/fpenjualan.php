<?php
include '../database/database.php';

// Ambil nomor faktur terakhir
$result = mysqli_query($conn, "SELECT MAX(nomor_faktur) AS last_faktur FROM penjualan");
$data = mysqli_fetch_assoc($result);
$last_faktur = $data['last_faktur'];

if ($last_faktur == null) {
    $new_faktur = "SO001";
} else {
    $num = (int) substr($last_faktur, 2);
    $num++;
    $new_faktur = "SO" . str_pad($num, 3, "0", STR_PAD_LEFT);
}

// Ambil parameter asal dari URL (default ke pembelian kalau tidak ada)
$from = isset($_GET['from']) ? $_GET['from'] : 'pembelian';

// Tentukan URL kembali berdasarkan asal
if ($from === 'master') {
    $back_url = '../master/master.php';
} else {
    $back_url = 'pembelian.php';
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Penjualan</title>
  <link rel="stylesheet" href="../aset/css/invoice.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<main class="content">
<div class="card">
  <a href="penjualan.php" class="logout-btn">Kembali</a>
  <h2>Input Penjualan</h2>

  <form action="konpenj.php" method="POST">

    <label>Nomor Faktur:</label>
    <input type="text" name="nomor_faktur" value="<?= $new_faktur ?>" readonly><br><br>

    <label>Nama Customer:</label>
    <select name="id_customer" required>
      <option value="">-- Pilih Nama Customer --</option>
      <?php
      $cust = mysqli_query($conn, "SELECT id_customer, nama_customer FROM customer ORDER BY id_customer ASC");
      while ($v = mysqli_fetch_assoc($cust)) {
          echo "<option value='{$v['id_customer']}'>{$v['id_customer']} - {$v['nama_customer']}</option>";
      }
      ?>
    </select><br><br>

    <h3>Daftar Barang</h3>
    <table id="tabelBarang" border="1" cellpadding="5" cellspacing="0">
      <tr>
        <th>Nama Barang</th>
        <th>ID Barang</th>
        <th>Kategori</th>
        <th>Merk</th>
        <th>Satuan</th>
        <th>Harga</th>
        <th>Qty</th>
        <th>Subtotal</th>
        <th>Aksi</th>
      </tr>
      <tr>
        <td>
          <select name="nama_barang[]" class="nama_barang" required>
            <option value="">-- Pilih Barang --</option>
            <?php
            $barang = mysqli_query($conn, "SELECT nama_barang FROM barang ORDER BY nama_barang ASC");
            while ($b = mysqli_fetch_assoc($barang)) {
                echo "<option value='{$b['nama_barang']}'>{$b['nama_barang']}</option>";
            }
            ?>
          </select>
        </td>
        <td><input type="text" name="id_barang[]" class="id_barang" readonly></td>
        <td><input type="text" name="kategori[]" class="kategori" readonly></td>
        <td><input type="text" name="merk[]" class="merk" readonly></td>
        <td><input type="text" name="satuan[]" class="satuan" readonly></td>
        <td><input type="number" name="harga_satuan[]" class="harga_satuan" readonly></td>
        <td><input type="number" name="qty[]" class="qty" required></td>
        <td><input type="number" name="sub_total[]" class="sub_total" readonly></td>
        <td><button type="button" class="hapusBaris">X</button></td>
      </tr>
    </table>
    <button type="button" id="tambahBarang">+ Tambah Barang</button><br><br>

    <label>Diskon:</label>
    <input type="number" name="diskon" id="diskon" value="0"><br><br>

    <label>Pajak:</label>
    <input type="number" name="pajak" id="pajak" value="0"><br><br>

    <label>Ongkir:</label>
    <input type="number" name="ongkir" id="ongkir" value="0"><br><br>

    <label>Total:</label>
    <input type="number" name="total" id="total" readonly><br><br>

    <label>Status:</label>
    <select name="status">
      <option value="Belum Bayar">Belum Bayar</option>
      <option value="Belum Lunas">Belum Lunas</option>
      <option value="Lunas">Lunas</option>
    </select>

    <label>Termin Pembayaran:</label>
    <select name="termin" required>
      <option value="">-- Pilih Termin Pembayaran --</option>
      <?php
      $termin = mysqli_query($conn, "SELECT termin FROM termin ORDER BY termin ASC");
      while ($t = mysqli_fetch_assoc($termin)) {
          echo "<option value='{$t['termin']}'>{$t['termin']}</option>";
      }
      ?>
    </select><br><br>

    <input class="button" type="submit" name="submit" value="Simpan">
  </form>
</div>
</main>

<script>
$(document).on('change', '.nama_barang', function() {
  var row = $(this).closest('tr');
  var nama_barang = $(this).val();
  if (nama_barang != "") {
    $.ajax({
      url: "get_barang.php",
      type: "POST",
      data: { nama_barang: nama_barang },
      dataType: "json",
      success: function(data) {
        row.find('.id_barang').val(data.id_barang);
        row.find('.kategori').val(data.kategori);
        row.find('.merk').val(data.merk);
        row.find('.satuan').val(data.satuan);
        row.find('.harga_satuan').val(data.harga_satuan);
      }
    });
  }
});

$(document).on('input', '.qty', function() {
  var row = $(this).closest('tr');
  var qty = parseFloat(row.find('.qty').val()) || 0;
  var harga = parseFloat(row.find('.harga_satuan').val()) || 0;
  var subtotal = qty * harga;
  row.find('.sub_total').val(subtotal.toFixed(2));
  hitungTotal();
});

$('#diskon, #pajak, #ongkir').on('input', hitungTotal);

function hitungTotal() {
  let totalBarang = 0;
  $('.sub_total').each(function() {
    totalBarang += parseFloat($(this).val()) || 0;
  });
  const diskon = parseFloat($('#diskon').val()) || 0;
  const pajak = parseFloat($('#pajak').val()) || 0;
  const ongkir = parseFloat($('#ongkir').val()) || 0;
  const total = totalBarang - diskon + pajak + ongkir;
  $('#total').val(total.toFixed(2));
}

$('#tambahBarang').click(function() {
  let baris = $('#tabelBarang tr:eq(1)').clone();
  baris.find('input').val('');
  baris.find('select').val('');
  $('#tabelBarang').append(baris);
});

$(document).on('click', '.hapusBaris', function() {
  if ($('#tabelBarang tr').length > 2) {
    $(this).closest('tr').remove();
    hitungTotal();
  } else {
    alert('Minimal satu barang.');
  }
});
</script>
</body>
</html>
