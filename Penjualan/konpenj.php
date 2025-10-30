<?php
include '../database/database.php';

if (isset($_POST['submit'])) {
    $nomor_faktur = $_POST['nomor_faktur'];
    $id_customer = $_POST['id_customer'];
    $diskon = $_POST['diskon'];
    $pajak = $_POST['pajak'];
    $ongkir = $_POST['ongkir'];
    $total = $_POST['total'];
    $status = $_POST['status'];
    $termin = $_POST['termin'];

    mysqli_begin_transaction($conn);

    try {
        // 🧾 Simpan ke tabel penjualan
        $q1 = mysqli_query($conn, "INSERT INTO penjualan 
            (nomor_faktur, id_customer, diskon, pajak, ongkir, total, status, termin)
            VALUES 
            ('$nomor_faktur', '$id_customer', '$diskon', '$pajak', '$ongkir', '$total', '$status', '$termin')
        ");

        if (!$q1) {
            throw new Exception("Gagal insert ke tabel penjualan: " . mysqli_error($conn));
        }

        // 🧩 Simpan detail penjualan + kurangi qty di tabel barang
        $id_barang = $_POST['id_barang'];
        $qty = $_POST['qty'];
        $harga_satuan = $_POST['harga_satuan'];
        $sub_total = $_POST['sub_total'];

        for ($i = 0; $i < count($id_barang); $i++) {
            $id = $id_barang[$i];
            $jumlah_jual = $qty[$i];
            $harga = $harga_satuan[$i];
            $subtotal = $sub_total[$i];

            // Simpan detail penjualan
            $q2 = mysqli_query($conn, "INSERT INTO detail_penjualan 
                (nomor_faktur, id_barang, qty, harga_satuan, sub_total)
                VALUES 
                ('$nomor_faktur', '$id', '$jumlah_jual', '$harga', '$subtotal')
            ");

            if (!$q2) {
                throw new Exception("Gagal insert detail penjualan: " . mysqli_error($conn));
            }

            // 🔻 Ambil harga_satuan dari tabel barang untuk menghitung pengurangan sub_total
            $q_harga = mysqli_query($conn, "SELECT harga_satuan FROM barang WHERE id_barang = '$id'");
            if (!$q_harga) {
                throw new Exception("Gagal ambil harga_satuan barang: " . mysqli_error($conn));
            }
            $data_harga = mysqli_fetch_assoc($q_harga);
            $harga_satuan_barang = $data_harga['harga_satuan'];
            
            // Hitung nilai pengurang sub_total
            $pengurang_subtotal = $harga_satuan_barang * $jumlah_jual;
            
            // 🔻 Kurangi stok (qty) dan kurangi juga sub_total di tabel barang
            $update = mysqli_query($conn, "
                UPDATE barang 
                SET 
                    qty = qty - $jumlah_jual, 
                    sub_total = sub_total - $pengurang_subtotal
                WHERE id_barang = '$id'
            ");
            
            if (!$update) {
                throw new Exception("Gagal update qty dan sub_total barang: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        echo "<script>alert('✅ Data penjualan berhasil disimpan dan stok barang berkurang.'); window.location='penjualan.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('❌ Transaksi dibatalkan: " . addslashes($e->getMessage()) . "'); history.back();</script>";
    }
}
?>
