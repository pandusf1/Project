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
    $id_karyawan = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM karyawan WHERE id_karyawan='$id_karyawan'");
}

// === SIMPAN UPDATE ===
if (isset($_POST['update'])) {
    $id_karyawan   = $_POST['id_karyawan'];
    $nama  = $_POST['nama'];
    $alamat  = $_POST['alamat'];
    $gender  = $_POST['gender'];
    $departemen= $_POST['departemen'];
    mysqli_query($conn, "UPDATE karyawan SET nama='$nama', alamat='$alamat', gender='$gender', departemen='$departemen' WHERE id_karyawan='$id_karyawan'");


// setelah update, reload halaman -> form hilang
header("Location: ".$_SERVER['PHP_SELF']);
exit;
}

// === AMBIL DATA UNTUK FORM UPDATE ===
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_karyawan_edit = $_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM karyawan WHERE id_karyawan='$id_karyawan_edit'"));
}

// === INSERT ===
if (isset($_POST['insert'])) {
    $id_karyawan   = $_POST['id_karyawan'];
    $nama  = $_POST['nama'];
    $alamat  = $_POST['alamat'];
    $gender  = $_POST['gender'];
    $departemen= $_POST['departemen'];
    mysqli_query($conn, "INSERT INTO karyawan (id_karyawan, nama, alamat, gender, departemen) 
        VALUES ('$id_karyawan','$nama','$alamat', '$gender','$departemen')");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HRD</title>
  <link rel="stylesheet" href="../aset/css/general.css">
</style>
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

  <!-- Konten -->
    <section class="management" id="management">
      <h3>Data Karyawan</h3>
      <table>
        <thead>
          <tr>
            <th>ID Karyawan</th>
            <th>Nama Lengkap</th>
            <th>Alamat</th>
            <th>Gender</th>
            <th>Departemen</th>
            <th>Bergabung Sejak</th>
            <th>Gaji</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $karyawan = $conn->query("SELECT * FROM karyawan");
          if ($karyawan->num_rows > 0) {
            while ($row = $karyawan->fetch_assoc()) {
              echo "<tr>
                      <td style='border-left:1px solid black'>{$row['id_karyawan']}</td>
                      <td>{$row['nama_karyawan']}</td>
                      <td>{$row['alamat']}</td>
                      <td>{$row['gender']}</td>
                      <td>{$row['departemen']}</td>
                      <td>{$row['bergabung_sejak']}</td>
                      <td>{$row['gaji']}</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Belum ada data barang.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
</body>
</html>
