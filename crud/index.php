<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php?pesan=belum_login");
    exit();
}

include_once("config.php");

// Logika Pencarian dan Filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($mysqli, trim($_GET['search'])) : '';
$filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($mysqli, trim($_GET['filter_status'])) : '';

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(nama_alat LIKE '%$search%' OR merek LIKE '%$search%' OR lokasi LIKE '%$search%' OR penyewa LIKE '%$search%' OR no_seri LIKE '%$search%')";
}
if (!empty($filter_status)) {
    if ($filter_status == 'Tersedia') {
        $where_clauses[] = "status = 'Tersedia'";
    } elseif ($filter_status == 'Disewa') {
        $where_clauses[] = "(status LIKE '%Sewa%' OR status LIKE '%Pakai%')";
    } elseif ($filter_status == 'Maintenance') {
        $where_clauses[] = "(status LIKE '%Maintenance%' OR status LIKE '%Perbaikan%' OR status LIKE '%Rusak%')";
    }
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";
$result = mysqli_query($mysqli, "SELECT * FROM alat $where_sql ORDER BY id DESC");

// Hitung data statistik untuk widget kartu
$q_total = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM alat");
$count_total = mysqli_fetch_assoc($q_total)['count'] ?? 0;

$q_tersedia = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM alat WHERE status = 'Tersedia'");
$count_tersedia = mysqli_fetch_assoc($q_tersedia)['count'] ?? 0;

$q_disewa = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM alat WHERE status LIKE '%Sewa%' OR status LIKE '%Pakai%'");
$count_disewa = mysqli_fetch_assoc($q_disewa)['count'] ?? 0;

$q_maint = mysqli_query($mysqli, "SELECT COUNT(*) as count FROM alat WHERE status LIKE '%Maintenance%' OR status LIKE '%Perbaikan%' OR status LIKE '%Rusak%'");
$count_maint = mysqli_fetch_assoc($q_maint)['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SIMRS - Monitoring Alat Elektromedis</title>

    <!-- Font & CSS SB Admin 2 -->
    <link href="tampilan/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="tampilan/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .card-stat {
            transition: all 0.25s ease-in-out;
            cursor: pointer;
        }
        .card-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }
        .badge-status {
            font-size: 0.82rem;
            padding: 0.45em 0.75em;
            border-radius: 50rem;
        }
        .alat-name {
            font-size: 1.02rem;
            font-weight: 700;
            color: #2e59d9;
        }
        .alat-img-thumb {
            width: 58px;
            height: 58px;
            min-width: 58px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #eaecf4;
            transition: transform 0.2s;
            cursor: pointer;
        }
        .alat-img-thumb:hover {
            transform: scale(1.08);
            border-color: #4e73df;
        }
        .table td {
            vertical-align: middle !important;
        }
        @media print {
            .no-print, .sidebar, .topbar, .btn, .card-header form {
                display: none !important;
            }
            .content-wrapper, #content {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion no-print" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-hospital-alt"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SIM RS MEDIS</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard / Table -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-stethoscope"></i>
                    <span>Data & Status Alat</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="add.php">
                    <i class="fas fa-fw fa-plus-circle"></i>
                    <span>Tambah Alat Baru</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Navigasi Cepat
            </div>

            <li class="nav-item">
                <a class="nav-link" href="index.php?filter_status=Disewa">
                    <i class="fas fa-fw fa-procedures text-warning"></i>
                    <span>Sedang Disewa (<?php echo $count_disewa; ?>)</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?filter_status=Tersedia">
                    <i class="fas fa-fw fa-check-circle text-success"></i>
                    <span>Alat Tersedia (<?php echo $count_tersedia; ?>)</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="export_excel.php">
                    <i class="fas fa-fw fa-file-excel text-success"></i>
                    <span>Unduh Excel</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow justify-content-between no-print">
                    <div class="d-flex align-items-center">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                        <span class="h5 mb-0 text-gray-800 font-weight-bold d-none d-sm-inline">
                            <i class="fas fa-hospital-symbol text-primary mr-1"></i> SIMRS - Monitoring Alat Elektromedis
                        </span>
                    </div>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item d-flex align-items-center">
                            <span class="mr-3 text-gray-700 small d-none d-md-inline">
                                <i class="fas fa-user-circle text-primary"></i> 
                                Petugas: <strong><?php echo htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?></strong>
                            </span>
                            <a href="logout.php" class="btn btn-outline-danger btn-sm shadow-sm" onclick="return confirm('Apakah Anda yakin ingin logout dari sistem?')">
                                <i class="fas fa-sign-out-alt fa-sm"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Notifikasi Aksi -->
                    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'kembali_sukses'): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> <strong>Pengembalian Berhasil!</strong> Alat telah berhasil dikembalikan, data penyewa dibersihkan, dan status alat kini kembali <strong>Tersedia</strong>.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Header Judul & Tombol Cetak / Ekspor / Tambah -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Monitoring Alat Elektromedis</h1>
                            <p class="text-muted small mb-0">Pantau foto fisik, nomor seri, jatuh tempo kalibrasi, lokasi ruangan, dan status sewa.</p>
                        </div>
                        <div class="mt-3 mt-sm-0 no-print">
                            <a href="export_excel.php" class="btn btn-sm btn-success shadow-sm mr-1">
                                <i class="fas fa-file-excel fa-sm text-white mr-1"></i> Ekspor Excel
                            </a>
                            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary shadow-sm mr-1">
                                <i class="fas fa-print fa-sm text-gray-600 mr-1"></i> Cetak Laporan
                            </button>
                            <a href="add.php" class="btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Data Alat
                            </a>
                        </div>
                    </div>

                    <!-- BARIS STATISTIK KARTU (STAT CARDS) -->
                    <div class="row mb-4">

                        <!-- Total Alat -->
                        <div class="col-xl-3 col-md-6 mb-3">
                            <a href="index.php" class="text-decoration-none">
                                <div class="card border-left-primary shadow h-100 py-2 card-stat">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Semua Alat
                                                </div>
                                                <div class="h4 mb-0 font-weight-bold text-gray-800"><?php echo $count_total; ?> <span class="small font-weight-normal text-muted">Unit</span></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-laptop-medical fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Alat Tersedia (Ready) -->
                        <div class="col-xl-3 col-md-6 mb-3">
                            <a href="index.php?filter_status=Tersedia" class="text-decoration-none">
                                <div class="card border-left-success shadow h-100 py-2 card-stat">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Alat Siap / Tersedia
                                                </div>
                                                <div class="h4 mb-0 font-weight-bold text-success"><?php echo $count_tersedia; ?> <span class="small font-weight-normal text-muted">Unit</span></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Sedang Disewa / Dipakai di Ruangan -->
                        <div class="col-xl-3 col-md-6 mb-3">
                            <a href="index.php?filter_status=Disewa" class="text-decoration-none">
                                <div class="card border-left-warning shadow h-100 py-2 card-stat">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Sedang Disewa / Dipakai
                                                </div>
                                                <div class="h4 mb-0 font-weight-bold text-warning"><?php echo $count_disewa; ?> <span class="small font-weight-normal text-muted">Unit</span></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-procedures fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Dalam Maintenance / Perbaikan -->
                        <div class="col-xl-3 col-md-6 mb-3">
                            <a href="index.php?filter_status=Maintenance" class="text-decoration-none">
                                <div class="card border-left-danger shadow h-100 py-2 card-stat">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                    Maintenance / Rusak
                                                </div>
                                                <div class="h4 mb-0 font-weight-bold text-danger"><?php echo $count_maint; ?> <span class="small font-weight-normal text-muted">Unit</span></div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-tools fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>

                    <!-- CARD TABEL & FILTER PENCARIAN -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list-alt mr-1"></i> Daftar Inventaris, Foto & Posisi Ruangan Alat
                                <?php if (!empty($filter_status)): ?>
                                    <span class="badge badge-info ml-2">Filter: <?php echo htmlspecialchars($filter_status); ?></span>
                                    <a href="index.php" class="text-danger small ml-1 font-weight-normal"><i class="fas fa-times-circle"></i> Reset</a>
                                <?php endif; ?>
                            </h6>

                            <!-- Form Pencarian -->
                            <form method="GET" action="index.php" class="form-inline mt-2 mt-md-0 no-print">
                                <?php if (!empty($filter_status)): ?>
                                    <input type="hidden" name="filter_status" value="<?php echo htmlspecialchars($filter_status); ?>">
                                <?php endif; ?>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama, SN, merek, ruangan..." value="<?php echo htmlspecialchars($search); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <?php if (!empty($search)): ?>
                                            <a href="index.php<?php echo !empty($filter_status) ? '?filter_status='.$filter_status : ''; ?>" class="btn btn-outline-secondary">Reset</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th width="40">No</th>
                                            <th style="min-width: 200px;">Foto & Spesifikasi Alat</th>
                                            <th width="140">Status Alat</th>
                                            <th style="min-width: 220px;">Posisi Ruangan Saat Ini</th>
                                            <th style="min-width: 180px;">Penyewa / Penanggung Jawab</th>
                                            <th width="145">Jatuh Tempo Kalibrasi</th>
                                            <th width="170" class="no-print">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($alat = mysqli_fetch_array($result)) {
                                                $status = !empty($alat['status']) ? $alat['status'] : 'Tersedia';
                                                $penyewa = !empty($alat['penyewa']) ? $alat['penyewa'] : '-';
                                                $gambar = !empty($alat['gambar']) ? $alat['gambar'] : '';
                                                $no_seri = !empty($alat['no_seri']) ? $alat['no_seri'] : '';
                                                
                                                // Tentukan warna badge dan icon berdasarkan status penggunaan
                                                $badge_class = "badge-success";
                                                $icon_status = "fa-check-circle";
                                                $is_disewa = (stripos($status, 'sewa') !== false || stripos($status, 'pakai') !== false);
                                                if ($is_disewa) {
                                                    $badge_class = "badge-warning text-dark";
                                                    $icon_status = "fa-procedures";
                                                } elseif (stripos($status, 'maint') !== false || stripos($status, 'rusak') !== false || stripos($status, 'perbaikan') !== false) {
                                                    $badge_class = "badge-danger";
                                                    $icon_status = "fa-tools";
                                                }

                                                // Hitung selisih hari kalibrasi
                                                $badge_kalibrasi = "<span class='badge badge-light border text-muted px-2 py-1'><i class='far fa-calendar-times mr-1'></i> Belum Ada</span>";
                                                if (!empty($alat['tgl_kalibrasi'])) {
                                                    $tgl_k = strtotime($alat['tgl_kalibrasi']);
                                                    $today = strtotime(date('Y-m-d'));
                                                    $diff_days = round(($tgl_k - $today) / (60 * 60 * 24));
                                                    $formatted_date = date('d/m/Y', $tgl_k);

                                                    if ($diff_days < 0) {
                                                        $badge_kalibrasi = "<span class='badge badge-danger px-2 py-1' title='Sudah melewati jatuh tempo! Wajib kalibrasi ulang.'><i class='fas fa-exclamation-triangle mr-1'></i> Lewat ($formatted_date)</span>";
                                                    } elseif ($diff_days <= 30) {
                                                        $badge_kalibrasi = "<span class='badge badge-warning text-dark px-2 py-1' title='Mendekati jatuh tempo dalam $diff_days hari'><i class='fas fa-clock mr-1'></i> H-$diff_days ($formatted_date)</span>";
                                                    } else {
                                                        $badge_kalibrasi = "<span class='badge badge-info px-2 py-1' title='Terkalibrasi aman'><i class='fas fa-certificate mr-1'></i> $formatted_date</span>";
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center font-weight-bold text-muted"><?php echo $i; ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <!-- Thumbnail Foto Alat -->
                                                            <?php if (!empty($gambar) && file_exists(__DIR__ . '/uploads/' . $gambar)): ?>
                                                                <a href="#" data-toggle="modal" data-target="#imageModal" data-img="uploads/<?php echo htmlspecialchars($gambar); ?>" data-title="<?php echo htmlspecialchars($alat['nama_alat']); ?>" title="Klik untuk memperbesar foto">
                                                                    <img src="uploads/<?php echo htmlspecialchars($gambar); ?>" 
                                                                         alt="<?php echo htmlspecialchars($alat['nama_alat']); ?>" 
                                                                         class="alat-img-thumb mr-3 shadow-sm">
                                                                </a>
                                                            <?php else: ?>
                                                                <div class="rounded bg-light border d-flex align-items-center justify-content-center mr-3 text-primary shadow-sm" 
                                                                     style="width: 58px; height: 58px; min-width: 58px;" title="Tidak ada foto">
                                                                    <i class="fas fa-stethoscope fa-2x"></i>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- Nama, No Seri, & Merek -->
                                                            <div>
                                                                <span class="alat-name"><?php echo htmlspecialchars($alat['nama_alat']); ?></span>
                                                                <div class="small text-muted mt-1">
                                                                    <?php if (!empty($no_seri)): ?>
                                                                        <span class="mr-2"><i class="fas fa-barcode fa-xs"></i> SN: <strong class="text-dark"><?php echo htmlspecialchars($no_seri); ?></strong></span>
                                                                    <?php endif; ?>
                                                                    <span class="mr-2"><i class="fas fa-tag fa-xs"></i> Merek: <strong><?php echo htmlspecialchars($alat['merek']); ?></strong></span>
                                                                    <span><i class="far fa-calendar-alt fa-xs"></i> Tahun: <strong><?php echo htmlspecialchars($alat['tahun']); ?></strong></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-status <?php echo $badge_class; ?>">
                                                            <i class="fas <?php echo $icon_status; ?> mr-1"></i> <?php echo htmlspecialchars($status); ?>
                                                        </span>
                                                    </td>
                                                    <td style="min-width: 220px; white-space: normal;">
                                                        <div class="d-flex align-items-start">
                                                            <i class="fas fa-map-marker-alt text-danger mt-1 mr-2 flex-shrink-0"></i>
                                                            <div style="word-break: break-word; line-height: 1.45;">
                                                                <strong class="text-gray-900"><?php echo htmlspecialchars($alat['lokasi']); ?></strong>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td style="min-width: 180px; white-space: normal;">
                                                        <?php if ($penyewa != '-' && $is_disewa): ?>
                                                            <div class="d-flex align-items-start">
                                                                <i class="fas fa-user-tag text-primary mt-1 mr-2 flex-shrink-0"></i>
                                                                <span class="text-primary font-weight-bold" style="word-break: break-word; line-height: 1.45;">
                                                                    <?php echo htmlspecialchars($penyewa); ?>
                                                                </span>
                                                            </div>
                                                        <?php elseif (stripos($status, 'maint') !== false): ?>
                                                            <span class="text-danger small font-italic">
                                                                <i class="fas fa-wrench mr-1"></i> Dalam Penanganan Teknisi
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted small font-italic">
                                                                <i class="fas fa-minus-circle mr-1"></i> Tidak disewa (Siap digunakan)
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- Status Kalibrasi -->
                                                    <td class="text-center">
                                                        <?php echo $badge_kalibrasi; ?>
                                                    </td>
                                                    <!-- Tombol Aksi -->
                                                    <td class="text-center no-print">
                                                        <?php if ($is_disewa): ?>
                                                            <a href="kembali.php?id=<?php echo $alat['id']; ?>" class="btn btn-success btn-sm shadow-sm mb-1" onclick="return confirm('Konfirmasi pengembalian alat ini? Status akan otomatis kembali menjadi Tersedia.')" title="Selesai Sewa / Kembalikan Alat">
                                                                <i class="fas fa-undo-alt mr-1"></i> Selesai
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="edit.php?id=<?php echo $alat['id']; ?>" class="btn btn-warning btn-sm shadow-sm mb-1" title="Ubah Data / Foto / Status">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="delete.php?id=<?php echo $alat['id']; ?>" class="btn btn-danger btn-sm shadow-sm mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus alat ini dari database?')" title="Hapus Data">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                                                    <h5 class="text-gray-600 font-weight-bold">Tidak ada data alat yang ditemukan</h5>
                                                    <p class="text-muted small">Coba ubah kata kunci pencarian atau tambahkan data alat elektromedis baru.</p>
                                                    <a href="add.php" class="btn btn-primary btn-sm mt-2">
                                                        <i class="fas fa-plus mr-1"></i> Tambah Alat Sekarang
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white no-print">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SIMRS Teknik Elektromedis <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Modal Pratinjau Foto Lebih Besar -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalImageTitle"><i class="fas fa-camera mr-2"></i>Foto Alat Elektromedis</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="modalImageSrc" src="" alt="Foto Alat" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain;">
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="tampilan/vendor/jquery/jquery.min.js"></script>
    <script src="tampilan/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="tampilan/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="tampilan/js/sb-admin-2.min.js"></script>

    <script>
        // Script untuk menampilkan gambar besar di modal ketika thumbnail diklik
        $('#imageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var imgSrc = button.data('img');
            var title = button.data('title');
            var modal = $(this);
            modal.find('#modalImageTitle').html('<i class="fas fa-stethoscope mr-2"></i> ' + title);
            modal.find('#modalImageSrc').attr('src', imgSrc);
        });
    </script>

</body>

</html>
