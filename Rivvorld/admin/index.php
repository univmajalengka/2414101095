<?php
include '../database/db.php';

date_default_timezone_set('Asia/Jakarta');

$produkList = [];
$sql = "SELECT id_produk, nama_produk, deskripsi, harga_produk, gambar_produk, created_at FROM produk ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $produkList[] = $row;
  }
  $result->free();
}

$conn->close();

function e($value)
{
  return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatRupiah($value)
{
  if ($value === null || $value === '') {
    return 'Rp0';
  }

  return 'Rp' . number_format((float) $value, 0, ',', '.');
}

$currentDateLabel = date('d F Y');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Produk | RIVVORLD Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Inter"', "ui-sans-serif", "system-ui", "sans-serif"],
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
            <h1 class="text-2xl font-semibold text-neutral-900">Manajemen Produk</h1>
            <p class="text-sm text-neutral-500">
              Kelola katalog produk RIVVORLD, mulai dari penambahan, pembaruan, hingga penghapusan.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <label class="relative flex items-center">
              <input
                type="text"
                id="search"
                placeholder="Cari produk..."
                class="w-52 rounded-full border border-neutral-200 bg-white px-4 py-2 pr-10 text-sm text-neutral-700 shadow-sm transition focus:border-neutral-900 focus:outline-none"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="absolute right-3 h-4 w-4 text-neutral-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
              </svg>
            </label>
            <a
              href="createProduk.php"
              class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
              Tambah Produk
            </a>
          </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
          <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Daftar produk</h2>
            <span class="text-xs font-medium text-neutral-400">Data per <?= e($currentDateLabel) ?></span>
          </div>
          <div class="overflow-x-auto">
            <table id="product-table" class="min-w-full text-left text-sm text-neutral-700">
              <thead class="bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500">
                <tr>
                  <th scope="col" class="px-6 py-3 font-semibold">Foto</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Nama Produk</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Deskripsi</th>
                  <th scope="col" class="px-6 py-3 font-semibold">Harga</th>
                  <th scope="col" class="px-6 py-3 font-semibold text-right">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($produkList) === 0) : ?>
                  <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-neutral-500">
                      Belum ada produk yang tersimpan. Tambahkan produk baru untuk mulai mengisi katalog.
                    </td>
                  </tr>
                <?php else : ?>
                  <?php foreach ($produkList as $produk) : ?>
                    <tr class="transition hover:bg-neutral-50">
                      <td class="px-6 py-4">
                        <?php if (!empty($produk['gambar_produk'])) : ?>
                          <img
                            src="../gambar/<?= e($produk['gambar_produk']) ?>"
                            alt="<?= e($produk['nama_produk']) ?>"
                            class="h-14 w-14 rounded-xl object-cover"
                          />
                        <?php else : ?>
                          <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-neutral-100 text-xs font-medium text-neutral-400">
                            No Img
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="px-6 py-4 font-medium text-neutral-900"><?= e($produk['nama_produk']) ?></td>
                      <td class="px-6 py-4 text-neutral-500">
                        <?= nl2br(e($produk['deskripsi'])) ?>
                      </td>
                      <td class="px-6 py-4 font-semibold text-neutral-900"><?= e(formatRupiah($produk['harga_produk'])) ?></td>
                      <td class="px-6 py-4">
                        <div class="flex justify-end gap-2">
                          <a
                            href="editProduk.php?id_produk=<?= e($produk['id_produk']) ?>"
                            class="inline-flex items-center gap-1 rounded-full border border-neutral-200 px-3 py-1.5 text-xs font-semibold text-neutral-600 transition hover:border-neutral-900 hover:text-neutral-900"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Edit
                          </a>
                          <form
                            action="../controller/ProdukController.php"
                            method="GET"
                            class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus produk ini?');"
                          >
                            <input type="hidden" name="id_produk" value="<?= e($produk['id_produk']) ?>" />
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
              Fitur tambah, edit, dan hapus produk kini tersambung ke database. Pastikan untuk melakukan pengujian setelah melakukan perubahan pada data produk.
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
        const search = document.getElementById('search');
        const rows = Array.from(document.querySelectorAll('#product-table tbody tr'));

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
