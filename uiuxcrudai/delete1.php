<?php
// Memanggil config1.php
include_once("config1.php");

// Mengambil id dari URL
$id = $_GET['id'];

// Hapus data alat berdasarkan id
$result = mysqli_query($mysqli, "DELETE FROM alat WHERE id=$id");

// Kembalikan ke halaman index
header("Location:index.php");
?>