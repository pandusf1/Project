<?php
include '../Database/database.php';
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
  header("Location: ../index.php");
  exit();
}

$username = $_SESSION['username'];
$role = "HRD";

// Ambil semua data karyawan untuk dropdown
$karyawan_options = [];
$result = mysqli_query($conn, "SELECT id_karyawan, nama_karyawan, departemen, gaji_per_hari FROM karyawan ORDER BY nama_karyawan ASC");
while ($row = mysqli_fetch_assoc($result)) {
  $karyawan_options[] = $row;
}

// Variabel awal
$nama_karyawan_selected = '';
$id_karyawan_selected = '';
$gaji_per_hari = '';
$departemen = '';

// Jika dropdown dipilih (POST tapi bukan simpan)
if (isset($_POST['nama_karyawan']) && !isset($_POST['simpan'])) {
  $nama_karyawan_selected = $_POST['nama_karyawan'];
  $selected_karyawan = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM karyawan WHERE nama_karyawan = '$nama_karyawan_selected'")
  );
  if ($selected_karyawan) {
    $id_karyawan_selected = $selected_karyawan['id_karyawan'];
    $departemen = $selected_karyawan['departemen'];
    $gaji_per_hari = $selected_karyawan['gaji_per_hari'];
  }
}

// Jika tombol simpan ditekan
if (isset($_POST['simpan'])) {
  $nama_karyawan = $_POST['nama_karyawan'];
  $id_karyawan = $_POST['id_karyawan'];
  $departemen = $_POST['departemen'];
  $gaji_per_hari = $_POST['gaji_per_hari'];
  $jumlah_hari = $_POST['jumlah_hari'];

  // Hitung total gaji
  $total_gaji = $jumlah_hari * $gaji_per_hari;

  // Simpan ke tabel gaji
  $query = "INSERT INTO gaji (id_karyawan, nama_karyawan, departemen, jumlah_hari, gaji)
            VALUES ('$id_karyawan', '$nama_karyawan', '$departemen', '$jumlah_hari', '$total_gaji')";
  mysqli_query($conn, $query);

  // Update kolom gaji di tabel karyawan
  mysqli_query($conn, "UPDATE karyawan SET gaji = '$total_gaji' WHERE id_karyawan = '$id_karyawan'");

  // Redirect ke halaman tabel data
  header("Location: tabelkar.php?success=1");
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input Gaji</title>
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
        <a href="hrd.php" class="logout-btn">Kembali</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <section class="management">
      <h3>Input Gaji Karyawan</h3>

      <form method="post" class="mb-3">
        <!-- Pilih Nama Karyawan -->
        <div class="mb-2">
          <label>Nama Karyawan</label>
          <select name="nama_karyawan" class="form-control" required onchange="this.form.submit()">
            <option value="">-- Pilih Nama Karyawan --</option>
            <?php foreach ($karyawan_options as $karyawan): ?>
              <option value="<?php echo $karyawan['nama_karyawan']; ?>"
                <?php if ($nama_karyawan_selected == $karyawan['nama_karyawan']) echo 'selected'; ?>>
                <?php echo $karyawan['nama_karyawan']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- ID Karyawan -->
        <div class="mb-2">
          <label>ID Karyawan</label>
          <input type="text" name="id_karyawan" class="form-control" value="<?php echo $id_karyawan_selected; ?>" readonly>
        </div>

        <!-- Departemen -->
        <div class="mb-2">
          <label>Departemen</label>
          <input type="text" name="departemen" class="form-control" value="<?php echo $departemen; ?>" readonly>
        </div>

        <!-- Gaji per Hari -->
        <div class="mb-2">
          <label>Gaji per Hari</label>
          <input type="number" name="gaji_per_hari" class="form-control" value="<?php echo $gaji_per_hari; ?>" readonly>
        </div>

        <!-- Jumlah Hari -->
        <div class="mb-2">
          <label>Jumlah Hari Kerja</label>
          <input type="number" name="jumlah_hari" class="form-control" placeholder="contoh: 20" required>
        </div>

        <!-- Tombol Simpan -->
        <button type="submit" name="simpan" class="logout-btn" style="background-color: #0385F7;">Simpan Gaji</button>
      </form>
    </section>
  </main>
</body>
</html>
