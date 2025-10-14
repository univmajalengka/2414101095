<?php
include '../database/db.php';

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatRupiah($value)
{
  return 'Rp' . number_format((float) $value, 0, ',', '.');
}

function paymentLabel($value)
{
  $labels = [
    'bank' => 'Transfer bank',
    'ewallet' => 'E-wallet',
    'cod' => 'Bayar di tempat (COD)',
  ];

  return $labels[$value] ?? ucfirst($value);
}

$flashType = '';
$flashMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
  $idOrder = intval($_POST['id_order'] ?? 0);

  if ($idOrder > 0) {
    $stmtDelete = $conn->prepare('DELETE FROM orders WHERE id_order = ?');

    if ($stmtDelete) {
      $stmtDelete->bind_param('i', $idOrder);

      if ($stmtDelete->execute()) {
        $stmtDelete->close();
        $conn->close();
        header('Location: orders.php?status=deleted');
        exit();
      }

      $flashType = 'error';
      $flashMessage = 'Gagal menghapus pesanan. Silakan coba lagi.';
      $stmtDelete->close();
    } else {
      $flashType = 'error';
      $flashMessage = 'Tidak dapat memproses penghapusan pesanan.';
    }
  } else {
    $flashType = 'error';
    $flashMessage = 'Pesanan tidak valid.';
  }
}

if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
  $flashType = 'success';
  $flashMessage = 'Pesanan berhasil dihapus.';
}

$orders = [];
$sql = 'SELECT o.*, p.nama_produk AS produk_terkini FROM orders o LEFT JOIN produk p ON p.id_produk = o.id_produk ORDER BY o.created_at DESC';
$result = $conn->query($sql);

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
  $result->free();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Orders | RIVVORLD Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
              brand: {
                50: '#f4f3ff',
                100: '#eae8ff',
                500: '#4f46e5',
                600: '#4338ca',
                900: '#1e1b4b',
              },
            },
          },
        },
      };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
  </head>
  <body class="bg-neutral-50 font-sans text-neutral-900 antialiased">
    <div class="flex min-h-screen flex-col">
      <header class="border-b border-neutral-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
          <div class="flex items-center gap-6">
            <a href="../index.php" class="text-lg font-semibold tracking-tight text-neutral-900">
              RIVVORLD Admin
            </a>
            <nav class="hidden gap-2 text-sm text-neutral-500 sm:flex">
              <a
                href="index.php"
                class="rounded-full px-3 py-1 font-medium text-neutral-500 transition hover:text-neutral-900"
              >
                Produk
              </a>
              <span class="rounded-full bg-neutral-900/5 px-3 py-1 font-medium text-neutral-900">
                Orders
              </span>
            </nav>
          </div>
          <div class="flex items-center gap-3 text-sm">
            <span class="hidden text-neutral-500 sm:inline">Admin</span>
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-neutral-900/5 text-xs font-semibold text-neutral-700">
              AD
            </div>
          </div>
        </div>
      </header>

      <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 px-6 py-8">
        <section class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl font-semibold text-neutral-900">Manajemen Orders</h1>
            <p class="text-sm text-neutral-500">Pantau order terbaru, update status pembayaran, dan kelola informasi pelanggan.</p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <label class="relative flex items-center">
              <input
                type="text"
                id="order-search"
                placeholder="Cari order atau pelanggan..."
                class="w-52 rounded-full border border-neutral-200 bg-white px-4 py-2 pr-10 text-sm text-neutral-700 shadow-sm transition focus:border-neutral-900 focus:outline-none"
              />
              <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
              </svg>
            </label>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 transition hover:border-neutral-900 hover:text-neutral-900"
            >
              Export CSV
            </button>
          </div>
        </section>

        <?php if (!empty($flashMessage)) : ?>
          <div class="rounded-2xl border <?php echo $flashType === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'; ?> px-6 py-4 text-sm">
            <?php echo e($flashMessage); ?>
          </div>
        <?php endif; ?>

        <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
          <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Daftar orders</h2>
            <span class="text-xs font-medium text-neutral-400">Update per <?= e(date('d F Y')) ?></span>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-neutral-700">
              <thead class="bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                <tr>
                  <th scope="col" class="px-6 py-3 font-semibold">Produk</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Jumlah</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Total Harga</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Nama Pembeli</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Kontak</th>
                  <th scope="col" class="px-6 py-3 font-semibold text-right">Aksi</th>
                </tr>
              </thead>
              <tbody id="order-table" class="divide-y divide-neutral-100">
                <?php if (count($orders) === 0) : ?>
                  <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-neutral-500">Belum ada pesanan yang tercatat.</td>
                  </tr>
                <?php else : ?>
                  <?php foreach ($orders as $order) : ?>
                    <?php
                      $invoice = $order['invoice_code'];
                      $productName = $order['produk_terkini'] ?: $order['nama_produk'];
                      $quantity = max(1, (int) $order['jumlah']);
                      $total = formatRupiah($order['total_harga']);
                      $buyer = $order['nama_pembeli'];
                      $paymentText = paymentLabel($order['metode_pembayaran']);
                      $email = trim($order['email_pembeli'] ?? '');
                      $phone = trim($order['nomor_telepon'] ?? '');
                      $alamat = trim($order['alamat_pembeli'] ?? '');
                      $created = $order['created_at'] ? date('d M Y H:i', strtotime($order['created_at'])) : '';
                    ?>
                    <tr class="transition hover:bg-neutral-50">
                      <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                          <span class="font-medium text-neutral-900"><?= e($productName) ?></span>
                          <span class="text-xs text-neutral-500">Order #<?= e($invoice) ?><?= $created ? ' · ' . e($created) : '' ?></span>
                        </div>
                      </td>
                      <td class="px-6 py-4 font-medium text-neutral-900"><?= e($quantity) ?> pcs</td>
                      <td class="px-6 py-4 font-semibold text-neutral-900"><?= e($total) ?></td>
                      <td class="px-6 py-4">
                        <div class="flex flex-col">
                          <span class="font-medium text-neutral-900"><?= e($buyer) ?></span>
                          <span class="text-xs text-neutral-400">Dibayar via <?= e($paymentText) ?></span>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-neutral-600">
                        <div class="flex flex-col gap-1 text-sm">
                          <?php if ($email !== '') : ?>
                            <a href="mailto:<?= e($email) ?>" class="hover:text-neutral-900"><?= e($email) ?></a>
                          <?php endif; ?>
                          <?php if ($phone !== '') : ?>
                            <a href="tel:<?= e($phone) ?>" class="text-xs text-neutral-400 hover:text-neutral-700"><?= e($phone) ?></a>
                          <?php endif; ?>
                          <?php if ($alamat !== '') : ?>
                            <span class="text-xs text-neutral-400">Alamat: <?= nl2br(e($alamat)) ?></span>
                          <?php endif; ?>
                          <?php if ($email === '' && $phone === '' && $alamat === '') : ?>
                            <span class="text-xs text-neutral-400">Tidak ada kontak</span>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex justify-end">
                          <form
                            action="orders.php"
                            method="POST"
                            class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');"
                          >
                            <input type="hidden" name="id_order" value="<?= e($order['id_order']) ?>" />
                            <input type="hidden" name="delete_order" value="1" />
                            <button
                              type="submit"
                              class="inline-flex items-center gap-1 rounded-full border border-red-100 px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:border-red-500 hover:bg-red-50"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4.5a1.5 1.5 0 00-1.5-1.5h-4A1.5 1.5 0 008 4.5V7m5 0H7" />
                              </svg>
                              Hapus
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <section class="grid gap-4 rounded-2xl border border-dashed border-neutral-200 bg-white p-6 text-sm text-neutral-500">
          <div class="flex flex-col gap-2">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Catatan admin</h2>
            <p>
              Data orders kini tersimpan di database. Integrasikan aksi hapus atau ubah status untuk melengkapi alur pengelolaan pesanan.
            </p>
          </div>
        </section>
      </main>

      <footer class="border-t border-neutral-200 bg-white py-4 text-center text-xs text-neutral-500">
        RIVVORLD Admin Dashboard (c) 2025
      </footer>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const search = document.getElementById('order-search');
        const rows = Array.from(document.querySelectorAll('#order-table tr'));

        if (!search || rows.length === 0) {
          return;
        }

        const normalise = (value) => value.toLowerCase().trim();

        search.addEventListener('input', () => {
          const keyword = normalise(search.value);

          rows.forEach((row) => {
            const text = normalise(row.textContent || '');
            row.classList.toggle('hidden', keyword.length > 0 && !text.includes(keyword));
          });
        });
      });
    </script>
  </body>
</html>
