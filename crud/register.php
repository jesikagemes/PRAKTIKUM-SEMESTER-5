<?php
session_start();

// Jika sudah login, langsung alihkan ke halaman utama
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("Location: index.php");
    exit();
}

include_once("config.php");

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $nama             = trim($_POST['nama']);
    $username         = trim($_POST['username']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($nama) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 4) {
        $error = "Password minimal terdiri dari 4 karakter!";
    } else {
        // Cek apakah username sudah dipakai
        $stmt = mysqli_prepare($mysqli, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username sudah digunakan, silakan pilih username lain!";
        } else {
            // Enkripsi password menggunakan hash aman
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan akun baru
            $insert_stmt = mysqli_prepare($mysqli, "INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "sss", $nama, $username, $hashed_password);

            if (mysqli_stmt_execute($insert_stmt)) {
                // Alihkan ke halaman login dengan notifikasi sukses
                header("Location: login.php?pesan=registrasi_sukses");
                exit();
            } else {
                $error = "Terjadi kesalahan saat mendaftarkan akun. Silakan coba lagi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registrasi Akun - SIMRS Alat Elektromedis</title>

    <!-- Custom fonts for this template-->
    <link href="tampilan/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="tampilan/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center mb-4">
                                <div class="sidebar-brand-icon rotate-n-15 text-primary mb-2">
                                    <i class="fas fa-user-plus fa-3x"></i>
                                </div>
                                <h1 class="h4 text-gray-900 font-weight-bold">Daftar Akun Baru</h1>
                                <p class="text-muted small">Buat akun untuk mengakses Sistem SIMRS</p>
                            </div>

                            <!-- Pesan Error -->
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="register.php">
                                <div class="form-group">
                                    <label class="small text-gray-600 font-weight-bold mb-1">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control form-control-user"
                                        placeholder="Masukkan Nama Lengkap..." required 
                                        value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label class="small text-gray-600 font-weight-bold mb-1">Username</label>
                                    <input type="text" name="username" class="form-control form-control-user"
                                        placeholder="Pilih Username..." required 
                                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="small text-gray-600 font-weight-bold mb-1">Password</label>
                                        <input type="password" name="password" class="form-control form-control-user"
                                            placeholder="Password..." required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="small text-gray-600 font-weight-bold mb-1">Ulangi Password</label>
                                        <input type="password" name="confirm_password" class="form-control form-control-user"
                                            placeholder="Konfirmasi..." required>
                                    </div>
                                </div>
                                <button type="submit" name="register" class="btn btn-primary btn-user btn-block font-weight-bold mt-4">
                                    <i class="fas fa-user-check mr-1"></i> Daftarkan Akun
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <span class="small text-muted">Sudah punya akun? </span>
                                <a class="small font-weight-bold text-primary" href="login.php">Login di sini!</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="tampilan/vendor/jquery/jquery.min.js"></script>
    <script src="tampilan/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="tampilan/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="tampilan/js/sb-admin-2.min.js"></script>

</body>

</html>
