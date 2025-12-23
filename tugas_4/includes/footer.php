<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: '<?= $flash['type'] ?>',
        title: '<?= htmlspecialchars($flash['message'], ENT_QUOTES) ?>',
        showConfirmButton: false,
        timer: 2800,
        timerProgressBar: true,
      });
    });
  </script>
<?php endif; ?>
  </body>
</html>
