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
$role = "HRD";

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

    <div class="right">
      <div class="user-info">
        <div class="user-text">
          <strong><?php echo $role; ?></strong><br>
        </div>
        <a href="../hrd/hrd.php" class="logout-btn">Kembali</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <section class="management">
      <h3>Data Karyawan</h3>
      <a href="?insert=form#insert" class="logout-btn" style="width:1075px; display: flex; justify-content:center; margin-top: 15px; background-color:#0385F7">Insert Data Baru</a>                        
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
            <th>Aksi</th>
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
                      <td>Rp " . number_format($row['gaji'], 0,',','.')."</td>
                      <td id='center'>
                          <a href= '?edit={$row['id_karyawan']}#update' class='logout-btn' style='background-color:#FE9900'>Update</a>
                          <a href='?delete={$row['id_karyawan']}' class='logout-btn' style='background-color:#B90607;margin: top 10px;' onclick='return confirm(\"Yakin hapus?\")'>Delete</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Belum ada data barang.</td></tr>";
          }
          ?>
        </tbody>
      </table>
      <?php if ($edit_data): ?>
      <hr>
      <h3 id="update">Update Data Karyawan</h3>
      <form method="post" class="mb-3" id="update">
          <div class="mb-2">
              <label>ID Karyawan</label>
              <input type="text" name="id_karyawan" class="form-control" value="<?= $edit_data['id_karyawan'] ?>" readonly>
          </div>
          <div class="mb-2">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" value="<?= $edit_data['nama'] ?>">
          </div>
          <div class="mb-2">
              <label>Alamat</label>
              <input type="text" name="alamat" class="form-control" value="<?= $edit_data['alamat'] ?>">
          </div>
          <div class="mb-2">
              <label>Gender</label>
              <select name="gender" class="form-control">
                  <option value="Laki-Laki" <?= ($edit_data['gender'] == 'Laki-Laki') ? 'selected' : '' ?>>Laki-Laki</option>
                  <option value="Perempuan" <?= ($edit_data['gender'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
              </select>    
            </div>
          <div class="mb-2">
              <label>Departemen</label>
              <select name="departemen" class="form-control">
                  <option value="Pembelian" <?= ($edit_data['departemen'] == 'Pembelian') ? 'selected' : '' ?>>Pembelian</option>
                  <option value="Penjualan" <?= ($edit_data['departemen'] == 'Penjualan') ? 'selected' : '' ?>>Penjualan</option>
                  <option value="Gudang" <?= ($edit_data['departemen'] == 'Gudang') ? 'selected' : '' ?>>Gudang</option>
                  <option value="Akuntansi" <?= ($edit_data['departemen'] == 'Akuntansi') ? 'selected' : '' ?>>Akuntansi</option>
                  <option value="HRD" <?= ($edit_data['departemen'] == 'HRD') ? 'selected' : '' ?>>HRD</option>
              </select>    
          </div>
          <button type="submit" name="update" class="logout-btn" style="background-color: #0385F7;">Simpan Perubahan</button>
      </form>
      <?php endif; ?>
      
      <!-- FORM INSERT DATA BARU -->
      <?php if (isset($_GET['insert']) && $_GET['insert']=='form'): ?>
      <hr>
      <h3 id="insert">Tambah Data Mahasiswa Baru</h3>
      <form method="post" class="mb-3">
          <div class="mb-2">
              <label>ID Karyawan</label>
              <input type="text" name="id_karyawan" class="form-control" required>
          </div>
          <div class="mb-2">
              <label>Nama</label>
              <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-2">
              <label>Alamat</label>
              <input type="text" name="alamat" class="form-control" required>
          </div>
          <div class="mb-2">
              <label>Gender</label>
              <select name="gender" class="form-control" required>
                  <option value="">-- Pilih Gender --</option>
                  <option value="Laki-Laki">Laki-Laki</option>
                  <option value="Perempuan">Perempuan</option>
              </select>
          </div>
          <div class="mb-2">
              <label>Departemen</label>
              <select name="departemen" class="form-control" required>
                  <option value="">-- Pilih Departemen --</option>
                  <option value="Pembelian">Pembelian</option>
                  <option value="Penjualan">Penjualan</option>
                  <option value="Gudang">Gudang</option>
                  <option value="Akuntansi">Akuntansi</option>
                  <option value="HRD">HRD</option>
              </select>
          </div>
          <button type="submit" name="insert" class="logout-btn" style="background-color: #0385F7;">Insert Data</button>
      </form>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
