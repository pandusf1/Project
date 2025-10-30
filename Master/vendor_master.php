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

// === HAPUS DATA ===
if (isset($_GET['delete'])) {
    $id_vendor = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM vendor WHERE id_vendor='$id_vendor'");
}

// === SIMPAN UPDATE ===
if (isset($_POST['update'])) {
    $id_vendor   = $_POST['id_vendor'];
    $nama  = $_POST['nama'];
    $kontak  = $_POST['kontak'];
    $email  = $_POST['email'];
    $alamat= $_POST['alamat'];
    mysqli_query($conn, "UPDATE vendor SET nama='$nama', kontak='$kontak', email='$email', alamat='$alamat' WHERE id_vendor='$id_vendor'");


// setelah update, reload halaman -> form hilang
header("Location: ".$_SERVER['PHP_SELF']);
exit;
}

// === AMBIL DATA UNTUK FORM UPDATE ===
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_vendor_edit = $_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vendor WHERE id_vendor='$id_vendor_edit'"));
}

// === INSERT ===
if (isset($_POST['insert'])) {
    $id_vendor   = $_POST['id_vendor'];
    $nama  = $_POST['nama'];
    $kontak  = $_POST['kontak'];
    $email  = $_POST['email'];
    $alamat= $_POST['alamat'];
    mysqli_query($conn, "INSERT INTO vendor (id_vendor, nama, kontak, email, alamat) 
        VALUES ('$id_vendor','$nama','$kontak', '$email','$alamat')");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Ambil nomor faktur terakhir dari tabel pembelian
$result = mysqli_query($conn, "SELECT MAX(id_vendor) AS last_vendor FROM vendor");
$data = mysqli_fetch_assoc($result);
$last_vendor = $data['last_vendor'];

// Jika belum ada faktur, mulai dari PO001
if ($last_vendor == null) {
    $new_vendor = "VO001";
} else {
    // Ambil angka dari faktur terakhir (misal PO005 → 5)
    $num = (int) substr($last_vendor, 2);
    $num++;
    // Format jadi 3 digit (001, 002, dst)
    $new_vendor = "VO" . str_pad($num, 3, "0", STR_PAD_LEFT);
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
      <h3>Data Vendor</h3>
      <a href="?insert=form#insert" class="logout-btn" style="width:1062px; display: flex; justify-content:center; margin-top: 15px; background-color:#0385F7">Insert Data Baru</a>                        
      <div class="tabslide">
      <table>
        <thead>
          <tr>
            <th>ID Vendor</th>
            <th>Nama Lengkap</th>
            <th>Kontak</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $vendor = $conn->query("SELECT * FROM vendor");
          if ($vendor->num_rows > 0) {
            while ($row = $vendor->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['id_vendor']}</td>
                      <td>{$row['nama']}</td>
                      <td>{$row['kontak']}</td>
                      <td>{$row['email']}</td>
                      <td>{$row['alamat']}</td>
                      <td style='display:flex; justify-content:center'>
                          <a href= '?edit={$row['id_vendor']}#update' class='logout-btn' style='background-color:#FE9900'>Update</a>
                          <a href='?delete={$row['id_vendor']}' class='logout-btn' style='background-color:#B90607' onclick='return confirm(\"Yakin hapus?\")'>Delete</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Belum ada data vendor.</td></tr>";
          }
          ?>
        </tbody>
      </table>
      </div>
      <?php if ($edit_data): ?>
      <hr>
      <h3 id="update">Update Data Vendor</h3>
      <form method="post" class="mb-3" id="update">
          <div class="mb-2">
              <label>ID Vendor</label>
              <input type="text" name="id_vendor" class="form-control" value="<?= $edit_data['id_vendor'] ?>">
          </div>
          <div class="mb-2">
              <label>Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" value="<?= $edit_data['nama'] ?>">
          </div>
          <div class="mb-2">
              <label>Kontak</label>
              <input type="text" name="kontak" class="form-control" value="<?= $edit_data['kontak'] ?>">
          </div>
          <div class="mb-2">    
              <label>Email</label>
              <input type="email" name="email" class="form-control" value="<?= $edit_data['email'] ?>">
          </div>
          <div class="mb-2">
              <label>Alamat</label>
              <input type="text" name="alamat" class="form-control" value="<?= $edit_data['alamat'] ?>">
          </div>
          <button type="submit" name="update" class="logout-btn" style="background-color: #0385F7;">Simpan Perubahan</button>
      </form>
      <?php endif; ?>
      
      <!-- FORM INSERT DATA BARU -->
      <?php if (isset($_GET['insert']) && $_GET['insert']=='form'): ?>
      <hr>
      <h3 id="insert">Tambah Data Vendor</h3>
      <form method="post" class="mb-3">
          <div class="mb-2">
              <label>ID Vendor</label>
              <input type="text" name="id_vendor" class="form-control" value="<?= $new_vendor?>" readonly>
          </div>
          <div class="mb-2">
              <label>Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-2">
              <label>Kontak</label>
              <input type="number" name="kontak" class="form-control" required>
          </div>
          <div class="mb-2">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-2">
              <label>Alamat</label>
              <input type="text" name="alamat" class="form-control" required>
          </div>
          <button type="submit" name="insert" class="logout-btn" style="background-color: #0385F7;">Insert Data</button>
      </form>
      <?php endif; ?>
    </section>

</body>
</html>
