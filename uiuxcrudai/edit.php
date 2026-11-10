<?php
include_once("config1.php");

if(isset($_POST['update']))
{   
    $id        = $_POST['id'];
    $nama_alat = $_POST['nama_alat'];
    $tahun     = $_POST['tahun'];
    $merek     = $_POST['merek'];
    $lokasi    = $_POST['lokasi'];

    $result = mysqli_query($mysqli, "UPDATE alat SET nama_alat='$nama_alat',tahun='$tahun',merek='$merek',lokasi='$lokasi' WHERE id=$id");

    header("Location: index.php");
}

$id = $_GET['id'];
$result = mysqli_query($mysqli, "SELECT * FROM alat WHERE id=$id");

while($user_data = mysqli_fetch_array($result))
{
    $nama_alat = $user_data['nama_alat'];
    $tahun     = $user_data['tahun'];
    $merek     = $user_data['merek'];
    $lokasi    = $user_data['lokasi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Alat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" style="max-width: 550px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h2>Edit Data Alat</h2>
                    <p>Perbarui rincian data alat elektromedis.</p>
                </div>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>

            <form name="update_user" method="post" action="edit.php">
                <div class="form-group">
                    <label>Nama Alat</label>
                    <input type="text" name="nama_alat" class="form-control" value="<?php echo $nama_alat;?>" required>
                </div>

                <div class="form-group">
                    <label>Merek</label>
                    <input type="text" name="merek" class="form-control" value="<?php echo $merek;?>" required>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="<?php echo $tahun;?>" required>
                </div>

                <div class="form-group">
                    <label>Lokasi / Ruangan</label>
                    <input type="text" name="lokasi" class="form-control" value="<?php echo $lokasi;?>" required>
                </div>

                <div style="margin-top: 24px;">
                    <input type="hidden" name="id" value=<?php echo $_GET['id'];?>>
                    <input type="submit" name="update" value="Simpan Perubahan" class="btn btn-primary" style="width: 100%;">
                </div>
            </form>
        </div>
    </div>

</body>
</html>