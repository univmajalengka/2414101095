<?php
declare(strict_types=1);

function format_rupiah(float|int $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

function service_prices(): array
{
    return [
        'penginapan' => 1_000_000,
        'transportasi' => 1_200_000,
        'makan' => 500_000,
    ];
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function redirect(string $path): void
{
    header("Location: {$path}");
    exit;
}
