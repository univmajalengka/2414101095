<?php
include 'database/db.php';

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatRupiah($value)
{
  return 'Rp' . number_format((float) $value, 0, ',', '.');
}

function productImageUrl($filename)
{
  if (empty($filename)) {
    return 'https://via.placeholder.com/400x400?text=Produk';
  }

  return 'gambar/' . rawurlencode($filename);
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

$invoice = trim($_GET['invoice'] ?? '');

if ($invoice === '') {
  header('Location: index.php#produk');
  exit();
}

$stmt = $conn->prepare('SELECT o.*, p.gambar_produk AS gambar_produk_db, p.deskripsi AS deskripsi_produk_db FROM orders o LEFT JOIN produk p ON p.id_produk = o.id_produk WHERE o.invoice_code = ? LIMIT 1');
$stmt->bind_param('s', $invoice);
$stmt->execute();
$result = $stmt->get_result();
$order = $result ? $result->fetch_assoc() : null;
$stmt->close();
$conn->close();

if (!$order) {
  header('Location: index.php#produk');
  exit();
}

$namaPembeli = $order['nama_pembeli'] ?: 'Pelanggan';
$emailPembeli = $order['email_pembeli'] ?: '-';
$teleponPembeli = $order['nomor_telepon'] ?: '-';
alamatPembeli = trim($order['alamat_pembeli'] ?? '') ?: '-';
$paymentText = paymentLabel($order['metode_pembayaran']);
$namaProduk = $order['nama_produk'];
$deskripsiProduk = $order['deskripsi_produk'] ?: $order['deskripsi_produk_db'] ?: 'Belum ada deskripsi untuk produk ini.';
$quantity = max(1, (int) $order['jumlah']);
$hargaSatuan = (float) $order['harga_satuan'];
$totalHarga = (float) $order['total_harga'];
$subtotal = $hargaSatuan * $quantity;
$gambarProduk = $order['gambar_produk'] ?: $order['gambar_produk_db'];
$gambarUrl = productImageUrl($gambarProduk);
$createdAt = $order['created_at'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pembayaran Berhasil | RIVVORLD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
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
  <body class="bg-white font-sans text-neutral-900 antialiased">
    <header class="border-b border-neutral-200 bg-white/95 backdrop-blur">
      <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
        <a href="index.php" class="text-base font-semibold tracking-tight">RIVVORLD</a>
        <a
          href="index.php#produk"
          class="inline-flex h-9 items-center justify-center rounded-full border border-neutral-200 px-4 text-sm font-medium text-neutral-700 transition hover:border-neutral-900 hover:text-black"
        >
          Kembali ke katalog
        </a>
      </div>
    </header>

    <main class="mx-auto flex min-h-[calc(100vh-120px)] max-w-5xl flex-col px-4 py-12">
      <div class="grid gap-8 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm md:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-6">
          <div class="space-y-3">
            <span class="inline-flex items-center gap-2 self-start rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
              <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
              Pembayaran diterima
            </span>
            <h1 class="text-3xl font-semibold">
              Terima kasih, <?= e($namaPembeli) ?>!
            </h1>
            <p class="text-sm text-neutral-600">
              Pesanan kamu sudah kami terima dan sedang diproses. Detail pesanan kamu kami rangkum di bawah ini.
              <?php if ($createdAt) : ?>
                <span class="block text-xs text-neutral-400">Dibuat pada <?= e(date('d F Y H:i', strtotime($createdAt))) ?> · <?= e($order['invoice_code']) ?></span>
              <?php else : ?>
                <span class="block text-xs text-neutral-400">Nomor pesanan: <?= e($order['invoice_code']) ?></span>
              <?php endif; ?>
            </p>
          </div>

          <div class="space-y-4 rounded-2xl border border-neutral-200 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Informasi pelanggan</h2>
            <dl class="space-y-3 text-sm text-neutral-700">
              <div class="flex items-center justify-between">
                <dt class="text-neutral-500">Nama</dt>
                <dd><?= e($namaPembeli) ?></dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-neutral-500">Email</dt>
                <dd><?= e($emailPembeli) ?></dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-neutral-500">Nomor telepon</dt>
                <dd><?= e($teleponPembeli) ?></dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-neutral-500">Alamat pengiriman</dt>
                <dd class="text-right text-neutral-600 max-w-xs"><?= nl2br(e($alamatPembeli)) ?></dd>
              </div>
              <div class="flex items-center justify-between">
                <dt class="text-neutral-500">Metode pembayaran</dt>
                <dd><?= e($paymentText) ?></dd>
              </div>
            </dl>
          </div>

          <div class="space-y-3 text-xs text-neutral-500">
            <p>
              Bukti transaksi juga telah kami kirim ke email terdaftar. Jika ada pertanyaan, hubungi tim kami di
              <a href="mailto:support@rivworld.id" class="underline transition hover:text-black">support@rivworld.id</a>.
            </p>
            <p>Terima kasih sudah berbelanja di RIVVORLD.</p>
          </div>
        </section>

        <aside class="space-y-6">
          <div class="space-y-4 rounded-2xl border border-neutral-200 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Detail pesanan</h2>
            <div class="flex items-start gap-4">
              <div class="h-16 w-16 overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50">
                <img src="<?= e($gambarUrl) ?>" alt="<?= e($namaProduk) ?>" class="h-full w-full object-cover" />
              </div>
              <div class="flex-1 space-y-3 text-sm text-neutral-700">
                <div class="flex items-start justify-between">
                  <div>
                    <p id="product-name" class="font-medium text-neutral-900"><?= e($namaProduk) ?></p>
                    <p class="text-xs text-neutral-500"><?= nl2br(e($deskripsiProduk)) ?></p>
                  </div>
                  <span id="product-unit-price" class="text-neutral-900"><?= e(formatRupiah($hargaSatuan)) ?></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-neutral-500">Jumlah</span>
                  <span id="product-quantity" class="font-medium"><?= e($quantity) ?> pcs</span>
                </div>
              </div>
            </div>
          </div>

          <div class="space-y-3 rounded-2xl border border-neutral-200 p-6 text-sm text-neutral-700">
            <div class="flex items-center justify-between">
              <span>Subtotal</span>
              <span id="product-subtotal"><?= e(formatRupiah($subtotal)) ?></span>
            </div>
            <div class="flex items-center justify-between text-neutral-500">
              <span>Pengiriman</span>
              <span>Gratis</span>
            </div>
            <div class="flex items-center justify-between text-lg font-semibold text-neutral-900">
              <span>Total dibayar</span>
              <span id="product-total"><?= e(formatRupiah($totalHarga)) ?></span>
            </div>
          </div>

          <div class="space-y-3">
            <a
              href="index.php"
              class="inline-flex w-full items-center justify-center rounded-full border border-neutral-200 px-4 py-3 text-sm font-semibold text-neutral-700 transition hover:border-neutral-900 hover:text-black"
            >
              Kembali ke beranda
            </a>
            <a
              href="checkout.php?id_produk=<?= e($order['id_produk']) ?>"
              class="inline-flex w-full items-center justify-center rounded-full border border-neutral-900 px-4 py-3 text-sm font-semibold text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
            >
              Buat pesanan baru
            </a>
          </div>
        </aside>
      </div>
    </main>

    <footer class="border-t border-neutral-200 py-6 text-center text-xs text-neutral-500">
      RIVVORLD (c) 2025
    </footer>
  </body>
</html>
