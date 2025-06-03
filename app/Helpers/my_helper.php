<?php

function isChecked($status)
{
    return ($status) ? "checked" : "";
}

function isLabelChecked($label)
{
    return ($label) ? "Aktif" : "Nonaktif";
}

function getAmount($money)
{
    // Hilangkan semua karakter kecuali angka, titik dan koma
    $money = preg_replace('/[^\d.,]/', '', $money);

    // Jika format Indonesia (misalnya "10.000,50") → ubah menjadi "10000.50"
    if (strpos($money, ',') !== false && strpos($money, '.') !== false) {
        $money = str_replace('.', '', $money); // hilangkan pemisah ribuan
        $money = str_replace(',', '.', $money); // ubah koma jadi titik desimal
    }
    // Jika hanya koma → asumsikan sebagai desimal
    else if (strpos($money, ',') !== false) {
        $money = str_replace(',', '.', $money);
    }
    // Jika hanya titik → asumsikan sebagai pemisah ribuan (hilangkan saja)
    else {
        $money = str_replace('.', '', $money);
    }

    return (float) $money;
}

function encrypt_url($string)
{

    $output = false;
    /*
    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
    */
    $security       = parse_ini_file("security.ini");
    $secret_key     = $security["encryption_key"];
    $secret_iv      = $security["iv"];
    $encrypt_method = $security["encryption_mechanism"];

    // hash
    $key    = hash("sha256", $secret_key);

    // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
    $iv     = substr(hash("sha256", $secret_iv), 0, 16);

    //do the encryption given text/string/number
    $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($result);
    return $output;
}

function decrypt_url($string)
{

    $output = false;
    /*
    * read security.ini file & get encryption_key | iv | encryption_mechanism value for generating encryption code
    */

    $security       = parse_ini_file("security.ini");
    $secret_key     = $security["encryption_key"];
    $secret_iv      = $security["iv"];
    $encrypt_method = $security["encryption_mechanism"];

    // hash
    $key    = hash("sha256", $secret_key);

    // iv – encrypt method AES-256-CBC expects 16 bytes – else you will get a warning
    $iv = substr(hash("sha256", $secret_iv), 0, 16);

    //do the decryption given text/string/number

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    return $output;
}

function formatRupiah($angka, $prefix = null)
{
    // Menghapus karakter selain angka dan koma
    $numberString = preg_replace('/[^,\d]/', '', $angka);

    // Memisahkan angka dengan pecahan desimal (jika ada)
    $split = explode(',', $numberString);
    $sisa = strlen($split[0]) % 3;

    // Bagian awal angka (sebelum ribuan)
    $rupiah = substr($split[0], 0, $sisa);

    // Kelompokkan angka dalam ribuan
    $ribuan = substr($split[0], $sisa);
    $ribuan = str_split($ribuan, 3);

    if (!empty($ribuan)) {
        $separator = $sisa ? '.' : '';
        $rupiah .= $separator . implode('.', $ribuan);
    }

    // Tambahkan pecahan desimal (jika ada)
    $rupiah = isset($split[1]) ? $rupiah . ',' . $split[1] : $rupiah;

    // Tambahkan prefix jika ada
    return $prefix === null ? $rupiah : ($rupiah ? $prefix . $rupiah : '');
}