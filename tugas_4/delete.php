<?php
require_once __DIR__ . '/includes/init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    set_flash('error', 'ID pesanan tidak valid.');
    redirect('pesanan.php');
}

try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM bookings WHERE id = :id');
    $stmt->execute([':id' => $id]);
    set_flash('success', 'Pesanan berhasil dihapus.');
} catch (Throwable $e) {
    set_flash('error', 'Gagal menghapus: ' . $e->getMessage());
}

redirect('pesanan.php');
