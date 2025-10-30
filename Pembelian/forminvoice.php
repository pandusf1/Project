<?php
 include '../pembelian/invoice.php';

// Ambil nomor faktur terakhir
$result = mysqli_query($conn, "SELECT MAX(nomor_faktur) AS last_faktur FROM pembelian");
$data = mysqli_fetch_assoc($result);
$last_faktur = $data['last_faktur'];

if ($last_faktur == null) {
    $new_faktur = "PO001";
} else {
    $num = (int) substr($last_faktur, 2);
    $num++;
    $new_faktur = "PO" . str_pad($num, 3, "0", STR_PAD_LEFT);
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
  <title>Input Pembelian</title>
  <link rel="stylesheet" href="../aset/css/invoice.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
.suggestion-box div {
  padding: 6px 10px;
  cursor: pointer;
}
.suggestion-box div:hover {
  background: #f0f0f0;
}
</style>

</head>
<body>
<main class="content">
<div class="card">
  <a href="pembelian.php" class="logout-btn">Kembali</a>
  <h2>Input Pembelian</h2>

  <form action="invoice.php" method="POST">

    <label>Nomor Faktur:</label>
    <input type="text" name="nomor_faktur" value="<?= $new_faktur ?>" readonly><br><br>

    <label>Nama Vendor:</label>
    <select name="id_vendor" required>
      <option value="">-- Pilih Nama Vendor --</option>
      <?php
      $cust = mysqli_query($conn, "SELECT id_vendor, nama FROM vendor ORDER BY id_vendor ASC");
      while ($v = mysqli_fetch_assoc($cust)) {
          echo "<option value='{$v['id_vendor']}'>{$v['id_vendor']} - {$v['nama']}</option>";
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
        <th>Sub Total</th>
        <th>Aksi</th>
      </tr>
      <tr>
        <td>
  <input list="daftarBarang" name="nama_barang[]" class="nama_barang" required>
  <datalist id="daftarBarang">
    <?php
    $barangList = mysqli_query($conn, "SELECT nama_barang FROM barang");
    while ($b = mysqli_fetch_assoc($barangList)) {
        echo "<option value='{$b['nama_barang']}'>";
    }
    ?>
  </datalist>
</td>
        <td><input type="text" name="id_barang[]" class="id_barang" readonly></td>
        <td><input type="text" name="kategori[]" class="kategori" required></td>
        <td><input type="text" name="merk[]" class="merk" required></td>
        <td><input type="text" name="satuan[]" class="satuan" required></td>
        <td><input type="number" name="harga_satuan[]" class="harga_satuan" required></td>
        <td><input type="number" name="qty[]" class="qty" required></td>
        <td><input type="number" name="sub_total[]" class="sub_total" required></td>
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

// Ketika user mengetik nama barang
$(document).on('input', '.nama_barang', function () {
  var input = $(this);
  var keyword = input.val();

  if (keyword.length >= 1) {
    $.ajax({
      url: 'get_barang.php',
      method: 'POST',
      data: { keyword: keyword },
      success: function (data) {
        input.next('.suggestion-box').remove(); // hapus suggestion sebelumnya
        input.after('<div class="suggestion-box" style="border:1px solid #ccc; position:absolute; background:#fff; z-index:99;">' + data + '</div>');
      }
    });
  } else {
    input.next('.suggestion-box').remove();
  }
});

// Ketika user klik salah satu suggestion
$(document).on('click', '.suggestion-item', function () {
  var row = $(this).closest('tr');
  var id_barang = $(this).data('id');
  var nama_barang = $(this).data('nama');
  var kategori = $(this).data('kategori');
  var merk = $(this).data('merk');
  var satuan = $(this).data('satuan');
  var harga = $(this).data('harga');

  // isi otomatis ke input
  row.find('.id_barang').val(id_barang);
  row.find('.nama_barang').val(nama_barang);
  row.find('.kategori').val(kategori);
  row.find('.merk').val(merk);
  row.find('.satuan').val(satuan);
  row.find('.harga_satuan').val(harga);

  $('.suggestion-box').remove();
});
</script>
</body>
</html>
