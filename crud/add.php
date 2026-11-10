<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tambah Data Alat - SIMRS</title>
    <link href="tampilan/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="tampilan/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-hospital-alt"></i></div>
                <div class="sidebar-brand-text mx-3">SIM RS</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="index.php"><i class="fas fa-fw fa-table"></i><span>Data Alat Elektromedis</span></a>
            </li>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <span class="h5 mb-0 text-gray-800 font-weight-bold">Sistem Informasi Manajemen Rumah Sakit</span>
                </nav>

                <!-- Container -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Tambah Data Alat Elektromedis</h1>
                        <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
                    </div>

                    <div class="card shadow mb-4 col-lg-8 p-0">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Data Baru</h6>
                        </div>
                        <div class="card-body">
                            <?php
                            if(isset($_POST['Submit'])) {
                                $nama_alat = $_POST['nama_alat'];
                                $tahun     = $_POST['tahun'];
                                $merek     = $_POST['merek'];
                                $lokasi    = $_POST['lokasi'];

                                include_once("config.php");

                                $result = mysqli_query($mysqli, "INSERT INTO alat(nama_alat,tahun,merek,lokasi) VALUES('$nama_alat','$tahun','$merek','$lokasi')");

                                if($result) {
                                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                            Data alat berhasil ditambahkan! <a href='index.php' class='alert-link'>Kembali ke Data Alat</a>
                                          </div>";
                                } else {
                                    echo "<div class='alert alert-danger' role='alert'>Gagal menambahkan data!</div>";
                                }
                            }
                            ?>

                            <form action="add.php" method="post" name="form1">
                                <div class="form-group">
                                    <label>Nama Alat</label>
                                    <input type="text" name="nama_alat" class="form-control" placeholder="Masukkan nama alat" required>
                                </div>
                                <div class="form-group">
                                    <label>Tahun</label>
                                    <input type="text" name="tahun" class="form-control" placeholder="Contoh: 2023" required>
                                </div>
                                <div class="form-group">
                                    <label>Merek</label>
                                    <input type="text" name="merek" class="form-control" placeholder="Masukkan merek alat" required>
                                </div>
                                <div class="form-group">
                                    <label>Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Ruang ICU / Poliklinik" required>
                                </div>
                                <button type="submit" name="Submit" class="btn btn-primary">Simpan Data</button>
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>