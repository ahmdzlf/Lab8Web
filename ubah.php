<?php
error_reporting(E_ALL);
include_once 'koneksi.php';

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
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
        die("Semua kolom harus diisi dengan benar dan harga serta stok harus berupa angka.");
    }

    // Validasi dan proses file gambar
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

    // Update data menggunakan prepared statement
    $sql = "UPDATE data_barang 
            SET nama = ?, kategori = ?, harga_jual = ?, harga_beli = ?, stok = ?";
    if (!empty($gambar)) {
        $sql .= ", gambar = ?";
    }
    $sql .= " WHERE id_barang = ?";

    $stmt = $conn->prepare($sql);
    if (!empty($gambar)) {
        $stmt->bind_param("ssiiisi", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar, $id);
    } else {
        $stmt->bind_param("ssiiis", $nama, $kategori, $harga_jual, $harga_beli, $stok, $id);
    }

    if ($stmt->execute()) {
        header('location: index.php');
    } else {
        echo "Gagal memperbarui data: " . $stmt->error;
    }
}

// Ambil data barang berdasarkan ID
$id = $_GET['id'];
$sql = "SELECT * FROM data_barang WHERE id_barang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) die('Error: Data tidak tersedia');
$data = $result->fetch_assoc();

// Fungsi untuk menentukan opsi yang dipilih
function is_select($var, $val) {
    return $var == $val ? 'selected="selected"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <title>Ubah Barang</title>
</head>
<body>
    <div class="container">
        <h1>Ubah Barang</h1>
        <div class="main">
            <form method="post" action="ubah.php" enctype="multipart/form-data">
                <div class="input">
                    <label>Nama Barang</label>
                    <input type="text" name="nama" value="<?php echo $data['nama']; ?>" required />
                </div>
                <div class="input">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option <?php echo is_select('Komputer', $data['kategori']); ?> value="Komputer">Komputer</option>
                        <option <?php echo is_select('Elektronik', $data['kategori']); ?> value="Elektronik">Elektronik</option>
                        <option <?php echo is_select('Hand Phone', $data['kategori']); ?> value="Hand Phone">Hand Phone</option>
                    </select>
                </div>
                <div class="input">
                    <label>Harga Jual</label>
                    <input type="text" name="harga_jual" value="<?php echo $data['harga_jual']; ?>" required />
                </div>
                <div class="input">
                    <label>Harga Beli</label>
                    <input type="text" name="harga_beli" value="<?php echo $data['harga_beli']; ?>" required />
                </div>
                <div class="input">
                    <label>Stok</label>
                    <input type="text" name="stok" value="<?php echo $data['stok']; ?>" required />
                </div>
                <div class="input">
                    <label>File Gambar</label>
                    <input type="file" name="file_gambar" />
                </div>
                <div class="submit">
                    <input type="hidden" name="id" value="<?php echo $data['id_barang']; ?>" />
                    <input type="submit" name="submit" value="Simpan" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>
