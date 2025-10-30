<?php
include '../database/database.php';

if (isset($_POST['nama_barang'])) {
    $nama_barang = $_POST['nama_barang'];
    $query = mysqli_query($conn, "SELECT * FROM barang WHERE nama_barang = '$nama_barang'");
    $data = mysqli_fetch_assoc($query);

    echo json_encode([
        'id_barang' => $data['id_barang'],
        'kategori' => $data['kategori'],
        'merk' => $data['merk'],
        'satuan' => $data['satuan'],
        'harga_satuan' => $data['harga_satuan']
    ]);
}
?>
