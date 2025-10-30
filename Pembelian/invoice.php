<?php
include '../Database/database.php';

if (isset($_POST['submit'])) {
    $nomor_faktur = $_POST['nomor_faktur'];
    $id_vendor = $_POST['id_vendor']; 
    $diskon = $_POST['diskon'];
    $pajak = $_POST['pajak'];
    $ongkir = $_POST['ongkir'];
    $total = $_POST['total'];
    $termin = $_POST['termin'];
    $jmlh_blm_bayar = $total;
    $status = "Belum Bayar";

    mysqli_begin_transaction($conn);

    try {
        // 🧾 Simpan data ke tabel pembelian
        $q1 = mysqli_query($conn, "
            INSERT INTO pembelian 
            (nomor_faktur, id_vendor, diskon, pajak, ongkir, total, termin, jmlh_blm_bayar, status)
            VALUES 
            ('$nomor_faktur', '$id_vendor', '$diskon', '$pajak', '$ongkir', '$total', '$termin', '$jmlh_blm_bayar', '$status')
        ");
        if (!$q1) throw new Exception("Gagal insert ke tabel pembelian: " . mysqli_error($conn));

        // 🔹 Ambil semua data barang dari form
        $id_barang = $_POST['id_barang'];
        $nama_barang = $_POST['nama_barang'];
        $kategori = $_POST['kategori'];
        $merk = $_POST['merk'];
        $satuan = $_POST['satuan'];
        $harga_satuan = $_POST['harga_satuan'];
        $qty = $_POST['qty'];
        $sub_total = $_POST['sub_total'];

        for ($i = 0; $i < count($id_barang); $i++) {
            $id = mysqli_real_escape_string($conn, $id_barang[$i]);
            $nama = mysqli_real_escape_string($conn, $nama_barang[$i]);
            $kat = mysqli_real_escape_string($conn, $kategori[$i]);
            $mrk = mysqli_real_escape_string($conn, $merk[$i]);
            $sat = mysqli_real_escape_string($conn, $satuan[$i]);
            $harga = floatval($harga_satuan[$i]);
            $jumlah = intval($qty[$i]);
            $subtotal = floatval($sub_total[$i]);

            // 🔍 Cek apakah barang sudah ada di tabel barang
            $cek = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'");
            
            if (mysqli_num_rows($cek) > 0) {
                // Jika barang sudah ada → tambahkan stok qty & perbarui total
                $update = mysqli_query($conn, "
                    UPDATE barang 
                    SET qty = qty + $jumlah,
                        harga_satuan = $harga,
                        sub_total = (qty + $jumlah) * $harga
                    WHERE id_barang = '$id'
                ");
                if (!$update) throw new Exception("Gagal update data barang: " . mysqli_error($conn));
            } else {
                // 🆕 Jika barang belum ada → generate ID otomatis
                $result = mysqli_query($conn, "SELECT MAX(CAST(SUBSTRING(id_barang, 3) AS UNSIGNED)) AS last_barang FROM barang");
                $data = mysqli_fetch_assoc($result);
                $last_barang = $data['last_barang'];

                if ($last_barang == null) {
                    $new_id_barang = "IV001";
                } else {
                    $num = (int)$last_barang + 1;
                    $new_id_barang = "IV" . str_pad($num, 3, "0", STR_PAD_LEFT);
                }

                // 🧾 Insert barang baru
                $insert_barang = mysqli_query($conn, "
                    INSERT INTO barang (id_barang, nama_barang, kategori, merk, satuan, harga_satuan, qty, sub_total)
                    VALUES ('$new_id_barang', '$nama', '$kat', '$mrk', '$sat', '$harga', '$jumlah', '$subtotal')
                ");
                if (!$insert_barang) throw new Exception("Gagal insert barang baru: " . mysqli_error($conn));

                // Ganti id yang akan dipakai di detail_pembelian dengan ID baru
                $id = $new_id_barang;
            }

            // 🧾 Simpan ke detail_pembelian
            $q2 = mysqli_query($conn, "
                INSERT INTO detail_pembelian 
                (nomor_faktur, id_barang, qty, harga_satuan, sub_total)
                VALUES 
                ('$nomor_faktur', '$id', '$jumlah', '$harga', '$subtotal')
            ");
            if (!$q2) throw new Exception("Gagal insert detail pembelian: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        echo "<script>alert('✅ Data pembelian berhasil disimpan dan stok barang diperbarui.'); window.location='pembelian.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('❌ Transaksi dibatalkan: " . addslashes($e->getMessage()) . "'); history.back();</script>";
    }
}
?>
