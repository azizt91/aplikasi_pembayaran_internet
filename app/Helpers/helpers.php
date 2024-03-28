<?php

if (!function_exists('rupiah')) {
    function rupiah($nilai)
    {
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
}
