<?php

$file = file_get_contents('/mnt/c/Users/h-saito/Downloads/senbeijiru.flac');

$url = 'https://gateway-tok.watsonplatform.net/speech-to-text/api/v1/recognize';
$model = 'ja-JP_NarrowbandModel';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '?model=' . $model);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_USERPWD, 'apikey' . ':' . 'jz8S58TekitouAPI-ka');

$headers = array();
$headers[] = 'Content-Type: audio/flac';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
print_r($result);
?>
