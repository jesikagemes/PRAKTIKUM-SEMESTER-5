<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php?pesan=belum_login");
    exit();
}

// include database connection file
include_once("config.php");

// Ambil ID alat yang akan dihapus
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil info nama file gambar untuk dihapus dari folder uploads
$res = mysqli_query($mysqli, "SELECT gambar FROM alat WHERE id = $id");
if ($res && $data = mysqli_fetch_assoc($res)) {
    if (!empty($data['gambar']) && file_exists(__DIR__ . '/uploads/' . $data['gambar'])) {
        @unlink(__DIR__ . '/uploads/' . $data['gambar']);
    }
}

// Hapus data alat dari tabel database
$result = mysqli_query($mysqli, "DELETE FROM alat WHERE id = $id");

// Alihkan kembali ke halaman utama
header("Location: index.php");
exit();
?>