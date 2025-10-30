<?php
$host = "sinarbit.com";
$user = "kelompok3";
$pass = "1234";
$db   = "kelompok3";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
