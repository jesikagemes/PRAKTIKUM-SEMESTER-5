<?php
// include database connection file
include_once("config.php");

// Check if form is submitted for user update, then redirect to homepage after update
if(isset($_POST['update']))
{   
    $id = $_POST['id'];
    $nama_alat= $_POST['nama_alat'];
    $tahun = $_POST['tahun'];
    $merek= $_POST['merek'];
    $lokasi = $_POST['lokasi'];

    // update user data
    $result = mysqli_query($mysqli, "UPDATE alat SET nama_alat='$nama_alat',tahun='$tahun',merek='$merek',lokasi='$lokasi' WHERE id=$id");

    // Redirect to homepage to display updated user in list
    header("Location: index.php");
}

// Display selected user data based on id
$id = $_GET['id'];
$result = mysqli_query($mysqli, "SELECT * FROM alat WHERE id=$id");

while($user_data = mysqli_fetch_array($result))
{
    $nama_alat = $user_data['nama_alat'];
    $tahun = $user_data['tahun'];
    $merek= $user_data['merek'];
    $lokasi = $user_data['lokasi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Edit Data Alat - SIMRS</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Edit Data Alat Elektromedis</h1>
                        <a href="index.php" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
                    </div>

                    <div class="card shadow mb-4 col-lg-8 p-0">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Form Perubahan Data</h6>
                        </div>
                        <div class="card-body">
                            <form name="update_user" method="post" action="edit.php">
                                <div class="form-group">
                                    <label>Nama Alat</label>
                                    <input type="text" name="nama_alat" class="form-control" value="<?php echo $nama_alat;?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Tahun</label>
                                    <input type="text" name="tahun" class="form-control" value="<?php echo $tahun;?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Merek</label>
                                    <input type="text" name="merek" class="form-control" value="<?php echo $merek;?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Lokasi</label>
                                    <input type="text" name="lokasi" class="form-control" value="<?php echo $lokasi;?>" required>
                                </div>
                                <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                                <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
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