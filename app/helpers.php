<?php

if (!function_exists('formatRupiah')) {
    /**
     * Format angka menjadi format Rupiah Indonesia
     * 
     * @param mixed $amount
     * @param bool $withSymbol
     * @return string
     */
    function formatRupiah($amount, $withSymbol = true)
    {
        $formatted = number_format((float)$amount, 0, ',', '.');
        return $withSymbol ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format angka dengan pemisah ribuan
     * 
     * @param mixed $number
     * @param int $decimals
     * @return string
     */
    function formatNumber($number, $decimals = 0)
    {
        return number_format((float)$number, $decimals, ',', '.');
    }
}

if (!function_exists('formatPercentage')) {
    /**
     * Format angka menjadi persentase
     * 
     * @param mixed $number
     * @param int $decimals
     * @return string
     */
    function formatPercentage($number, $decimals = 2)
    {
        return number_format((float)$number, $decimals, ',', '.') . '%';
    }
}