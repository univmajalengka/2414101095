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

$idProduk = intval($_GET['id_produk'] ?? $_POST['id_produk'] ?? 0);

if ($idProduk <= 0) {
  header('Location: index.php#produk');
  exit();
}

$produk = null;
$stmt = $conn->prepare('SELECT id_produk, nama_produk, deskripsi, harga_produk, gambar_produk FROM produk WHERE id_produk = ?');
$stmt->bind_param('i', $idProduk);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$produk) {
  $conn->close();
  header('Location: index.php#produk');
  exit();
}

$errors = [];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$payment = $_POST['payment'] ?? 'bank';
$quantityValue = max(1, intval($_POST['quantity'] ?? 1));
$basePrice = (float) $produk['harga_produk'];
$totalValue = $basePrice * $quantityValue;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($name === '') {
    $errors[] = 'Nama wajib diisi.';
  }

  if ($alamat === '') {
    $errors[] = 'Alamat wajib diisi.';
  }

  if ($payment === '') {
    $errors[] = 'Silakan pilih metode pembayaran.';
  }

  if ($quantityValue < 1) {
    $errors[] = 'Jumlah minimal 1.';
  }

  if (empty($errors)) {
    try {
      $invoice = 'INV-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
    } catch (Exception $ex) {
      $invoice = 'INV-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));
    }

    $deskripsiProduk = $produk['deskripsi'] ?? '';
    $gambarProduk = $produk['gambar_produk'] ?? '';

    $insert = $conn->prepare('INSERT INTO orders (invoice_code, id_produk, nama_produk, deskripsi_produk, gambar_produk, jumlah, harga_satuan, total_harga, nama_pembeli, email_pembeli, nomor_telepon, alamat_pembeli, metode_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

    if ($insert) {
      $insert->bind_param(
        'sisssiddsssss',
        $invoice,
        $produk['id_produk'],
        $produk['nama_produk'],
        $deskripsiProduk,
        $gambarProduk,
        $quantityValue,
        $basePrice,
        $totalValue,
        $name,
        $email,
        $phone,
        $alamat,
        $payment
      );

      if ($insert->execute()) {
        $insert->close();
        $conn->close();
        header('Location: success.php?invoice=' . urlencode($invoice));
        exit();
      }

      $errors[] = 'Terjadi kesalahan saat menyimpan pesanan. Silakan coba lagi.';
      $insert->close();
    } else {
      $errors[] = 'Gagal menyiapkan penyimpanan pesanan.';
    }
  }
}

$hargaFormatted = formatRupiah($basePrice);
$totalFormatted = formatRupiah($totalValue);
$productImage = productImageUrl($produk['gambar_produk'] ?? '');
$productDescription = trim($produk['deskripsi'] ?? '') === '' ? 'Belum ada deskripsi untuk produk ini.' : $produk['deskripsi'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout | RIVVORLD</title>
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
        <nav class="hidden items-center gap-3 md:flex">
          <a
            href="index.php"
            aria-label="Home"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.75L12 3l9 6.75v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18.75v-9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 21v-6h6v6" />
            </svg>
          </a>
          <a
            href="login.php"
            aria-label="Masuk"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 20.25a8.25 8.25 0 0115 0" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25v3" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 9.75h-3" />
            </svg>
          </a>
          <a
            href="kontak.php"
            aria-label="Kontak"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25v9a2.25 2.25 0 01-2.25 2.25h-13.5A2.25 2.25 0 013 17.25v-9" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25l-8.954 5.593a2.25 2.25 0 01-2.292 0L3 8.25" />
            </svg>
          </a>
        </nav>
        <a
          href="index.php#produk"
          aria-label="Kembali"
          class="inline-flex h-9 items-center justify-center rounded-full border border-neutral-200 px-4 text-sm font-medium text-neutral-700 transition hover:border-neutral-900 hover:text-black"
        >
          Kembali
        </a>
      </div>
    </header>

    <main class="mx-auto mt-10 flex max-w-5xl flex-col gap-10 px-4 pb-24">
      <section class="grid gap-8 md:grid-cols-[1.05fr_0.95fr]">
        <form
          id="checkout-form"
          action="checkout.php?id_produk=<?= e($produk['id_produk']) ?>"
          method="post"
          class="space-y-8 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm"
        >
          <h1 class="text-2xl font-semibold text-neutral-900">Checkout</h1>

          <?php if (!empty($errors)) : ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
              <ul class="list-disc space-y-1 pl-5">
                <?php foreach ($errors as $error) : ?>
                  <li><?= e($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <div class="space-y-6">
            <div class="space-y-4">
              <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Informasi kontak</h2>
              <label class="flex flex-col gap-2 text-xs uppercase tracking-wide text-neutral-500">
                Nama
                <input
                  type="text"
                  name="name"
                  placeholder="Nama lengkap"
                  value="<?= e($name) ?>"
                  required
                  class="rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-900 focus:outline-none"
                />
              </label>
              <label class="flex flex-col gap-2 text-xs uppercase tracking-wide text-neutral-500">
                Email
                <input
                  type="email"
                  name="email"
                  placeholder="nama@email.com"
                  value="<?= e($email) ?>"
                  class="rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-900 focus:outline-none"
                />
              </label>
              <label class="flex flex-col gap-2 text-xs uppercase tracking-wide text-neutral-500">
                Nomor telepon
                <input
                  type="tel"
                  name="phone"
                  placeholder="0812xxxxxxxx"
                  value="<?= e($phone) ?>"
                  class="rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-900 focus:outline-none"
                />
              </label>
              <label class="flex flex-col gap-2 text-xs uppercase tracking-wide text-neutral-500">
                Alamat pengiriman
                <textarea
                  name="alamat"
                  rows="3"
                  placeholder="Tuliskan alamat lengkap pengiriman"
                  required
                  class="rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-900 focus:outline-none"
                ><?= e($alamat) ?></textarea>
              </label>
            </div>

            <div class="space-y-4">
              <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Metode pembayaran</h2>
              <div class="space-y-3">
                <label class="flex items-center gap-3 rounded-xl border border-neutral-200 px-4 py-3 text-sm text-neutral-700 transition hover:border-neutral-900">
                  <input
                    type="radio"
                    name="payment"
                    value="bank"
                    <?= $payment === 'bank' ? 'checked' : '' ?>
                    class="h-4 w-4 border-neutral-300 text-neutral-900 focus:ring-neutral-900"
                  />
                  Transfer bank
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-neutral-200 px-4 py-3 text-sm text-neutral-700 transition hover:border-neutral-900">
                  <input
                    type="radio"
                    name="payment"
                    value="ewallet"
                    <?= $payment === 'ewallet' ? 'checked' : '' ?>
                    class="h-4 w-4 border-neutral-300 text-neutral-900 focus:ring-neutral-900"
                  />
                  E-wallet
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-neutral-200 px-4 py-3 text-sm text-neutral-700 transition hover:border-neutral-900">
                  <input
                    type="radio"
                    name="payment"
                    value="cod"
                    <?= $payment === 'cod' ? 'checked' : '' ?>
                    class="h-4 w-4 border-neutral-300 text-neutral-900 focus:ring-neutral-900"
                  />
                  Bayar di tempat (COD)
                </label>
              </div>
            </div>
          </div>

          <input type="hidden" name="id_produk" value="<?= e($produk['id_produk']) ?>" />
          <input type="hidden" id="base-price" value="<?= number_format($basePrice, 2, '.', '') ?>" />
          <input type="hidden" name="quantity" id="quantity-input" value="<?= e($quantityValue) ?>" />
          <input type="hidden" name="total" id="total-input" value="<?= number_format($totalValue, 2, '.', '') ?>" />

          <button
            type="submit"
            class="w-full rounded-full border border-neutral-900 px-4 py-3 text-sm font-semibold text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
          >
            Konfirmasi dan bayar
          </button>
        </form>

        <aside class="space-y-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
          <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Ringkasan pesanan</h2>
            <div class="mt-4 flex items-start gap-4">
              <div class="h-20 w-20 overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-50">
                <img src="<?= e($productImage) ?>" alt="<?= e($produk['nama_produk']) ?>" class="h-full w-full object-cover" />
              </div>
              <div class="flex-1 space-y-2 text-sm text-neutral-700">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p class="font-medium text-neutral-900"><?= e($produk['nama_produk']) ?></p>
                    <p class="text-xs text-neutral-500"><?= nl2br(e($productDescription)) ?></p>
                  </div>
                  <span id="product-price" class="text-neutral-900"><?= e($hargaFormatted) ?></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-neutral-500">Metode pembayaran</span>
                  <span id="payment-label"><?= e(paymentLabel($payment)) ?></span>
                </div>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-sm text-neutral-700">
              <span>Jumlah</span>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  id="decrement"
                  class="flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-lg leading-none transition hover:border-neutral-900"
                  aria-label="Kurangi jumlah"
                >
                  -
                </button>
                <span id="quantity" class="w-10 text-center font-medium"><?= e($quantityValue) ?></span>
                <button
                  type="button"
                  id="increment"
                  class="flex h-8 w-8 items-center justify-center rounded-full border border-neutral-300 text-lg leading-none transition hover:border-neutral-900"
                  aria-label="Tambah jumlah"
                >
                  +
                </button>
              </div>
            </div>
          </div>

          <div class="space-y-3 text-sm text-neutral-700">
            <div class="flex items-center justify-between text-neutral-900">
              <span>Total</span>
              <span id="total-price"><?= e($totalFormatted) ?></span>
            </div>
            <p class="text-xs text-neutral-500">Total akan menyesuaikan jumlah yang dipilih.</p>
          </div>

          <a
            href="index.php#produk"
            class="inline-flex w-full items-center justify-center rounded-full border border-neutral-200 px-4 py-3 text-sm font-semibold text-neutral-700 transition hover:border-neutral-900 hover:text-black"
          >
            Kembali ke katalog
          </a>
        </aside>
      </section>
    </main>

    <footer class="border-t border-neutral-200 py-6 text-center text-xs text-neutral-500">
      RIVVORLD (c) 2025
    </footer>
    <script>
      (function () {
        const basePriceInput = document.getElementById('base-price');
        const basePrice = basePriceInput ? parseFloat(basePriceInput.value) : 0;
        let quantity = <?= e($quantityValue) ?>;
        const productPriceEl = document.getElementById('product-price');
        const quantityEl = document.getElementById('quantity');
        const totalPriceEl = document.getElementById('total-price');
        const incrementBtn = document.getElementById('increment');
        const decrementBtn = document.getElementById('decrement');
        const quantityInput = document.getElementById('quantity-input');
        const totalInput = document.getElementById('total-input');
        const paymentLabelEl = document.getElementById('payment-label');
        const paymentRadios = document.querySelectorAll('input[name="payment"]');
        const paymentTexts = { bank: 'Transfer bank', ewallet: 'E-wallet', cod: 'Bayar di tempat (COD)' };

        const formatPrice = (value) =>
          new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
          }).format(value);

        const render = () => {
          const totalValue = Math.max(1, quantity) * basePrice;
          if (productPriceEl) {
            productPriceEl.textContent = formatPrice(basePrice);
          }
          if (quantityEl) {
            quantityEl.textContent = String(quantity);
          }
          if (totalPriceEl) {
            totalPriceEl.textContent = formatPrice(totalValue);
          }
          if (quantityInput) {
            quantityInput.value = String(quantity);
          }
          if (totalInput) {
            totalInput.value = totalValue.toFixed(2);
          }
        };

        if (incrementBtn) {
          incrementBtn.addEventListener('click', () => {
            quantity += 1;
            render();
          });
        }

        if (decrementBtn) {
          decrementBtn.addEventListener('click', () => {
            if (quantity > 1) {
              quantity -= 1;
              render();
            }
          });
        }

        if (paymentRadios.length && paymentLabelEl) {
          paymentRadios.forEach((radio) => {
            radio.addEventListener('change', () => {
              paymentLabelEl.textContent = paymentTexts[radio.value] || radio.value;
            });
          });
        }

        render();
      })();
    </script>
  </body>
</html>
