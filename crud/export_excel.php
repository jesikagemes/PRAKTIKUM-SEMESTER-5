<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php?pesan=belum_login");
    exit();
}

include_once("config.php");

// Set header agar browser mengunduh file sebagai Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Inventaris_Alat_Elektromedis_" . date('Y-m-d_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil semua data alat
$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris Alat Elektromedis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
        }
        .header-sub {
            font-size: 10pt;
            text-align: center;
            color: #555;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        th {
            background-color: #2e59d9;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000000;
            padding: 8px;
        }
        td {
            border: 1px solid #000000;
            padding: 6px;
            vertical-align: top;
        }
        .text-center {
            text-align: center;
        }
        .badge-tersedia {
            color: #1cc88a;
            font-weight: bold;
        }
        .badge-disewa {
            color: #f6c23e;
            font-weight: bold;
        }
        .badge-maint {
            color: #e74a3b;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header-title">SISTEM INFORMASI MANAJEMEN RUMAH SAKIT (SIMRS)</div>
    <div class="header-title">LAPORAN DATA INVENTARIS & STATUS ALAT ELEKTROMEDIS</div>
    <div class="header-sub">Tanggal Ekspor: <?php echo date('d-m-Y H:i:s'); ?> | Petugas: <?php echo htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?></div>
    <br>

    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th>Nama Alat Elektromedis</th>
                <th>No. Seri (SN)</th>
                <th>Merek</th>
                <th>Tahun</th>
                <th>Posisi Ruangan Saat Ini</th>
                <th>Status Penggunaan</th>
                <th>Penyewa / Penanggung Jawab</th>
                <th>Jatuh Tempo Kalibrasi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_array($result)) {
                $status = !empty($row['status']) ? $row['status'] : 'Tersedia';
                $penyewa = !empty($row['penyewa']) ? $row['penyewa'] : '-';
                $no_seri = !empty($row['no_seri']) ? $row['no_seri'] : '-';
                $tgl_kalibrasi = !empty($row['tgl_kalibrasi']) ? date('d/m/Y', strtotime($row['tgl_kalibrasi'])) : 'Belum Terjadwal';
                
                // Status class
                $status_class = "badge-tersedia";
                if (stripos($status, 'sewa') !== false || stripos($status, 'pakai') !== false) {
                    $status_class = "badge-disewa";
                } elseif (stripos($status, 'maint') !== false || stripos($status, 'rusak') !== false) {
                    $status_class = "badge-maint";
                }
                ?>
                <tr>
                    <td class="text-center"><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nama_alat']); ?></strong></td>
                    <td class="text-center"><?php echo htmlspecialchars($no_seri); ?></td>
                    <td><?php echo htmlspecialchars($row['merek']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['tahun']); ?></td>
                    <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                    <td class="text-center <?php echo $status_class; ?>"><?php echo htmlspecialchars($status); ?></td>
                    <td><?php echo htmlspecialchars($penyewa); ?></td>
                    <td class="text-center"><?php echo $tgl_kalibrasi; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

</body>
</html>
