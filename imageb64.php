<?php
require("model/utils/Encoder.php");

$info = array(
    "id" => 2
);

$jwt = Encoder::createJWT();
$jwt2 = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2Mzg1MzU0NzUsImlzcyI6ImplYW5jYXNzZXRldGUuZnIiLCJleHAiOjE2Mzg1MzcyNzV9.Izlh9WRItA0iQKWz_zY9MFfG0LZHGbh1B4URoEB_EXY";
$time = time();

$jwtToRet = array();

echo $jwt;
echo "</br>";
echo Encoder::verifyJWT($jwt, $jwtToRet) ? "valide" : "non valide";
echo " - exp : " . $jwtToRet["exp"]-$time;
echo "</br>";
echo Encoder::verifyJWT($jwt2, $jwtToRet) ? "valide" : "non valide";
echo " - exp : " . $jwtToRet["exp"]-$time;

echo "</br><hr></br>";

$encryption_key = "ZAF4ZT54Gfzg3Pzvze";
$encryption_iv = "VF3Gvrezv346-zrv";

$startPhrase = "Les carottes sont cuites";
echo "Départ : " . $startPhrase . "</br>";

$ciphering = "AES-128-CTR";
$iv_length = openssl_cipher_iv_length($ciphering);

$endPhrase = openssl_encrypt($startPhrase, $ciphering, $encryption_key, 0, $encryption_iv);
echo "Fin : " . $endPhrase . "</br>";

echo "Décrypt : " . openssl_decrypt ($endPhrase, $ciphering, $encryption_key, 0, $encryption_iv) . "</br>";

echo "</br><hr></br>";

echo $_SERVER['REMOTE_ADDR'];