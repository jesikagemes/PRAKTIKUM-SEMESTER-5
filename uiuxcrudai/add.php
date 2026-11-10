<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alat Elektromedis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" style="max-width: 550px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h2>Tambah Alat Baru</h2>
                    <p>Isi formulir di bawah ini untuk menambah data alat.</p>
                </div>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>

            <?php
            if(isset($_POST['Submit'])) {
                $nama_alat = $_POST['nama_alat'];
                $tahun     = $_POST['tahun'];
                $merek     = $_POST['merek'];
                $lokasi    = $_POST['lokasi'];

                include_once("config1.php");

                $result = mysqli_query($mysqli, "INSERT INTO alat(nama_alat,tahun,merek,lokasi) VALUES('$nama_alat','$tahun','$merek','$lokasi')");

                echo "<div class='alert-success'>Data alat berhasil ditambahkan! <a href='index.php' style='color: #15803d; font-weight: bold;'>Lihat Data</a></div>";
            }
            ?>

            <form action="add.php" method="post" name="form1">
                <div class="form-group">
                    <label>Nama Alat</label>
                    <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: ECG Monitor" required>
                </div>
                
                <div class="form-group">
                    <label>Merek</label>
                    <input type="text" name="merek" class="form-control" placeholder="Contoh: Philips" required>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control" placeholder="Contoh: 2023" required>
                </div>

                <div class="form-group">
                    <label>Lokasi / Ruangan</label>
                    <input type="text" name="lokasi" class="form-control" placeholder="Contoh: ICU" required>
                </div>

                <div style="margin-top: 24px;">
                    <input type="submit" name="Submit" value="Simpan Data" class="btn btn-primary" style="width: 100%;">
                </div>
            </form>
        </div>
    </div>

</body>
</html>