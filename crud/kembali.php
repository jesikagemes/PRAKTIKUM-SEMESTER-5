<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php?pesan=belum_login");
    exit();
}

include_once("config.php");

// Ambil ID alat yang dikembalikan
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Ubah status alat kembali menjadi Tersedia dan kosongkan penyewa
    $update_query = "UPDATE alat SET status = 'Tersedia', penyewa = NULL WHERE id = $id";
    mysqli_query($mysqli, $update_query);
}

// Alihkan kembali ke halaman utama dengan notifikasi sukses
header("Location: index.php?pesan=kembali_sukses");
exit();
?>