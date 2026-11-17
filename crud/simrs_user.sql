-- Database: simrs

-- 1. Struktur Tabel `users` untuk Login & Registrasi
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun default untuk pengujian:
-- Username: admin
-- Password: admin123
INSERT INTO `users` (`nama`, `username`, `password`) 
VALUES ('Administrator Elektromedis', 'admin', '$2y$10$6UxKQyoYaqQKgUvFSG/j8OnH2jQt7LMkSewJwps7eYaQoqR7pzsXi')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- 2. Struktur Tabel `alat` untuk Manajemen Data Alat Elektromedis
CREATE TABLE IF NOT EXISTS `alat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_alat` varchar(150) NOT NULL,
  `no_seri` varchar(50) DEFAULT NULL,
  `tahun` varchar(10) NOT NULL,
  `merek` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Tersedia',
  `penyewa` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tgl_kalibrasi` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contoh data awal alat elektromedis dengan gambar, nomor seri & jadwal kalibrasi (opsional)
INSERT INTO `alat` (`nama_alat`, `no_seri`, `tahun`, `merek`, `lokasi`, `status`, `penyewa`, `gambar`, `tgl_kalibrasi`) VALUES
('Pulse Oximeter Digital Portable', 'SN-OXI-2023-01', '2023', 'ChoiceMMed', 'Instalasi Gawat Darurat (IGD) - Ruang Resusitasi Bed 01', 'Tersedia', NULL, 'oximeter.jpg', '2027-05-15'),
('Tensimeter Digital Otomatis', 'SN-TNS-4491-B', '2023', 'Omron Healthcare', 'Poliklinik Rawat Jalan Spesialis Penyakit Dalam', 'Disewa / Dipakai', 'Poli Penyakit Dalam (dr. Budi, Sp.PD)', 'tensimeter.jpg', '2026-12-01'),
('Stetoskop Akustik Elektrik Medis', 'SN-STK-9902-X', '2022', '3M Littmann', 'Gedung Bedah Terpadu Lantai 3 - Ruang Intensive Care Unit (ICU) Bed 02', 'Disewa / Dipakai', 'Unit Perawatan Intensif (dr. Sarah)', 'stetoskop.jpg', '2026-11-25'),
('Thermometer Infrared Klinis Non-Contact', 'SN-THM-1029-C', '2024', 'Microlife', 'Ruang Rawat Inap Melati Lantai 2 Kamar 204', 'Tersedia', NULL, 'thermometer.jpg', '2027-08-20'),
('Kursi Roda Medis Standar Rumah Sakit', 'SN-WCH-8120-M', '2021', 'Sella Medika', 'Gudang Logistik & Peralatan Medis Sentral', 'Maintenance', NULL, 'kursiroda.jpg', '2026-10-10')
ON DUPLICATE KEY UPDATE `nama_alat`=`nama_alat`;
