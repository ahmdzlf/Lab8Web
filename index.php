<?php
include("koneksi.php");
$sql = 'SELECT * FROM data_barang';
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Data Barang</title>
</head>
<body>
    <h1>Data Barang</h1>
    <table border="1">
        <tr>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_array($result)): ?>
        <tr>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['kategori'] ?></td>
            <td><?= $row['harga_beli'] ?></td>
            <td><?= $row['harga_jual'] ?></td>
            <td><?= $row['stok'] ?></td>
            <td><a href="ubah.php?id=<?= $row['id_barang'] ?>">Ubah</a> | <a href="hapus.php?id=<?= $row['id_barang'] ?>">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
