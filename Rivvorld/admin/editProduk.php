<?php
include '../database/db.php';

$idProduk = intval($_GET['id_produk'] ?? 0);
$produk = null;

if ($idProduk > 0) {
  $sql = "SELECT id_produk, nama_produk, deskripsi, harga_produk, gambar_produk FROM produk WHERE id_produk=$idProduk";
  $result = $conn->query($sql);
  if ($result) {
    $produk = $result->fetch_assoc();
    $result->free();
  }
}

$conn->close();

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$produkNotFound = !$produk;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Produk | RIVVORLD Admin</title>
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
              <span class="rounded-full bg-neutral-900/5 px-3 py-1 font-medium text-neutral-900">
                Produk
              </span>
              <a
                href="orders.php"
                class="rounded-full px-3 py-1 font-medium text-neutral-500 transition hover:text-neutral-900"
              >
                Orders
              </a>
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
            <h1 class="text-2xl font-semibold text-neutral-900">Edit Produk</h1>
            <p class="text-sm text-neutral-500">
              Perbarui informasi produk untuk menjaga katalog tetap akurat.
            </p>
          </div>
          <a
            href="index.php"
            class="inline-flex items-center gap-2 rounded-full border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-600 transition hover:border-neutral-900 hover:text-neutral-900"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
          </a>
        </section>

        <?php if ($produkNotFound) : ?>
          <section class="rounded-2xl border border-neutral-200 bg-white p-8 text-center shadow-sm">
            <h2 class="text-lg font-semibold text-neutral-900">Produk tidak ditemukan</h2>
            <p class="mt-2 text-sm text-neutral-500">
              Data produk yang Anda cari tidak tersedia atau sudah dihapus. Silakan kembali ke daftar produk.
            </p>
            <div class="mt-4 flex justify-center">
              <a
                href="index.php"
                class="inline-flex items-center gap-2 rounded-full border border-neutral-200 px-5 py-2 text-sm font-semibold text-neutral-600 transition hover:border-neutral-900 hover:text-neutral-900"
              >
                Kembali ke Daftar Produk
              </a>
            </div>
          </section>
        <?php else : ?>
          <section class="space-y-6">
            <div class="rounded-2xl border border-neutral-200 bg-white p-8 shadow-sm">
              <form action="../controller/ProdukController.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="id_produk" value="<?= e($produk['id_produk']) ?>" />

                <div class="grid gap-4 md:grid-cols-2">
                  <div class="md:col-span-2">
                    <label for="nama_produk" class="mb-2 block text-sm font-medium text-neutral-700">Nama Produk</label>
                    <input
                      type="text"
                      id="nama_produk"
                      name="nama_produk"
                      value="<?= e($produk['nama_produk']) ?>"
                      class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-2 text-sm text-neutral-800 shadow-sm transition focus:border-neutral-900 focus:outline-none"
                      required
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label for="deskripsi" class="mb-2 block text-sm font-medium text-neutral-700">Deskripsi</label>
                    <textarea
                      id="deskripsi"
                      name="deskripsi"
                      rows="4"
                      class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-2 text-sm text-neutral-800 shadow-sm transition focus:border-neutral-900 focus:outline-none"><?= e($produk['deskripsi']) ?></textarea>
                  </div>

                  <div>
                    <label for="harga_produk" class="mb-2 block text-sm font-medium text-neutral-700">Harga Produk</label>
                    <div class="relative">
                      <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm text-neutral-400">Rp</span>
                      <input
                        type="number"
                        step="0.01"
                        min="0"
                        id="harga_produk"
                        name="harga_produk"
                        value="<?= e($produk['harga_produk']) ?>"
                        class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-2 pl-10 text-sm text-neutral-800 shadow-sm transition focus:border-neutral-900 focus:outline-none"
                        required
                      />
                    </div>
                  </div>

                  <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700">Gambar Saat Ini</label>
                    <?php if (!empty($produk['gambar_produk'])) : ?>
                      <img
                        src="../gambar/<?= e($produk['gambar_produk']) ?>"
                        alt="<?= e($produk['nama_produk']) ?>"
                        class="h-32 w-32 rounded-2xl border border-neutral-200 object-cover"
                      />
                    <?php else : ?>
                      <div class="flex h-32 w-32 items-center justify-center rounded-2xl border border-dashed border-neutral-300 text-xs font-medium text-neutral-400">
                        Tidak ada gambar
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="md:col-span-2">
                    <label for="gambar_produk" class="mb-2 block text-sm font-medium text-neutral-700">Ganti Gambar</label>
                    <input
                      type="file"
                      id="gambar_produk"
                      name="gambar_produk"
                      accept="image/*"
                      class="w-full cursor-pointer rounded-xl border border-dashed border-neutral-300 bg-neutral-50 px-4 py-3 text-sm text-neutral-600 transition hover:border-neutral-500"
                    />
                    <p class="mt-2 text-xs text-neutral-400">Kosongkan jika tidak ingin mengganti gambar.</p>
                  </div>
                </div>

                <div class="flex justify-end gap-3">
                  <a
                    href="index.php"
                    class="inline-flex items-center gap-2 rounded-full border border-neutral-200 px-5 py-2 text-sm font-semibold text-neutral-600 transition hover:border-neutral-900 hover:text-neutral-900"
                  >
                    Batal
                  </a>
                  <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                  </button>
                </div>
              </form>
            </div>
          </section>
        <?php endif; ?>
      </main>

      <footer class="border-t border-neutral-200 bg-white py-4 text-center text-xs text-neutral-500">
        RIVVORLD Admin Dashboard (c) 2025
      </footer>
    </div>
  </body>
</html>







