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
    $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
    $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

    $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

    $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
    $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

    return (float) str_replace(',', '.', $removedThousandSeparator);
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