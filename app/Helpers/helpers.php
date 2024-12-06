<?php

use Carbon\Carbon;

if (! function_exists('formatDate')) {
    /**
     * Format tanggal menjadi format: Senin, 12 Desember 2024
     * Jika tanggal tidak valid atau null, return '-'
     *
     * @param  mixed  $date
     * @return string
     */
    function formatDate($date)
    {
        try {
            // Jika tanggal null atau tidak valid, kembalikan '-'
            if (is_null($date) || empty($date)) {
                return '-';
            }

            // Menggunakan Carbon untuk parse dan format tanggal
            return Carbon::parse($date)->translatedFormat('l, d F Y');
        } catch (\Exception $e) {
            // Jika ada error pada parsing, kembalikan '-'
            return '-';
        }
    }
}


if (!function_exists('formatTime')) {
    /**
     * Format waktu menjadi format HH:mm dalam format 24 jam.
     * Jika waktu tidak valid atau null, akan mengembalikan '-'.
     *
     * @param  mixed  $time
     * @return string
     */
    function formatTime($time)
    {
        // Cek apakah waktu valid
        if ($time) {
            try {
                // Menggunakan Carbon untuk memparsing dan format waktu
                return Carbon::parse($time)->format('H:i');
            } catch (\Exception $e) {
                // Jika terjadi kesalahan (misalnya waktu tidak valid), return fallback '-'
                return '';
            }
        }

        // Return fallback '-' jika waktu null atau kosong
        return '';
    }
}
