<?php
session_start();

// Jika sudah login, langsung alihkan ke halaman utama
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("Location: index.php");
    exit();
}

include_once("config.php");

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } else {
        // Cek username di database
        $stmt = mysqli_prepare($mysqli, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verifikasi password (mendukung hash password_hash atau plain-text untuk fleksibilitas praktikum)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = !empty($user['nama']) ? $user['nama'] : $user['username'];
                $_SESSION['status'] = "login";

                header("Location: index.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
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
    <title>Login - SIMRS Alat Elektromedis</title>

    <!-- Custom fonts for this template-->
    <link href="tampilan/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template-->
    <link href="tampilan/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center mb-4">
                                <div class="sidebar-brand-icon rotate-n-15 text-primary mb-2">
                                    <i class="fas fa-hospital-alt fa-3x"></i>
                                </div>
                                <h1 class="h4 text-gray-900 font-weight-bold">SIM RS ELEKTROMEDIS</h1>
                                <p class="text-muted small">Silakan masuk dengan akun Anda</p>
                            </div>

                            <!-- Pesan Notifikasi URL -->
                            <?php if (isset($_GET['pesan'])): ?>
                                <?php if ($_GET['pesan'] == "logout"): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        Anda telah berhasil logout.
                                    </div>
                                <?php elseif ($_GET['pesan'] == "belum_login"): ?>
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        Silakan login terlebih dahulu untuk mengakses sistem.
                                    </div>
                                <?php elseif ($_GET['pesan'] == "registrasi_sukses"): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        Registrasi berhasil! Silakan masuk dengan akun Anda.
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Pesan Error Form -->
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="login.php">
                                <div class="form-group">
                                    <label class="small text-gray-600 font-weight-bold mb-1">Username</label>
                                    <input type="text" name="username" class="form-control form-control-user"
                                        placeholder="Masukkan Username..." required autofocus 
                                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label class="small text-gray-600 font-weight-bold mb-1">Password</label>
                                    <input type="password" name="password" class="form-control form-control-user"
                                        placeholder="Masukkan Password..." required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary btn-user btn-block font-weight-bold">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <span class="small text-muted">Belum punya akun? </span>
                                <a class="small font-weight-bold text-primary" href="register.php">Daftar Akun Baru!</a>
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
