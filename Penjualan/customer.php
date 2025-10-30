<?php
include '../Database/database.php';
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit();
}

// Simulasi data user
$username = $_SESSION['username'];
$role = "Departemen Penjualan";

// === HAPUS DATA ===
if (isset($_GET['delete'])) {
    $id_customer = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM customer WHERE id_customer='$id_customer'");
}

// === SIMPAN UPDATE ===
if (isset($_POST['update'])) {
    $id_customer = $_POST['id_customer'];
    $nama_customer = $_POST['nama_customer'];
    $kontak = $_POST['kontak'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    mysqli_query($conn, "UPDATE customer SET nama_customer='$nama_customer', kontak='$kontak', email='$email', alamat='$alamat' WHERE id_customer='$id_customer'");

    // setelah update, reload halaman -> form hilang
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// === AMBIL DATA UNTUK FORM UPDATE ===
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_customer_edit = $_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customer WHERE id_customer='$id_customer_edit'"));
}

// === INSERT ===
if (isset($_POST['insert'])) {
    $id_customer = $_POST['id_customer'];
    $nama_customer = $_POST['nama_customer'];
    $kontak = $_POST['kontak'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    mysqli_query($conn, "INSERT INTO customer (id_customer, nama_customer, kontak, email, alamat) 
        VALUES ('$id_customer','$nama_customer','$kontak', '$email','$alamat')");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Ambil nomor terakhir (angka dari id_customer)
$result = mysqli_query($conn, "
    SELECT MAX(CAST(SUBSTRING(id_customer, 3) AS UNSIGNED)) AS last_customer 
    FROM customer
");
$data = mysqli_fetch_assoc($result);
$last_customer = $data['last_customer'];

// Jika belum ada data, mulai dari CU001
if ($last_customer == null) {
    $new_customer = "CU001";
} else {
    $num = $last_customer + 1;
    $new_customer = "CU" . str_pad($num, 3, "0", STR_PAD_LEFT);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Customer</title>
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
        <a href="penjualan.php" class="logout-btn">Kembali</a>
      </div>
    </div>
  </header>

  <!-- Konten -->
  <main class="content">
    <section class="management">
      <h3>Data Customer</h3>
      <a href="?insert=form#insert" class="logout-btn" style="width:1075px; display: flex; justify-content:center; margin-top: 15px; background-color:#0385F7">Insert Data Baru</a>                        
      <div class="tabslide">
      <table>
        <thead>
          <tr>
            <th>ID Customer</th>
            <th>Nama Customer</th>
            <th>Kontak</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $customer = $conn->query("SELECT * FROM customer");
          if ($customer->num_rows > 0) {
            while ($row = $customer->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['id_customer']}</td>
                      <td>{$row['nama_customer']}</td>
                      <td>{$row['kontak']}</td>
                      <td>{$row['email']}</td>
                      <td>{$row['alamat']}</td>
                      <td style='display:flex; justify-content:center; gap:10px'>
                          <a href='?edit={$row['id_customer']}#update' class='logout-btn' style='background-color:#FE9900'>Update</a>
                          <a href='?delete={$row['id_customer']}' class='logout-btn' style='background-color:#B90607' onclick='return confirm(\"Yakin hapus?\")'>Delete</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='6'>Belum ada data customer.</td></tr>";
          }
          ?>
        </tbody>
      </table>
      </div>
      
      <?php if ($edit_data): ?>
      <hr>
      <h3 id="update">Update Data Customer</h3>
      <form method="post" class="mb-3" id="update">
          <div class="mb-2">
              <label>ID Customer</label>
              <input type="text" name="id_customer" class="form-control" value="<?= $edit_data['id_customer'] ?>" readonly>
          </div>
          <div class="mb-2">
              <label>Nama Customer</label>
              <input type="text" name="nama_customer" class="form-control" value="<?= $edit_data['nama_customer'] ?>">
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
      <h3 id="insert">Tambah Data Customer</h3>
      <form method="post" class="mb-3">
          <div class="mb-2">
              <label>ID Customer</label>
              <input type="text" name="id_customer" class="form-control" value="<?= $new_customer?>" readonly>
          </div>
          <div class="mb-2">
              <label>Nama Customer</label>
              <input type="text" name="nama_customer" class="form-control" required>
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
  </main>
</body>
</html>