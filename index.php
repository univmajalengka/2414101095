<?php
include 'database/db.php';

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

function shortenText($value, $limit = 120)
{
  $text = trim($value ?? '');
  if ($text === '') {
    return htmlspecialchars('Deskripsi belum tersedia.', ENT_QUOTES, 'UTF-8');
  }

  if (function_exists('mb_strlen')) {
    if (mb_strlen($text) > $limit) {
      $text = mb_substr($text, 0, $limit) . '…';
    }
  } elseif (strlen($text) > $limit) {
    $text = substr($text, 0, $limit) . '…';
  }

  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function productImageUrl($filename)
{
  if (empty($filename)) {
    return 'https://via.placeholder.com/400x400?text=Produk';
  }

  return 'gambar/' . rawurlencode($filename);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RIVVORLD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['"Inter"', "ui-sans-serif", "system-ui", "sans-serif"],
                },
            },
        },
    };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
</head>

<body class="bg-white font-sans text-neutral-900 antialiased">
    <header class="sticky top-0 z-20 border-b border-neutral-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-5xl flex-col px-4">
            <nav class="flex h-16 items-center justify-between">
                <a href="#" class="text-base font-semibold tracking-tight">RIVVORLD</a>
                <div class="hidden items-center gap-6 text-sm md:flex">
                    <a href="login.php" aria-label="Register"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.5 20.25a8.25 8.25 0 0115 0" />
                        </svg>
                    </a>
                    <a href="kontak.php" aria-label="Kontak"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 8.25v9a2.25 2.25 0 01-2.25 2.25h-13.5A2.25 2.25 0 013 17.25v-9" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 8.25l-8.954 5.593a2.25 2.25 0 01-2.292 0L3 8.25" />
                        </svg>
                    </a>
                </div>
                <button id="menu-button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 text-neutral-900 transition hover:border-neutral-400 md:hidden"
                    type="button" aria-expanded="false" aria-controls="mobile-menu">
                    <span class="sr-only">Open menu</span>
                    <svg data-menu="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg data-menu="close" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </nav>
            <div id="mobile-menu" class="hidden border-t border-neutral-200 py-4 md:hidden">
                <div class="flex flex-col items-center gap-3 text-sm">
                    <a href="login.php"
                        class="inline-flex w-full items-center justify-center rounded-full border border-neutral-200 px-4 py-2 text-neutral-700 transition hover:border-neutral-900 hover:text-black">Masuk</a>
                    <a href="kontak.php"
                        class="inline-flex w-full items-center justify-center rounded-full border border-neutral-200 px-4 py-2 text-neutral-700 transition hover:border-neutral-900 hover:text-black">Kontak</a>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto mt-16 flex max-w-5xl flex-col gap-20 px-4 pb-24">
        <section class="grid gap-10 md:grid-cols-[1.2fr_0.8fr]">
            <div class="flex flex-col justify-center gap-6">
                <span
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-neutral-200 px-3 py-1 text-xs font-medium uppercase tracking-wide text-neutral-500">
                    Koleksi Terbaru
                </span>
                <h1 class="text-4xl font-semibold text-neutral-900 md:text-5xl">
                    Rancang gaya, ciptakan cerita, wujudkan identitas Anda.
                </h1>
                <p class="text-base leading-relaxed text-neutral-600">
                    Temukan kurasi busana kontemporer dengan bahan premium dan detail presisi. Didesain untuk memberi
                    kenyamanan tanpa mengorbankan karakter personal Anda.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="#produk"
                        class="inline-flex items-center justify-center rounded-full bg-neutral-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700">Jelajahi
                        Produk</a>
                    <a href="kontak.php"
                        class="inline-flex items-center justify-center rounded-full border border-neutral-300 px-5 py-2 text-sm font-semibold text-neutral-700 transition hover:border-neutral-900 hover:text-neutral-900">Hubungi
                        Kami</a>
                </div>
            </div>
            <div class="relative">
                <div class="aspect-square overflow-hidden rounded-3xl border border-neutral-200 bg-neutral-100">
                    <img src="https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=900&q=80"
                        alt="Model koleksi RIVVORLD" class="h-full w-full object-cover" />
                </div>
                <div
                    class="absolute -bottom-6 -left-6 hidden w-36 rounded-2xl border border-neutral-200 bg-white/90 p-4 text-xs font-medium text-neutral-700 shadow-lg md:block">
                    "Setiap potongan menyimpan cerita pribadi"
                </div>
            </div>
        </section>

        <section id="produk" class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-neutral-900">Koleksi Unggulan</h2>
                    <p class="text-sm text-neutral-500">Produk pilihan yang siap melengkapi gaya harian Anda.</p>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php if (count($produkList) === 0) : ?>
                <div
                    class="col-span-full rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500">
                    Produk belum tersedia. Kunjungi lagi nanti untuk melihat koleksi terbaru kami.
                </div>
                <?php else : ?>
                <?php foreach ($produkList as $produk) : ?>
                <?php
                $imageUrl = productImageUrl($produk['gambar_produk'] ?? '');
                $hargaTeks = formatRupiah($produk['harga_produk']);
                $deskripsiTeks = shortenText($produk['deskripsi'] ?? '');
              ?>
                                <a
                  href="checkout.php?id_produk=<?= e($produk['id_produk']) ?>"
                  class="group flex flex-col rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                >
                  <div class="aspect-square overflow-hidden rounded-xl bg-neutral-100">
                    <img
                      src="<?= e($imageUrl) ?>"
                      alt="<?= e($produk['nama_produk']) ?>"
                      class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                      loading="lazy"
                    />
                  </div>
                  <div class="mt-4 flex justify-between text-sm">
                    <div>
                      <h3 class="font-medium text-neutral-900"><?= e($produk['nama_produk']) ?></h3>
                      <p class="mt-1 text-neutral-500"><?= $deskripsiTeks ?></p>
                    </div>
                    <span class="text-neutral-800"><?= e($hargaTeks) ?></span>
                  </div>
                  <span
                    class="mt-5 inline-flex items-center justify-center rounded-full border border-neutral-900 px-4 py-2 text-xs font-semibold text-neutral-900 transition group-hover:bg-neutral-900 group-hover:text-white"
                  >
                    Beli Sekarang
                  </span>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var menuButton = document.getElementById("menu-button");
        var mobileMenu = document.getElementById("mobile-menu");
        if (!menuButton || !mobileMenu) {
            return;
        }

        var openIcon = menuButton.querySelector('[data-menu="open"]');
        var closeIcon = menuButton.querySelector('[data-menu="close"]');

        var toggleMenu = function() {
            var isExpanded = menuButton.getAttribute("aria-expanded") === "true";
            menuButton.setAttribute("aria-expanded", (!isExpanded).toString());
            mobileMenu.classList.toggle("hidden");
            if (openIcon && closeIcon) {
                openIcon.classList.toggle("hidden");
                closeIcon.classList.toggle("hidden");
            }
        };

        menuButton.addEventListener("click", toggleMenu);

        document.addEventListener("keydown", function(event) {
            if (
                event.key === "Escape" &&
                menuButton.getAttribute("aria-expanded") === "true"
            ) {
                toggleMenu();
            }
        });

        mobileMenu.querySelectorAll("a").forEach(function(link) {
            link.addEventListener("click", function() {
                if (menuButton.getAttribute("aria-expanded") === "true") {
                    toggleMenu();
                }
            });
        });
    });
    </script>
</body>

</html>
