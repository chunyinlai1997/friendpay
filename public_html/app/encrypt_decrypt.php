<?php
function encrypt($plaintext) {
    $secret_key = "41f1f19176d383480afa65d325c06ed0";
    $method = "AES-256-CBC";
    $key = hash('sha256', $secret_key, true);
    $iv = openssl_random_pseudo_bytes(16);

    $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $ciphertext, $key, true);

    return bin2hex($iv . $hash . $ciphertext);
}

function decrypt($get) {
    $ivHashCiphertext = hex2bin($get);
    $secret_key = "41f1f19176d383480afa65d325c06ed0";
    $method = "AES-256-CBC";
    $iv = substr($ivHashCiphertext, 0, 16);
    $hash = substr($ivHashCiphertext, 16, 32);
    $ciphertext = substr($ivHashCiphertext, 48);
    $key = hash('sha256', $secret_key, true);

    if (hash_hmac('sha256', $ciphertext, $key, true) !== $hash) return null;

    return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
}
?>
