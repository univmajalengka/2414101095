<?php
require_once __DIR__ . '/includes/init.php';

$pageTitle = 'Daftar Pemesanan';

$dbError = null;
$orders = [];

try {
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT b.*, p.title AS package_title FROM bookings b LEFT JOIN packages p ON b.package_id = p.id ORDER BY b.id DESC');
    $orders = $stmt->fetchAll();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="relative">
  <div class="absolute inset-0 opacity-50">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(20,140,239,0.35),_transparent)]"></div>
  </div>

  <section class="relative z-10 mx-auto max-w-6xl px-6 py-12">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <p class="text-sm uppercase tracking-[0.35em] text-brand-200">Modifikasi Pesanan</p>
        <h1 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Daftar Semua Pemesanan</h1>
        <p class="mt-2 text-sm text-brand-100">Edit atau hapus data pesanan yang tersimpan di database.</p>
      </div>
      <a href="pemesanan.php" class="inline-flex items-center rounded-full bg-brand-500 px-5 py-3 text-sm font-semibold text-white shadow-glow transition hover:bg-brand-400">Tambah Pesanan</a>
    </div>

    <?php if ($dbError): ?>
      <div class="mt-6 rounded-xl border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-100">
        Tidak bisa mengambil data: <?= htmlspecialchars($dbError) ?>.
      </div>
    <?php endif; ?>

    <?php if (!$orders): ?>
      <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-6 text-sm text-brand-100">
        Belum ada pesanan. Tambahkan melalui halaman pemesanan.
      </div>
    <?php else: ?>
      <div class="mt-8 overflow-hidden rounded-3xl border border-white/10 bg-slate-900/70 shadow-2xl">
        <table class="min-w-full divide-y divide-white/10 text-sm">
          <thead class="bg-white/5 text-xs uppercase tracking-wide text-brand-200">
            <tr>
              <th class="px-4 py-3 text-left font-semibold">Pemesan</th>
              <th class="px-4 py-3 text-left font-semibold">Kontak</th>
              <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
              <th class="px-4 py-3 text-left font-semibold">Layanan</th>
              <th class="px-4 py-3 text-left font-semibold">Harga Paket</th>
              <th class="px-4 py-3 text-left font-semibold">Tagihan</th>
              <th class="px-4 py-3 text-left font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 text-brand-100">
            <?php foreach ($orders as $order): ?>
              <tr class="hover:bg-white/5">
                <td class="px-4 py-3">
                  <p class="font-semibold text-white"><?= htmlspecialchars($order['customer_name']) ?></p>
                  <?php if ($order['package_title']): ?>
                    <p class="text-xs text-brand-200">Paket: <?= htmlspecialchars($order['package_title']) ?></p>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-3">
                  <p><?= htmlspecialchars($order['phone']) ?></p>
                  <p class="text-xs text-brand-200">Peserta: <?= (int) $order['participants'] ?></p>
                </td>
                <td class="px-4 py-3">
                  <p><?= htmlspecialchars($order['order_date']) ?>, <?= htmlspecialchars($order['start_time']) ?></p>
                  <p class="text-xs text-brand-200">Durasi <?= (int) $order['duration_days'] ?> hari</p>
                </td>
                <td class="px-4 py-3 text-xs capitalize">
                  <?php $srv = array_filter(explode(',', $order['services'])); ?>
                  <div class="flex flex-wrap gap-1">
                    <?php foreach ($srv as $item): ?>
                      <span class="rounded-full bg-white/10 px-2 py-1 text-[11px] text-brand-100"><?= htmlspecialchars($item) ?></span>
                    <?php endforeach; ?>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm font-semibold text-white"><?= format_rupiah((float) $order['package_price']) ?></td>
                <td class="px-4 py-3 text-sm font-semibold text-white"><?= format_rupiah((float) $order['total_amount']) ?></td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-2">
                    <a href="edit_pesanan.php?id=<?= (int) $order['id'] ?>" class="inline-flex items-center rounded-full bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20">Edit</a>
                    <button type="button" data-id="<?= (int) $order['id'] ?>" data-name="<?= htmlspecialchars($order['customer_name']) ?>" class="btnDelete inline-flex items-center rounded-full bg-red-500/80 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-500">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>

<script>
  document.querySelectorAll('.btnDelete').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const name = btn.dataset.name;
      Swal.fire({
        title: 'Hapus pesanan?',
        text: `Pesanan atas nama ${name} akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#475569',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `delete.php?id=${id}`;
        }
      });
    });
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
