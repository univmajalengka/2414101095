<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kontak | Monochrome Apparel</title>
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
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
  </head>
  <body class="bg-white font-sans text-neutral-900 antialiased">
    <header class="border-b border-neutral-200 bg-white/95 backdrop-blur">
      <div
        class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4"
      >
        <a href="index.php" class="text-base font-semibold tracking-tight"
          >RIVVORLD</a
        >
        <nav class="hidden items-center gap-3 md:flex">
          <a
            href="index.php"
            aria-label="Home"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M3 9.75L12 3l9 6.75v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18.75v-9z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M9 21v-6h6v6"
              />
            </svg>
          </a>
          <a
            href="login.php"
            aria-label="Register"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M4.5 20.25a8.25 8.25 0 0115 0"
              />
            </svg>
          </svg>
          <a
            href="kontak.php"
            aria-label="Kontak"
            aria-current="page"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-900 text-neutral-900 transition hover:bg-neutral-900 hover:text-white"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M21 8.25v9a2.25 2.25 0 01-2.25 2.25h-13.5A2.25 2.25 0 013 17.25v-9"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.5"
                d="M21 8.25l-8.954 5.593a2.25 2.25 0 01-2.292 0L3 8.25"
              />
            </svg>
          </a>
        </nav>
        <a
          href="index.php"
          aria-label="Kembali"
          class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black md:hidden"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M15.75 19.5L8.25 12l7.5-7.5"
            />
          </svg>
        </a>
      </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-12">
      <section class="space-y-6 text-center">
        <h1 class="text-3xl font-semibold">Mari terhubung</h1>
        <p class="mx-auto max-w-2xl text-sm text-neutral-600">
          Tim kami siap membantu soal pemesanan, kolaborasi, atau pertanyaan
          produk. Kirim pesan melalui form atau gunakan kontak langsung di
          bawah.
        </p>
      </section>

      <section class="mt-12 grid gap-10">
        <aside
          class="space-y-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm"
        >
          <div>
            <h2
              class="text-sm font-semibold uppercase tracking-wide text-neutral-500"
            >
              Butuh cepat?
            </h2>
            <p class="mt-2 text-sm text-neutral-700">
              Hubungi kami melalui kanal berikut untuk respon kilat.
            </p>
          </div>
          <div class="space-y-3 text-sm text-neutral-700">
            <p>
              Email:
              <a
                href="encejay7@gmail.com"
                class="underline transition hover:text-black"
                >encejay7@gmail.com</a
              >
            </p>
            <p>
              Telepon:
              <a
                href="tel:+6281214888448"
                class="underline transition hover:text-black"
                >+62 812 1488 8448</a
              >
            </p>
            <p>Jam layanan: Senin - Jumat, 09.00 - 18.00 WIB</p>
          </div>
          <div class="space-y-3 text-sm text-neutral-700">
            <h3 class="text-xs uppercase tracking-wide text-neutral-500">
              ALAMAT
            </h3>
            <p>Majalengka, Malausma</p>
            <a
              href="#"
              target="_blank"
              rel="noreferrer"
              class="inline-flex w-max items-center gap-2 text-xs uppercase tracking-wide text-neutral-900 transition hover:text-black"
            >
              Lihat peta
              <span aria-hidden="true">-></span>
            </a>
          </div>
        </aside>
      </section>
    </main>

    <footer
      class="border-t border-neutral-200 py-6 text-center text-xs text-neutral-500"
    >
      RIVVORLD (c) 2025
    </footer>
  </body>
</html>






