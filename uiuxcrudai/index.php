<?php
include_once("config1.php");
$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMRS - Data Alat Elektromedis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <h2>🏥 Data Alat Elektromedis</h2>
                    <p>Sistem Informasi Manajemen Rumah Sakit (SIMRS)</p>
                </div>
                <a href="add.php" class="btn btn-primary">+ Tambah Alat</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Alat</th>
                            <th>Merek</th>
                            <th>Tahun</th>
                            <th>Lokasi</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                        $i = 1;
                        if(mysqli_num_rows($result) > 0) {
                            while($user_data = mysqli_fetch_array($result)) {         
                                echo "<tr>";
                                echo "<td>".$i++."</td>";
                                echo "<td><strong>".$user_data['nama_alat']."</strong></td>";
                                echo "<td>".$user_data['merek']."</td>";    
                                echo "<td><span class='badge'>".$user_data['tahun']."</span></td>";
                                echo "<td><span class='badge badge-lokasi'>📍 ".$user_data['lokasi']."</span></td>";    
                                echo "<td>
                                        <a href='edit.php?id=$user_data[id]' class='btn btn-edit'>Edit</a> 
                                        <a href='delete1.php?id=$user_data[id]' class='btn btn-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                                      </td>";
                                echo "</tr>"; 
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding: 24px; color: #94a3b8;'>Belum ada data alat elektromedis.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>

<!-- Desain UI/UX CRUD dibuat menggunakan bantuan AI -->