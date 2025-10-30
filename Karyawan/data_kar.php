<?php
    include '../Database/database.php';
    session_start();
$id_user = $_SESSION['id_user'];

// Ambil data karyawan berdasarkan id_user
$sql = "SELECT * FROM karyawan WHERE id_user = '$id_user'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data karyawan</title>
    <link rel="stylesheet" href="../aset/css/data-karyawan.css">
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
          <strong><?php echo htmlspecialchars($nama); ?></strong></a><br>
        </div>
        <a href="../logout.php" class="logout-btn">Keluar</a>
      </div>
    </div>
  </header>


  <main class="content">
    <div class="card">
        <h1>Data Karyawan</h1>
  <table>
    <tbody>
    <tr>
      <td>ID Karyawan</td>
      <td>: <?= htmlspecialchars($data['id_karyawan']) ?></td>
    </tr>
    <tr>
      <td>Nama</td>
      <td>: <?= htmlspecialchars($data['nama_karyawan']) ?></td>
    </tr>
    <tr>
      <td>Alamat</td>
      <td>: <?= htmlspecialchars($data['alamat']) ?></td>
    </tr>
    <tr>
      <td>Gender</td>
      <td>: <?= htmlspecialchars($data['gender']) ?></td>
    </tr>
    <tr>
      <td>Departemen</td>
      <td>: <?= htmlspecialchars($data['departemen']) ?></td>
    </tr>
    <tr>
      <td>Bergabung Sejak</td>
      <td>: <?= date('d F Y', strtotime($data['bergabung_sejak'])) ?></td>
    </tr>
    <tr>
      <td>Gaji</td>
      <td>: <?= htmlspecialchars($data['gaji']) ?></td>
    </tr>
    </tbody>
  </table>
    </div>
    </main>
</body>
</html>