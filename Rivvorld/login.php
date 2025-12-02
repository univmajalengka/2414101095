<?php
require_once 'controller/authLogin.php'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | RIVVORLD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif']
                }
            }
        }
    };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
</head>

<body class="bg-white font-sans text-neutral-900 antialiased">
    <header class="border-b border-neutral-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-4">
            <a href="index.php" class="text-base font-semibold tracking-tight">RIVVORLD</a>
            <a href="index.php" aria-label="Kembali"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-700 transition hover:border-neutral-900 hover:text-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
        </div>
    </header>

    <main class="mx-auto flex min-h-[calc(100vh-120px)] max-w-4xl flex-col items-center justify-center px-4 py-12">
        <section class="w-full max-w-md space-y-8">
            <div class="space-y-3 text-center">
                <h1 class="text-3xl font-semibold">Masuk ke RIVVORLD</h1>
                <p class="text-sm text-neutral-600">Gunakan username dan kata sandi Anda untuk melanjutkan.</p>
            </div>

            <form action="" method="POST"
                class="space-y-5 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
                <label class="flex flex-col gap-2 text-left text-xs uppercase tracking-wide text-neutral-500">
                    Username
                    <input type="text" name="username" placeholder="Username"
                        class="rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-900 focus:outline-none"
                        required />
                </label>
                <label class="flex flex-col gap-2 text-left text-xs uppercase tracking-wide text-neutral-500">
                    Kata sandi
                    <input type="password" name="password" placeholder="Masukkan kata sandi"
                        class="rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-900 focus:outline-none"
                        required />
                </label>

                <?php if (!empty($login_message)) : ?>
                <p class="text-sm text-red-600"><?php echo htmlspecialchars($login_message); ?></p>
                <?php endif; ?>

                <button type="submit" name="login"
                    class="w-full rounded-full border border-neutral-900 px-4 py-2 text-xs font-semibold text-neutral-900 transition hover:bg-neutral-900 hover:text-white">
                    Masuk
                </button>
            </form>

        </section>
    </main>

    <footer class="border-t border-neutral-200 py-6 text-center text-xs text-neutral-500">
        RIVVORLD (c) 2025
    </footer>
</body>

</html>





