<?php
header("Content-type: image/jpeg");
//URL for IMDb Image.
$url = rawurldecode($_REQUEST['url']);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$data = curl_exec($ch);
curl_close($ch);
echo $data;
?>
