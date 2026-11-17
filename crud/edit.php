<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php?pesan=belum_login");
    exit();
}

include_once("config.php");

// Ambil data saat ini berdasarkan ID
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
$result = mysqli_query($mysqli, "SELECT * FROM alat WHERE id = $id");

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$user_data = mysqli_fetch_array($result);
$nama_alat     = $user_data['nama_alat'];
$no_seri       = !empty($user_data['no_seri']) ? $user_data['no_seri'] : '';
$tahun         = $user_data['tahun'];
$merek         = $user_data['merek'];
$lokasi        = $user_data['lokasi'];
$status        = !empty($user_data['status']) ? $user_data['status'] : 'Tersedia';
$penyewa       = !empty($user_data['penyewa']) ? $user_data['penyewa'] : '';
$gambar_lama   = !empty($user_data['gambar']) ? $user_data['gambar'] : '';
$tgl_kalibrasi = !empty($user_data['tgl_kalibrasi']) ? $user_data['tgl_kalibrasi'] : '';

// Proses pembaruan data alat saat form disubmit
if (isset($_POST['update'])) {
    $nama_alat     = mysqli_real_escape_string($mysqli, trim($_POST['nama_alat']));
    $no_seri       = !empty($_POST['no_seri']) ? mysqli_real_escape_string($mysqli, trim($_POST['no_seri'])) : null;
    $tahun         = mysqli_real_escape_string($mysqli, trim($_POST['tahun']));
    $merek         = mysqli_real_escape_string($mysqli, trim($_POST['merek']));
    $lokasi        = mysqli_real_escape_string($mysqli, trim($_POST['lokasi']));
    $status        = mysqli_real_escape_string($mysqli, trim($_POST['status']));
    $penyewa       = isset($_POST['penyewa']) ? mysqli_real_escape_string($mysqli, trim($_POST['penyewa'])) : null;
    $tgl_kalibrasi = !empty($_POST['tgl_kalibrasi']) ? mysqli_real_escape_string($mysqli, trim($_POST['tgl_kalibrasi'])) : null;
    $gambar        = $gambar_lama;

    if ($status == 'Tersedia') {
        $penyewa = null;
    }

    // Cek jika pengguna mengunggah gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['gambar']['tmp_name'];
        $fileName      = $_FILES['gambar']['name'];
        $fileSize      = $_FILES['gambar']['size'];
        $fileExt       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($fileExt, $allowedExtensions)) {
            if ($fileSize <= 3 * 1024 * 1024) { // Maksimal 3MB
                $newFileName = time() . '_' . uniqid() . '.' . $fileExt;
                $destPath = __DIR__ . '/uploads/' . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Hapus gambar lama jika ada dan file fisiknya ada di uploads/
                    if (!empty($gambar_lama) && file_exists(__DIR__ . '/uploads/' . $gambar_lama)) {
                        @unlink(__DIR__ . '/uploads/' . $gambar_lama);
                    }
                    $gambar = $newFileName;
                }
            }
        }
    }

    $update_sql = "UPDATE alat SET 
                    nama_alat = '$nama_alat', 
                    no_seri = " . ($no_seri ? "'$no_seri'" : "NULL") . ",
                    tahun = '$tahun', 
                    merek = '$merek', 
                    lokasi = '$lokasi', 
                    status = '$status', 
                    penyewa = " . ($penyewa ? "'$penyewa'" : "NULL") . ",
                    gambar = " . ($gambar ? "'$gambar'" : "NULL") . ",
                    tgl_kalibrasi = " . ($tgl_kalibrasi ? "'$tgl_kalibrasi'" : "NULL") . " 
                   WHERE id = $id";
    mysqli_query($mysqli, $update_sql);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Data & Foto Alat - SIMRS</title>

    <link href="tampilan/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="tampilan/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-hospital-alt"></i></div>
                <div class="sidebar-brand-text mx-3">SIM RS MEDIS</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="fas fa-fw fa-stethoscope"></i><span>Data & Status Alat</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add.php"><i class="fas fa-fw fa-plus-circle"></i><span>Tambah Alat Baru</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow justify-content-between">
                    <span class="h5 mb-0 text-gray-800 font-weight-bold d-none d-sm-inline">
                        <i class="fas fa-hospital-symbol text-primary mr-1"></i> SIMRS - Monitoring Alat Elektromedis
                    </span>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item d-flex align-items-center">
                            <span class="mr-3 text-gray-600 small d-none d-sm-inline">
                                <i class="fas fa-user-circle fa-fw text-primary"></i> 
                                Petugas: <strong><?php echo htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?></strong>
                            </span>
                            <a href="logout.php" class="btn btn-outline-danger btn-sm shadow-sm" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                                <i class="fas fa-sign-out-alt fa-sm"></i> Logout
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Container -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Perbarui Data / Status / Kalibrasi Alat</h1>
                            <p class="text-muted small mb-0">Ubah nomor seri, jadwal kalibrasi, foto alat, lokasi ruangan, atau status peminjaman.</p>
                        </div>
                        <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-warning text-dark">
                                    <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-1"></i> Form Edit: <?php echo htmlspecialchars($nama_alat); ?></h6>
                                </div>
                                <div class="card-body">
                                    <form name="update_user" method="post" action="edit.php" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-heartbeat text-primary mr-1"></i> Nama Alat Elektromedis</label>
                                                <input type="text" name="nama_alat" class="form-control" value="<?php echo htmlspecialchars($nama_alat); ?>" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-barcode text-primary mr-1"></i> No. Seri (SN)</label>
                                                <input type="text" name="no_seri" class="form-control" value="<?php echo htmlspecialchars($no_seri); ?>" placeholder="Contoh: SN-88392-A">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-tag text-primary mr-1"></i> Merek / Pabrikan</label>
                                                <input type="text" name="merek" class="form-control" value="<?php echo htmlspecialchars($merek); ?>" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="far fa-calendar-alt text-primary mr-1"></i> Tahun Pengadaan</label>
                                                <input type="number" name="tahun" class="form-control" value="<?php echo htmlspecialchars($tahun); ?>" min="1990" max="2035" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800"><i class="fas fa-map-marker-alt text-danger mr-1"></i> Posisi Ruangan Saat Ini</label>
                                            <textarea name="lokasi" class="form-control" rows="2" maxlength="255" placeholder="Contoh: Gedung Rawat Inap Teratai Lantai 2, Ruang ICU Bed 03..." required><?php echo htmlspecialchars($lokasi); ?></textarea>
                                            <small class="form-text text-muted">Perbarui ruangan jika alat dipindahkan. Menampung hingga 255 karakter agar teks tidak terpotong.</small>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-info-circle text-primary mr-1"></i> Status Penggunaan</label>
                                                <select name="status" id="statusSelect" class="form-control font-weight-bold text-gray-900" onchange="togglePenyewaField()" required>
                                                    <option value="Tersedia" <?php echo ($status == 'Tersedia') ? 'selected' : ''; ?>>&#10004; Tersedia (Siap Digunakan)</option>
                                                    <option value="Disewa / Dipakai" <?php echo (stripos($status, 'sewa') !== false || stripos($status, 'pakai') !== false) ? 'selected' : ''; ?>>&#9873; Sedang Disewa / Dipakai</option>
                                                    <option value="Maintenance" <?php echo (stripos($status, 'maint') !== false || stripos($status, 'rusak') !== false) ? 'selected' : ''; ?>>&#9881; Maintenance / Rusak</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6" id="penyewaGroup">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-user-tag text-primary mr-1"></i> Penyewa / Penanggung Jawab Ruangan</label>
                                                <input type="text" name="penyewa" id="penyewaInput" class="form-control" value="<?php echo htmlspecialchars($penyewa); ?>" maxlength="255" placeholder="Contoh: Unit ICU / Dr. Sarah / Ns. Rina">
                                                <small class="form-text text-muted">Diisi nama unit/dokter yang sedang menyewa atau memakai alat (hingga 255 karakter).</small>
                                            </div>
                                        </div>

                                        <!-- Jadwal Kalibrasi -->
                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800"><i class="fas fa-calendar-check text-success mr-1"></i> Jatuh Tempo Kalibrasi Berikutnya</label>
                                            <input type="date" name="tgl_kalibrasi" class="form-control" value="<?php echo htmlspecialchars($tgl_kalibrasi); ?>">
                                            <small class="form-text text-muted">Atur tanggal jadwal uji kelayakan fungsi atau kalibrasi alat.</small>
                                        </div>

                                        <!-- Bagian Foto Alat -->
                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800"><i class="fas fa-camera text-primary mr-1"></i> Foto / Gambar Alat</label>
                                            
                                            <?php if (!empty($gambar_lama) && file_exists(__DIR__ . '/uploads/' . $gambar_lama)): ?>
                                                <div class="mb-2 d-flex align-items-center">
                                                    <img src="uploads/<?php echo htmlspecialchars($gambar_lama); ?>" class="img-thumbnail mr-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                                    <div>
                                                        <span class="badge badge-secondary mb-1">Foto Saat Ini:</span>
                                                        <div class="small text-muted"><?php echo htmlspecialchars($gambar_lama); ?></div>
                                                        <small class="text-info">Pilih file baru di bawah ini jika ingin mengganti foto.</small>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="small text-muted mb-2"><i class="fas fa-info-circle mr-1"></i> Belum ada foto untuk alat ini. Silakan unggah foto jika ada.</div>
                                            <?php endif; ?>

                                            <div class="custom-file">
                                                <input type="file" name="gambar" class="custom-file-input" id="customFile" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this)">
                                                <label class="custom-file-label" for="customFile" id="fileLabel">Pilih file foto baru jika ingin mengganti...</label>
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, JPEG, PNG, WEBP. Maksimal 3MB.</small>
                                            
                                            <!-- Preview Box Foto Baru -->
                                            <div class="mt-3 text-center" id="previewWrapper" style="display: none;">
                                                <div class="p-2 border rounded bg-light d-inline-block">
                                                    <img id="imagePreview" src="#" alt="Preview Foto Baru" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                                                    <div class="small text-muted mt-1" id="previewFileName"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="mt-4">
                                        <button type="submit" name="update" class="btn btn-success px-4 shadow-sm font-weight-bold">
                                            <i class="fas fa-check mr-1"></i> Simpan Perubahan
                                        </button>
                                        <a href="index.php" class="btn btn-secondary px-3">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Bantuan / Tips -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4 border-left-warning">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-exchange-alt mr-1"></i> Manajemen Peminjaman</h6>
                                </div>
                                <div class="card-body small text-gray-700">
                                    <p>Jika alat telah <strong>selesai disewa atau dikembalikan</strong>:</p>
                                    <ol>
                                        <li>Ubah status menjadi <strong>Tersedia</strong>.</li>
                                        <li>Perbarui lokasi ke ruang penyimpanan/gudang medis.</li>
                                        <li>Kolom penyewa akan otomatis dikosongkan.</li>
                                    </ol>
                                    <p class="mb-0">Jika alat mengalami kendala teknis, ganti status ke <strong>Maintenance</strong> agar tidak dipinjamkan ke pasien.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SIMRS Teknik Elektromedis <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JS Files -->
    <script src="tampilan/vendor/jquery/jquery.min.js"></script>
    <script src="tampilan/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="tampilan/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="tampilan/js/sb-admin-2.min.js"></script>

    <script>
        function togglePenyewaField() {
            var status = document.getElementById("statusSelect").value;
            var penyewaInput = document.getElementById("penyewaInput");
            if (status === "Disewa / Dipakai") {
                penyewaInput.required = true;
            } else {
                penyewaInput.required = false;
            }
        }
        togglePenyewaField();

        function previewImage(input) {
            var previewWrapper = document.getElementById('previewWrapper');
            var imagePreview = document.getElementById('imagePreview');
            var fileLabel = document.getElementById('fileLabel');
            var previewFileName = document.getElementById('previewFileName');

            if (input.files && input.files[0]) {
                var file = input.files[0];
                fileLabel.innerText = file.name;
                previewFileName.innerText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';

                var reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewWrapper.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewWrapper.style.display = 'none';
                fileLabel.innerText = 'Pilih file foto baru jika ingin mengganti...';
            }
        }
    </script>
</body>
</html>
