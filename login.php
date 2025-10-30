<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'database/database.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $_SESSION['username'] = $row['username'];
    $_SESSION['departement'] = $row['departement'];
    $_SESSION['username'] = $row['username'] ?? $row['nama_karyawan'];
    $_SESSION['id_user'] = $row['id'];

    $sql_karyawan = "SELECT * FROM karyawan WHERE id_user = '{$row['id']}' LIMIT 1";
    $result_karyawan = $conn->query($sql_karyawan);

    if ($result_karyawan && $result_karyawan->num_rows > 0) {
        $data_karyawan = $result_karyawan->fetch_assoc();
        $_SESSION['id_karyawan'] = $data_karyawan['id_karyawan'];
        $_SESSION['nama_karyawan'] = $data_karyawan['nama_karyawan'];
        $_SESSION['departemen'] = $data_karyawan['departemen'];
    }    

    switch ($row['departement']) {
        case 'Gudang':
            header("Location: gudang/gudang.php");
            break;
        case 'Penjualan':
            header("Location: penjualan/penjualan.php");
            break;
        case 'Akuntansi':
            header("Location: akuntansi/akuntansi.php");
            break;
        case 'Pembelian':
            header("Location: pembelian/pembelian.php");
            break;
        case 'Karyawan':
            header("Location: karyawan/karyawan.php");
            break;
        case 'Master':
            header("Location: master/master.php");
            break;
        case 'HRD':
            header("Location: hrd/hrd.php");
            break;
        default:
            header("Location: index.php");
    }
    exit();
} else {
    echo "<script>alert('Username atau password salah!'); window.location='index.php';</script>";
}

?>
