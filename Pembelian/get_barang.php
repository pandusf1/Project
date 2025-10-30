<?php
include '../Database/database.php';

if (isset($_POST['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
    $query = mysqli_query($conn, "SELECT * FROM barang WHERE nama_barang LIKE '%$keyword%' LIMIT 10");

    if (mysqli_num_rows($query) > 0) {
        while ($b = mysqli_fetch_assoc($query)) {
            echo "
            <div class='suggestion-item' 
                 data-id='{$b['id_barang']}'
                 data-nama='{$b['nama_barang']}'
                 data-kategori='{$b['kategori']}'
                 data-merk='{$b['merk']}'
                 data-satuan='{$b['satuan']}'
                 data-harga='{$b['harga_satuan']}'>
                 {$b['nama_barang']}
            </div>";
        }
    } else {
        echo "<div class='suggestion-item' data-id='' data-nama='$keyword'>+ Tambah Barang Baru \"$keyword\"</div>";
    }
}
?>
