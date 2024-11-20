<?php
error_reporting(E_ALL);
include_once 'koneksi.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga_jual = $_POST['harga_jual'];
    $harga_beli = $_POST['harga_beli'];
    $stok = $_POST['stok'];
    $file_gambar = $_FILES['file_gambar'];
    $gambar = null;

    // Validasi input
    if (empty($nama) || empty($kategori) || !is_numeric($harga_jual) || 
        !is_numeric($harga_beli) || !is_numeric($stok)) {
        die("Semua kolom harus diisi dan harga serta stok harus berupa angka.");
    }

    // Validasi file gambar
    if ($file_gambar['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file_gambar['type'], $allowed_types)) {
            die("Tipe file tidak diperbolehkan. Hanya .jpg, .jpeg, dan .png yang diizinkan.");
        }

        $filename = str_replace(' ', '_', $file_gambar['name']);
        $destination = dirname(__FILE__) . '/gambar/' . $filename;

        if (move_uploaded_file($file_gambar['tmp_name'], $destination)) {
            $gambar = 'gambar/' . $filename;
        } else {
            die("Gagal mengunggah gambar.");
        }
    }

    // Insert data menggunakan prepared statement
    $sql = "INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiis", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar);

    if ($stmt->execute()) {
        header('location: index.php');
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <title>Tambah Barang</title>
</head>
<body>
    <div class="container">
        <h1>Tambah Barang</h1>
        <div class="main">
            <form method="post" action="tambah.php" enctype="multipart/form-data">
                <div class="input">
                    <label>Nama Barang</label>
                    <input type="text" name="nama" required />
                </div>
                <div class="input">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="Komputer">Komputer</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Hand Phone">Hand Phone</option>
                    </select>
                </div>
                <div class="input">
                    <label>Harga Jual</label>
                    <input type="text" name="harga_jual" required />
                </div>
                <div class="input">
                    <label>Harga Beli</label>
                    <input type="text" name="harga_beli" required />
                </div>
                <div class="input">
                    <label>Stok</label>
                    <input type="text" name="stok" required />
                </div>
                <div class="input">
                    <label>File Gambar</label>
                    <input type="file" name="file_gambar" />
                </div>
                <div class="submit">
                    <input type="submit" name="submit" value="Simpan" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>
