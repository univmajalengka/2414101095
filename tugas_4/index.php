<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Beranda | Paket Wisata Sanghyang Dora';

$packages = [];
$dbError = null;

try {
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT * FROM packages ORDER BY id ASC');
    $packages = $stmt->fetchAll();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="relative">
  <div class="absolute inset-0 opacity-60">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(20,140,239,0.35),_transparent)]"></div>
  </div>

  <section class="relative isolate overflow-hidden">
    <div class="absolute inset-0">
      <img src="assets/images/hero-1.jpg" class="h-full w-full object-cover opacity-30" alt="Panorama Sanghyang Dora" />
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-950/80 to-slate-950"></div>

    <div class="relative z-10 mx-auto flex max-w-6xl flex-col gap-8 px-6 py-16 sm:py-24">
      <div class="max-w-3xl">
        <p class="text-sm uppercase tracking-[0.35em] text-brand-200">Capstone Project · UMKM Wisata</p>
        <h1 class="mt-4 text-3xl font-semibold leading-tight text-white sm:text-5xl">
          Kelola paket wisata & pemesanan Sanghyang Dora dengan satu aplikasi sederhana
        </h1>
        <p class="mt-4 max-w-2xl text-lg text-brand-100">
          Lihat paket, hitung biaya otomatis, simpan data ke database, serta modifikasi pesanan dengan cepat.
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-4">
          <a href="pemesanan.php" class="inline-flex items-center rounded-full bg-brand-500 px-5 py-3 text-sm font-semibold text-white shadow-glow transition hover:bg-brand-400">Buka Form Pemesanan</a>
          <a href="pesanan.php" class="inline-flex items-center rounded-full bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">Lihat Daftar Pesanan</a>
        </div>
      </div>
      <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg">
          <p class="text-sm font-semibold text-brand-200">Realtime perhitungan</p>
          <p class="mt-2 text-sm text-slate-200">Harga paket & tagihan otomatis dari layanan, peserta, dan hari perjalanan.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg">
          <p class="text-sm font-semibold text-brand-200">Database ready</p>
          <p class="mt-2 text-sm text-slate-200">Semua pesanan tersimpan di MariaDB & siap untuk edit / hapus.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg">
          <p class="text-sm font-semibold text-brand-200">UI dari Tugas 1</p>
          <p class="mt-2 text-sm text-slate-200">Tampilan tetap modern terinspirasi halaman Sanghyang Dora.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="paket" class="relative z-10 mx-auto max-w-6xl px-6 py-16">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.35em] text-brand-200">Daftar Paket</p>
        <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Paket Wisata Unggulan</h2>
        <p class="mt-2 max-w-2xl text-sm text-brand-100">
          Gambar, deskripsi, serta tautan video promosi dari destinasi utama. Klik pesan untuk membuka form pemesanan.
        </p>
      </div>
      <a href="pemesanan.php" class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">Pesan Sekarang</a>
    </div>

    <?php if ($dbError): ?>
      <div class="mt-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        Gagal mengambil data paket dari database: <?= htmlspecialchars($dbError) ?>. Pastikan schema sudah diimport.
      </div>
    <?php endif; ?>

    <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <?php if ($packages): ?>
        <?php foreach ($packages as $pkg): ?>
          <article class="group relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900/60 shadow-lg transition hover:-translate-y-1 hover:shadow-glow">
            <div class="aspect-[4/3] overflow-hidden">
              <img src="<?= htmlspecialchars($pkg['image_path']) ?>" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" alt="Gambar <?= htmlspecialchars($pkg['title']) ?>" />
            </div>
            <div class="space-y-3 p-5">
              <div class="flex items-center justify-between gap-2">
                <h3 class="text-lg font-semibold text-white"><?= htmlspecialchars($pkg['title']) ?></h3>
                <span class="rounded-full bg-brand-500/20 px-3 py-1 text-xs font-semibold text-brand-100">
                  <?= (int) $pkg['duration_days'] ?> Hari
                </span>
              </div>
              <p class="text-sm text-brand-100 line-clamp-3"><?= htmlspecialchars($pkg['description']) ?></p>
              <div class="flex flex-wrap items-center gap-2 text-xs text-brand-100">
                <span class="rounded-full bg-white/5 px-3 py-1"><?= htmlspecialchars($pkg['location']) ?></span>
                <a href="<?= htmlspecialchars($pkg['youtube_url']) ?>" target="_blank" class="inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1 font-semibold text-white transition hover:bg-white/20">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="h-4 w-4"><path d="M10 15.5v-7l6 3.5-6 3.5Z" /></svg>
                  Video
                </a>
              </div>
              <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-white"><?= format_rupiah((float) $pkg['base_price']) ?> / pax</p>
                <a href="pemesanan.php?paket=<?= urlencode((string) $pkg['id']) ?>" class="inline-flex items-center rounded-full bg-brand-500 px-4 py-2 text-xs font-semibold text-white shadow-glow transition hover:bg-brand-400">Pesan Paket</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-sm text-brand-100">
          Belum ada data paket. Import <code>schema.sql</code> atau tambahkan langsung di tabel <code>packages</code>.
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="relative z-10 mx-auto max-w-6xl px-6 pb-16">
    <div class="grid items-center gap-10 rounded-3xl border border-white/10 bg-gradient-to-r from-slate-900/80 via-slate-900/60 to-brand-900/50 p-10 shadow-2xl">
      <div class="space-y-4">
        <p class="text-sm uppercase tracking-[0.35em] text-brand-200">Alur Aplikasi</p>
        <h3 class="text-2xl font-semibold text-white">3 langkah singkat</h3>
        <ul class="space-y-3 text-sm text-brand-100">
          <li class="flex items-start gap-3"><span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">1</span> Buka form pemesanan, pilih layanan (penginapan/transportasi/servis), isi peserta & hari.</li>
          <li class="flex items-start gap-3"><span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">2</span> Harga paket & jumlah tagihan dihitung otomatis sebelum dikirim.</li>
          <li class="flex items-start gap-3"><span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">3</span> Simpan ke database, lalu kelola lewat halaman daftar pesanan (edit / hapus).</li>
        </ul>
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-brand-500/30 bg-brand-500/10 p-5">
          <p class="text-sm font-semibold text-white">Validasi Form</p>
          <p class="mt-2 text-sm text-brand-100">Jika ada kolom kosong, SweetAlert akan menampilkan pesan wajib isi.</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
          <p class="text-sm font-semibold text-white">Edit & Delete</p>
          <p class="mt-2 text-sm text-brand-100">Tabel daftar pesanan menyediakan pre-filled form edit dan konfirmasi hapus.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
