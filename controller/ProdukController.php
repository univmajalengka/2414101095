<?php
if (isset($_POST['createProduk'])) {
  include '../database/db.php';

  $namaProduk = $conn->real_escape_string($_POST['nama_produk'] ?? '');
  $deskripsiProduk = $conn->real_escape_string($_POST['deskripsi'] ?? '');
  $hargaProduk = $conn->real_escape_string($_POST['harga_produk'] ?? '0');

  $gambarProduk = $_FILES['gambar_produk']['name'] ?? '';
  $tempGambar = $_FILES['gambar_produk']['tmp_name'] ?? '';
  $errorUpload = $_FILES['gambar_produk']['error'] ?? UPLOAD_ERR_NO_FILE;
  $namaFileDisimpan = '';

  if ($errorUpload === UPLOAD_ERR_OK && is_uploaded_file($tempGambar)) {
    $targetDir = realpath(__DIR__ . '/../gambar');
    if ($targetDir === false) {
      $targetDir = __DIR__ . '/../gambar';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
      }
    }
    $targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
    $namaFileDisimpan = basename($gambarProduk);
    $targetFile = $targetDir . DIRECTORY_SEPARATOR . $namaFileDisimpan;

    if (!move_uploaded_file($tempGambar, $targetFile)) {
      $conn->close();
      echo "Error upload gambar.";
      exit;
    }
  } elseif ($errorUpload !== UPLOAD_ERR_NO_FILE) {
    $conn->close();
    echo "Error upload gambar.";
    exit;
  }

  $namaFileDisimpanEscaped = $conn->real_escape_string($namaFileDisimpan);

  $sql = "INSERT INTO produk (nama_produk, deskripsi, harga_produk, gambar_produk) VALUES ('$namaProduk', '$deskripsiProduk', '$hargaProduk', '$namaFileDisimpanEscaped')";

  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Produk berhasil disimpan.'); window.location.href='../admin/index.php';</script>";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}
?>

<?php
if (isset($_GET['id_produk'])) {
  include '../database/db.php';

  $idProduk = intval($_GET['id_produk']);

  $sqlSelect = "SELECT gambar_produk FROM produk WHERE id_produk=$idProduk";
  $result = $conn->query($sqlSelect);
  $row = $result ? $result->fetch_assoc() : null;
  $gambarProduk = $row['gambar_produk'] ?? '';

  $conn->begin_transaction();

  $stmtDeleteOrders = $conn->prepare('DELETE FROM orders WHERE id_produk = ?');
  if ($stmtDeleteOrders) {
    $stmtDeleteOrders->bind_param('i', $idProduk);
    if (!$stmtDeleteOrders->execute()) {
      $conn->rollback();
      echo "Error: " . $conn->error;
      $stmtDeleteOrders->close();
      $conn->close();
      exit;
    }
    $stmtDeleteOrders->close();
  } else {
    $conn->rollback();
    echo "Error: " . $conn->error;
    $conn->close();
    exit;
  }

  $sql = "DELETE FROM produk WHERE id_produk=$idProduk";

  if ($conn->query($sql) === TRUE) {
    $conn->commit();
    if (!empty($gambarProduk)) {
      $targetDir = realpath(__DIR__ . '/../gambar');
      if ($targetDir === false) {
        $targetDir = __DIR__ . '/../gambar';
      }
      $targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
      $filePath = $targetDir . DIRECTORY_SEPARATOR . basename($gambarProduk);
      if (file_exists($filePath)) {
        unlink($filePath);
      }
    }
    $conn->close();
    echo "<script>alert('Produk berhasil dihapus.'); window.location.href='../admin/index.php';</script>";
    exit;
  } else {
    $conn->rollback();
    echo "Error: " . $conn->error;
  }

  $conn->close();
}
?>

<?php
if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  isset($_POST['id_produk']) &&
  !isset($_POST['createProduk'])
) {
  include '../database/db.php';

  $id_produk = intval($_POST['id_produk']);
  $nama = $conn->real_escape_string($_POST['nama_produk'] ?? '');
  $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');
  $harga = $conn->real_escape_string($_POST['harga_produk'] ?? '0');

  $gambarBaru = $_FILES['gambar_produk']['name'] ?? '';
  $tmpGambarBaru = $_FILES['gambar_produk']['tmp_name'] ?? '';
  $errorUpload = $_FILES['gambar_produk']['error'] ?? UPLOAD_ERR_NO_FILE;

  $updateGambar = false;
  $namaFileBaru = '';
  $targetDir = null;

  if ($errorUpload === UPLOAD_ERR_OK && is_uploaded_file($tmpGambarBaru)) {
    $targetDir = realpath(__DIR__ . '/../gambar');
    if ($targetDir === false) {
      $targetDir = __DIR__ . '/../gambar';
      if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
      }
    }
    $targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
    $namaFileBaru = basename($gambarBaru);
    $targetFile = $targetDir . DIRECTORY_SEPARATOR . $namaFileBaru;

    if (!move_uploaded_file($tmpGambarBaru, $targetFile)) {
      $conn->close();
      echo "Error upload gambar.";
      exit;
    }
    $updateGambar = true;
    $namaFileBaru = $conn->real_escape_string($namaFileBaru);
  } elseif ($errorUpload !== UPLOAD_ERR_NO_FILE) {
    $conn->close();
    echo "Error upload gambar.";
    exit;
  }

  if ($updateGambar) {
    $sqlOld = "SELECT gambar_produk FROM produk WHERE id_produk=$id_produk";
    $resOld = $conn->query($sqlOld);
    $rowOld = $resOld ? $resOld->fetch_assoc() : null;
    $oldImage = $rowOld['gambar_produk'] ?? '';

    $sql = "UPDATE produk
            SET nama_produk='$nama', deskripsi='$deskripsi', harga_produk='$harga', gambar_produk='$namaFileBaru'
            WHERE id_produk=$id_produk";

    if ($conn->query($sql) === TRUE) {
      if (!empty($oldImage)) {
        if ($targetDir === null) {
          $targetDir = realpath(__DIR__ . '/../gambar');
          if ($targetDir === false) {
            $targetDir = __DIR__ . '/../gambar';
          }
          $targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
        }
        $oldPath = $targetDir . DIRECTORY_SEPARATOR . basename($oldImage);
        if (file_exists($oldPath)) {
          unlink($oldPath);
        }
      }
      $conn->close();
      echo "<script>alert('Produk berhasil diperbarui!'); window.location.href='../admin/index.php';</script>";
      exit;
    } else {
      echo "Error: " . $conn->error;
    }
  } else {
    $sql = "UPDATE produk
            SET nama_produk='$nama', deskripsi='$deskripsi', harga_produk='$harga'
            WHERE id_produk=$id_produk";

    if ($conn->query($sql) === TRUE) {
      $conn->close();
      echo "<script>alert('Produk berhasil diperbarui!'); window.location.href='../admin/index.php';</script>";
      exit;
    } else {
      echo "Error: " . $conn->error;
    }
  }

  $conn->close();
}
?>









