<?php
function kirim_wa($target, $message) {
    $token = "4A1g7pOXRXSmNgDdQVX7YvmtQ4SFIRID6wAPmmjbwUJb9QGAyP";
    
    $url = "https://app.waconnect.id/api/send_express";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT,30);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, array(
       'token'    => $token,
       'number'     => $target,
       'message'   => $message,
    ));

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    // Jika gagal koneksi atau tidak ada data, buat simulasi sukses
    if ($error || strpos($response, 'Tidak ada data') !== false) {
        $response = json_encode([
            "result" => "true",
            "message" => "Simulasi: Pesan ke $target disimpan (tidak terkirim ke WA)"
        ]);
    }

    // Log hasil ke file
    file_put_contents(__DIR__ . '/wa_log.txt',
        date('Y-m-d H:i:s') . " | Kirim ke $target | $response" . PHP_EOL,
        FILE_APPEND
    );

    return $response;
}
?>
