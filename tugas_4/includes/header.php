<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Aplikasi Paket Wisata UMKM';
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Plus Jakarta Sans"', "ui-sans-serif", "system-ui"],
            },
            colors: {
              brand: {
                50: "#eef9ff",
                100: "#cfefff",
                200: "#9cdfff",
                300: "#63c9ff",
                400: "#2eabff",
                500: "#148cef",
                600: "#0b6ecd",
                700: "#0e57a4",
                800: "#104a85",
                900: "#113b68",
              },
            },
            boxShadow: {
              glow: "0 15px 50px rgba(20,140,239,0.25)",
            },
          },
        },
      };
    </script>
    <style>
      body {
        font-family: "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
          sans-serif;
        background-color: #030712;
      }
    </style>
  </head>
  <body class="bg-slate-950 text-slate-100 antialiased">
