<?php

/**
 * Fungsi terbilang untuk mengkonversi angka ke kata-kata dalam bahasa Indonesia
 *
 * @param int|float $angka Angka yang akan dikonversi
 * @return string Hasil konversi dalam bentuk kata-kata
 */
if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            return $bilangan[$angka];
        } elseif ($angka < 20) {
            return $bilangan[$angka - 10] . ' Belas';
        } elseif ($angka < 100) {
            return $bilangan[floor($angka / 10)] . ' Puluh ' . $bilangan[$angka % 10];
        } elseif ($angka < 200) {
            return 'Seratus ' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $bilangan[floor($angka / 100)] . ' Ratus ' . terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'Seribu ' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return terbilang(floor($angka / 1000)) . ' Ribu ' . terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return terbilang(floor($angka / 1000000)) . ' Juta ' . terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return terbilang(floor($angka / 1000000000)) . ' Milyar ' . terbilang($angka % 1000000000);
        } elseif ($angka < 1000000000000000) {
            return terbilang(floor($angka / 1000000000000)) . ' Trilyun ' . terbilang($angka % 1000000000000);
        }

        return '';
    }
}
