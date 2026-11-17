<?php
//host
$databaseHost = 'localhost';
//Nama database
$databaseName = 'simrs';
//Username
$databseUsername = 'root';
//Password mysql
$databasePassword = '';

$mysqli = mysqli_connect($databaseHost, $databseUsername, $databasePassword, $databaseName);

if ($mysqli) {
    // 1. Buat folder uploads otomatis jika belum ada
    $uploadDir = __DIR__ . '/uploads';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // 2. Buat tabel users secara otomatis jika belum ada
    $createTableUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($mysqli, $createTableUsers);

    // 3. Buat tabel alat secara otomatis jika belum ada
    $createTableAlat = "CREATE TABLE IF NOT EXISTS alat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_alat VARCHAR(150) NOT NULL,
        no_seri VARCHAR(50) DEFAULT NULL,
        tahun VARCHAR(10) NOT NULL,
        merek VARCHAR(100) NOT NULL,
        lokasi VARCHAR(255) NOT NULL,
        status VARCHAR(50) DEFAULT 'Tersedia',
        penyewa VARCHAR(255) DEFAULT NULL,
        gambar VARCHAR(255) DEFAULT NULL,
        tgl_kalibrasi DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($mysqli, $createTableAlat);

    // 4. Tambahkan kolom status otomatis jika belum ada
    $checkStatus = mysqli_query($mysqli, "SHOW COLUMNS FROM `alat` LIKE 'status'");
    if ($checkStatus && mysqli_num_rows($checkStatus) == 0) {
        mysqli_query($mysqli, "ALTER TABLE `alat` ADD `status` VARCHAR(50) DEFAULT 'Tersedia' AFTER `lokasi`");
    }

    // 5. Tambahkan kolom penyewa otomatis jika belum ada
    $checkPenyewa = mysqli_query($mysqli, "SHOW COLUMNS FROM `alat` LIKE 'penyewa'");
    if ($checkPenyewa && mysqli_num_rows($checkPenyewa) == 0) {
        mysqli_query($mysqli, "ALTER TABLE `alat` ADD `penyewa` VARCHAR(255) NULL AFTER `status`");
    }

    // 6. Tambahkan kolom gambar otomatis jika belum ada
    $checkGambar = mysqli_query($mysqli, "SHOW COLUMNS FROM `alat` LIKE 'gambar'");
    if ($checkGambar && mysqli_num_rows($checkGambar) == 0) {
        mysqli_query($mysqli, "ALTER TABLE `alat` ADD `gambar` VARCHAR(255) NULL AFTER `penyewa`");
    }

    // 7. Tambahkan kolom no_seri otomatis jika belum ada
    $checkNoSeri = mysqli_query($mysqli, "SHOW COLUMNS FROM `alat` LIKE 'no_seri'");
    if ($checkNoSeri && mysqli_num_rows($checkNoSeri) == 0) {
        mysqli_query($mysqli, "ALTER TABLE `alat` ADD `no_seri` VARCHAR(50) NULL AFTER `merek`");
    }

    // 8. Tambahkan kolom tgl_kalibrasi otomatis jika belum ada
    $checkKalibrasi = mysqli_query($mysqli, "SHOW COLUMNS FROM `alat` LIKE 'tgl_kalibrasi'");
    if ($checkKalibrasi && mysqli_num_rows($checkKalibrasi) == 0) {
        mysqli_query($mysqli, "ALTER TABLE `alat` ADD `tgl_kalibrasi` DATE NULL AFTER `gambar`");
    }

    // 9. Pastikan kapasitas kolom lokasi & nama_alat tetap luas
    mysqli_query($mysqli, "ALTER TABLE `alat` MODIFY COLUMN `lokasi` VARCHAR(255) NOT NULL");
    mysqli_query($mysqli, "ALTER TABLE `alat` MODIFY COLUMN `penyewa` VARCHAR(255) NULL");
    mysqli_query($mysqli, "ALTER TABLE `alat` MODIFY COLUMN `nama_alat` VARCHAR(150) NOT NULL");
}
?>