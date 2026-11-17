<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php?pesan=belum_login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tambah Data Alat Elektromedis - SIMRS</title>

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
            <li class="nav-item active">
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
                            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Tambah Data Alat Baru</h1>
                            <p class="text-muted small mb-0">Input nomor seri, jadwal kalibrasi, spesifikasi alat, dan lokasi penempatan.</p>
                        </div>
                        <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-primary text-white">
                                    <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-1"></i> Formulir Inventaris & Penempatan Alat</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if(isset($_POST['Submit'])) {
                                        include_once("config.php");

                                        $nama_alat     = mysqli_real_escape_string($mysqli, trim($_POST['nama_alat']));
                                        $no_seri       = !empty($_POST['no_seri']) ? mysqli_real_escape_string($mysqli, trim($_POST['no_seri'])) : null;
                                        $tahun         = mysqli_real_escape_string($mysqli, trim($_POST['tahun']));
                                        $merek         = mysqli_real_escape_string($mysqli, trim($_POST['merek']));
                                        $lokasi        = mysqli_real_escape_string($mysqli, trim($_POST['lokasi']));
                                        $status        = mysqli_real_escape_string($mysqli, trim($_POST['status']));
                                        $penyewa       = isset($_POST['penyewa']) ? mysqli_real_escape_string($mysqli, trim($_POST['penyewa'])) : null;
                                        $tgl_kalibrasi = !empty($_POST['tgl_kalibrasi']) ? mysqli_real_escape_string($mysqli, trim($_POST['tgl_kalibrasi'])) : null;

                                        if ($status == 'Tersedia') {
                                            $penyewa = null;
                                        }

                                        // Proses Upload Gambar
                                        $gambar = null;
                                        $upload_error = "";
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
                                                        $gambar = $newFileName;
                                                    } else {
                                                        $upload_error = "Gagal menyimpan file gambar.";
                                                    }
                                                } else {
                                                    $upload_error = "Ukuran gambar terlalu besar (Maksimal 3MB).";
                                                }
                                            } else {
                                                $upload_error = "Format file tidak didukung! Gunakan JPG, PNG, atau WEBP.";
                                            }
                                        }

                                        if (!empty($upload_error)) {
                                            echo "<div class='alert alert-warning' role='alert'>
                                                    <i class='fas fa-exclamation-triangle mr-1'></i> $upload_error
                                                  </div>";
                                        } else {
                                            $insert_query = "INSERT INTO alat(nama_alat, no_seri, tahun, merek, lokasi, status, penyewa, gambar, tgl_kalibrasi) 
                                                             VALUES('$nama_alat', " . ($no_seri ? "'$no_seri'" : "NULL") . ", '$tahun', '$merek', '$lokasi', '$status', " . ($penyewa ? "'$penyewa'" : "NULL") . ", " . ($gambar ? "'$gambar'" : "NULL") . ", " . ($tgl_kalibrasi ? "'$tgl_kalibrasi'" : "NULL") . ")";
                                            $result = mysqli_query($mysqli, $insert_query);

                                            if($result) {
                                                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                                        <i class='fas fa-check-circle mr-1'></i> <strong>Berhasil!</strong> Data alat <strong>" . htmlspecialchars($nama_alat) . "</strong> berhasil ditambahkan. 
                                                        <a href='index.php' class='alert-link font-weight-bold ml-2'>Lihat di Tabel &rarr;</a>
                                                      </div>";
                                            } else {
                                                echo "<div class='alert alert-danger' role='alert'>
                                                        <i class='fas fa-exclamation-triangle mr-1'></i> Gagal menambahkan data: " . mysqli_error($mysqli) . "
                                                      </div>";
                                            }
                                        }
                                    }
                                    ?>

                                    <form action="add.php" method="post" name="form1" enctype="multipart/form-data">
                                        <div class="form-row">
                                            <div class="form-group col-md-8">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-heartbeat text-primary mr-1"></i> Nama Alat Elektromedis</label>
                                                <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Ventilator Medis, Pasien Monitor, USG 4D..." required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-barcode text-primary mr-1"></i> No. Seri (SN)</label>
                                                <input type="text" name="no_seri" class="form-control" placeholder="Contoh: SN-88392-A">
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-tag text-primary mr-1"></i> Merek / Pabrikan</label>
                                                <input type="text" name="merek" class="form-control" placeholder="Contoh: Philips, GE Healthcare, Mindray..." required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="far fa-calendar-alt text-primary mr-1"></i> Tahun Pengadaan</label>
                                                <input type="number" name="tahun" class="form-control" placeholder="Contoh: 2023" min="1990" max="2035" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-gray-800"><i class="fas fa-map-marker-alt text-danger mr-1"></i> Posisi Ruangan Saat Ini</label>
                                            <textarea name="lokasi" class="form-control" rows="2" maxlength="255" placeholder="Contoh: Gedung Rawat Inap Teratai Lantai 2, Ruang ICU Bed 03 (Sub-Unit Medis A)..." required></textarea>
                                            <small class="form-text text-muted">Tuliskan detail ruangan, gedung, atau nomor bed. Kapasitas diperbanyak hingga 255 karakter agar tidak terpotong.</small>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-info-circle text-primary mr-1"></i> Status Penggunaan</label>
                                                <select name="status" id="statusSelect" class="form-control font-weight-bold text-gray-900" onchange="togglePenyewaField()" required>
                                                    <option value="Tersedia" class="text-success">&#10004; Tersedia (Siap Digunakan)</option>
                                                    <option value="Disewa / Dipakai" class="text-warning">&#9873; Sedang Disewa / Dipakai</option>
                                                    <option value="Maintenance" class="text-danger">&#9881; Maintenance / Rusak</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-6" id="penyewaGroup">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-user-tag text-primary mr-1"></i> Penyewa / Penanggung Jawab Ruangan</label>
                                                <input type="text" name="penyewa" id="penyewaInput" class="form-control" maxlength="255" placeholder="Contoh: Unit ICU / Dr. Sarah / Ns. Rina">
                                                <small class="form-text text-muted">Isi nama peminjam/dokter/perawat jika alat sedang disewa (hingga 255 karakter).</small>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <!-- Jadwal Kalibrasi -->
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-calendar-check text-success mr-1"></i> Jatuh Tempo Kalibrasi Berikutnya</label>
                                                <input type="date" name="tgl_kalibrasi" class="form-control">
                                                <small class="form-text text-muted">Tanggal jatuh tempo uji kelayakan / kalibrasi alat medis.</small>
                                            </div>

                                            <!-- Upload Foto / Gambar Alat -->
                                            <div class="form-group col-md-6">
                                                <label class="font-weight-bold text-gray-800"><i class="fas fa-camera text-primary mr-1"></i> Foto / Gambar Alat Medis (Opsional)</label>
                                                <div class="custom-file">
                                                    <input type="file" name="gambar" class="custom-file-input" id="customFile" accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewImage(this)">
                                                    <label class="custom-file-label" for="customFile" id="fileLabel">Pilih file foto...</label>
                                                </div>
                                                <small class="form-text text-muted">Format: JPG, PNG, WEBP. Maks 3MB.</small>
                                            </div>
                                        </div>

                                        <!-- Preview Box -->
                                        <div class="mt-2 text-center" id="previewWrapper" style="display: none;">
                                            <div class="p-2 border rounded bg-light d-inline-block">
                                                <img id="imagePreview" src="#" alt="Preview Foto" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                                                <div class="small text-muted mt-1" id="previewFileName"></div>
                                            </div>
                                        </div>

                                        <hr class="mt-4">
                                        <button type="submit" name="Submit" class="btn btn-primary px-4 shadow-sm">
                                            <i class="fas fa-save mr-1"></i> Simpan Data Alat
                                        </button>
                                        <a href="index.php" class="btn btn-secondary px-3">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Petunjuk / Tips -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4 border-left-info">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-lightbulb mr-1"></i> Informasi Panduan</h6>
                                </div>
                                <div class="card-body small text-gray-700">
                                    <p><strong><i class="fas fa-certificate text-success mr-1"></i> Kalibrasi Alat:</strong></p>
                                    <p>Berdasarkan Permenkes, setiap alat medis wajib dikalibrasi minimal 1 tahun sekali untuk menjamin akurasi dan keselamatan pasien.</p>
                                    
                                    <hr>
                                    <p><strong>Status Ketersediaan:</strong></p>
                                    <ul>
                                        <li><strong class="text-success">Tersedia:</strong> Alat stanby dan siap dipinjamkan atau digunakan kapan saja.</li>
                                        <li><strong class="text-warning">Sedang Disewa / Dipakai:</strong> Alat sedang aktif dipakai di ruang perawatan.</li>
                                        <li><strong class="text-danger">Maintenance:</strong> Alat sedang dalam proses perbaikan atau kalibrasi.</li>
                                    </ul>
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
                penyewaInput.focus();
            } else {
                penyewaInput.required = false;
            }
        }

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
                fileLabel.innerText = 'Pilih file foto...';
            }
        }
    </script>
</body>
</html>
