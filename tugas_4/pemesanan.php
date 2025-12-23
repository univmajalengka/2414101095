<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Pemesanan Paket Wisata';

$packages = [];
$selectedPackage = null;
$dbError = null;
$pdo = null;

try {
    $pdo = get_pdo();
    $packages = $pdo->query('SELECT * FROM packages ORDER BY id ASC')->fetchAll();

    if (isset($_GET['paket'])) {
        $id = (int) $_GET['paket'];
        foreach ($packages as $pkg) {
            if ((int) $pkg['id'] === $id) {
                $selectedPackage = $pkg;
                break;
            }
        }
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $telp = trim($_POST['telp'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';
    $waktu = $_POST['waktu'] ?? '';
    $durasi = (int) ($_POST['durasi'] ?? 0);
    $peserta = (int) ($_POST['peserta'] ?? 0);
    $servicesInput = $_POST['services'] ?? [];
    $catatan = trim($_POST['catatan'] ?? '');
    $paketId = isset($_POST['paket_id']) ? (int) $_POST['paket_id'] : null;

    if ($nama === '') {
        $errors[] = 'Nama pemesan wajib diisi.';
    }
    if ($telp === '') {
        $errors[] = 'Nomor HP/Telp wajib diisi.';
    }
    if ($tanggal === '') {
        $errors[] = 'Tanggal pesan wajib diisi.';
    }
    if ($waktu === '') {
        $errors[] = 'Waktu pelaksanaan wajib diisi.';
    }
    if ($durasi <= 0) {
        $errors[] = 'Durasi perjalanan minimal 1 hari.';
    }
    if ($peserta <= 0) {
        $errors[] = 'Jumlah peserta minimal 1 orang.';
    }

    $servicePrices = service_prices();
    $selectedServices = array_values(array_intersect($servicesInput, array_keys($servicePrices)));

    if (count($selectedServices) === 0) {
        $errors[] = 'Pilih minimal satu layanan.';
    }

    $hargaPaket = 0;
    foreach ($selectedServices as $srv) {
        $hargaPaket += $servicePrices[$srv];
    }
    $totalTagihan = $hargaPaket * $durasi * $peserta;

    if (!$pdo) {
        $errors[] = 'Koneksi database belum tersedia.';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO bookings (customer_name, phone, order_date, start_time, duration_days, participants, services, package_price, total_amount, package_id, note) VALUES (:nama, :telp, :tanggal, :waktu, :durasi, :peserta, :services, :harga_paket, :total_tagihan, :paket_id, :catatan)');
            $stmt->execute([
                ':nama' => $nama,
                ':telp' => $telp,
                ':tanggal' => $tanggal,
                ':waktu' => $waktu,
                ':durasi' => $durasi,
                ':peserta' => $peserta,
                ':services' => implode(',', $selectedServices),
                ':harga_paket' => $hargaPaket,
                ':total_tagihan' => $totalTagihan,
                ':paket_id' => $paketId,
                ':catatan' => $catatan,
            ]);

            set_flash('success', 'Pemesanan berhasil disimpan.');
            redirect('pesanan.php');
        } catch (Throwable $e) {
            $errors[] = 'Gagal menyimpan ke database: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="relative">
  <div class="absolute inset-0 opacity-50">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(20,140,239,0.35),_transparent)]"></div>
  </div>

  <section class="relative z-10 mx-auto max-w-5xl px-6 py-12">
    <div class="mb-8">
      <p class="text-sm uppercase tracking-[0.35em] text-brand-200">Form Pemesanan</p>
      <h1 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Pesan Paket Wisata UMKM</h1>
      <p class="mt-2 max-w-3xl text-sm text-brand-100">
        Isi data lengkap, pilih layanan, dan biaya akan dihitung otomatis. Data valid akan tersimpan ke database MariaDB.
      </p>
    </div>

    <?php if ($dbError): ?>
      <div class="mb-6 rounded-xl border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        Tidak bisa terhubung ke database: <?= htmlspecialchars($dbError) ?>. Pastikan schema.sql sudah dijalankan.
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        <?php foreach ($errors as $err): ?>
          <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form id="formPemesanan" method="post" class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl backdrop-blur">
      <div class="grid gap-5 sm:grid-cols-2">
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Nama Pemesan</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" placeholder="Masukkan nama lengkap" required />
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Nomor HP / Telp</label>
          <input type="tel" name="telp" value="<?= htmlspecialchars($_POST['telp'] ?? '') ?>" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" placeholder="08xxxxxxxxxx" required />
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Tanggal Pesan</label>
          <input type="date" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" required />
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Waktu Pelaksanaan Perjalanan</label>
          <input type="time" name="waktu" value="<?= htmlspecialchars($_POST['waktu'] ?? '') ?>" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" required />
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Lama Perjalanan (Hari)</label>
          <input id="durasi" type="number" min="1" name="durasi" value="<?= htmlspecialchars($_POST['durasi'] ?? '1') ?>" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" required />
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Jumlah Peserta</label>
          <input id="peserta" type="number" min="1" name="peserta" value="<?= htmlspecialchars($_POST['peserta'] ?? '1') ?>" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" required />
        </div>
        <div class="space-y-2 sm:col-span-2">
          <label class="text-sm font-semibold text-white">Pilih Layanan</label>
          <div class="grid gap-3 md:grid-cols-3">
            <?php $services = service_prices(); ?>
            <?php foreach ($services as $key => $price): ?>
              <?php $checked = in_array($key, $_POST['services'] ?? [], true); ?>
              <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-brand-400/60">
                <input type="checkbox" name="services[]" value="<?= $key ?>" <?= $checked ? 'checked' : '' ?> class="h-5 w-5 rounded border-white/30 bg-slate-900 text-brand-500 focus:ring-brand-400" />
                <div>
                  <p class="font-semibold capitalize text-white"><?= $key ?></p>
                  <p class="text-sm text-brand-100"><?= format_rupiah($price) ?></p>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Harga Paket Perjalanan</label>
          <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
            <span id="hargaPaketDisplay" class="text-lg font-semibold text-white"><?= format_rupiah(0) ?></span>
            <input id="hargaPaket" type="hidden" name="harga_paket" value="0" />
            <span class="text-xs text-brand-100">Sum layanan</span>
          </div>
        </div>
        <div class="space-y-2">
          <label class="text-sm font-semibold text-white">Jumlah Tagihan</label>
          <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
            <span id="tagihanDisplay" class="text-lg font-semibold text-white"><?= format_rupiah(0) ?></span>
            <input id="tagihan" type="hidden" name="tagihan" value="0" />
            <span class="text-xs text-brand-100">Hari x Peserta x Harga Paket</span>
          </div>
        </div>
        <div class="space-y-2 sm:col-span-2">
          <label class="text-sm font-semibold text-white">Paket Wisata</label>
          <select name="paket_id" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30">
            <option value="">Pilih paket (opsional)</option>
            <?php foreach ($packages as $pkg): ?>
              <option value="<?= (int) $pkg['id'] ?>" <?= $selectedPackage && (int) $selectedPackage['id'] === (int) $pkg['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($pkg['title']) ?> · <?= htmlspecialchars($pkg['location']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="space-y-2 sm:col-span-2">
          <label class="text-sm font-semibold text-white">Catatan Tambahan (Opsional)</label>
          <textarea name="catatan" rows="3" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-500/30" placeholder="Permintaan khusus, alergi, dsb."><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <div class="text-xs text-brand-100">
          <p>Validasi wajib isi dilakukan di sisi klien & server.</p>
        </div>
        <button type="submit" class="inline-flex items-center rounded-full bg-brand-500 px-6 py-3 text-sm font-semibold text-white shadow-glow transition hover:bg-brand-400">
          Simpan Pemesanan
        </button>
      </div>
    </form>
  </section>
</main>

<script>
  const priceMap = <?= json_encode(service_prices(), JSON_PRETTY_PRINT) ?>;

  const form = document.getElementById('formPemesanan');
  const hargaPaketDisplay = document.getElementById('hargaPaketDisplay');
  const tagihanDisplay = document.getElementById('tagihanDisplay');
  const hargaPaketInput = document.getElementById('hargaPaket');
  const tagihanInput = document.getElementById('tagihan');

  function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
  }

  function hitung() {
    const durasi = parseInt(document.getElementById('durasi').value || '0', 10);
    const peserta = parseInt(document.getElementById('peserta').value || '0', 10);

    let hargaPaket = 0;
    document.querySelectorAll('input[name="services[]"]:checked').forEach((el) => {
      const key = el.value;
      hargaPaket += priceMap[key] || 0;
    });

    const total = hargaPaket * durasi * peserta;
    hargaPaketDisplay.textContent = formatRupiah(hargaPaket);
    tagihanDisplay.textContent = formatRupiah(total);
    hargaPaketInput.value = hargaPaket;
    tagihanInput.value = total;
  }

  document.querySelectorAll('input[name="services[]"]').forEach((el) => el.addEventListener('change', hitung));
  document.getElementById('durasi').addEventListener('input', hitung);
  document.getElementById('peserta').addEventListener('input', hitung);

  form.addEventListener('submit', (e) => {
    const servicesChecked = document.querySelectorAll('input[name="services[]"]:checked').length;
    if (!form.checkValidity() || servicesChecked === 0) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Data belum lengkap',
        text: servicesChecked === 0 ? 'Pilih minimal satu layanan.' : 'Pastikan seluruh field wajib terisi.',
      });
    }
  });

  // initial calculation on load
  hitung();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
