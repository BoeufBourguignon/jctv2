<?php

class Encoder
{
    private static function base64UrlEncode(string $toEncode):string
    {
        return str_replace(
            ["+","/","="],
            ["-","_",""],
            base64_encode($toEncode)
        );
    }

    public static function createJWT():string
    {
        $currentTimestamp = time();

        $tokenHeader = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);
        $tokenPayload = json_encode([
            "iat" => $currentTimestamp,
            "iss" => "jeancassetete.fr",
            "exp" => $currentTimestamp + 1800
        ]);

        $base64UrlTokenHeader = self::base64UrlEncode($tokenHeader);
        $base64UrlTokenPayload = self::base64UrlEncode($tokenPayload);

        $tokenSignature = hash_hmac(
            "sha256",
            $base64UrlTokenHeader.
            ".".
            $base64UrlTokenPayload,
            "&jct&".$currentTimestamp."&pSDvMA_u`6]9M#/p",
            true
        );
        $base64UrlTokenSignature = self::base64UrlEncode($tokenSignature);

        return
            $base64UrlTokenHeader.
            ".".
            $base64UrlTokenPayload.
            ".".
            $base64UrlTokenSignature
            ;
    }

    public static function verifyJWT(string $jwt, ?array &$ret = null):bool
    {
        $tokenParts = explode(".", $jwt);
        if (count($tokenParts) != 3)
        {
            return false;
        }

        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $tokenProvidedSignature = $tokenParts[2];

        $tokenPayloadJson = json_decode($tokenPayload);

        $exp = time() >= $tokenPayloadJson->exp;
        $iss = $tokenPayloadJson->iss === "jeancassetete.fr";
        $key = "&jct&".$tokenPayloadJson->iat."&pSDvMA_u`6]9M#/p";

        $tokenExpectedSignature = hash_hmac(
            "sha256",
            self::base64UrlEncode($tokenHeader).
            ".".
            self::base64UrlEncode($tokenPayload),
            $key,
            true
        );

        if ($ret !== null)
        {
            $ret["exp"] = $tokenPayloadJson->exp;
            $ret["iss"] = $tokenPayloadJson->iss;
        }

        return self::base64UrlEncode($tokenExpectedSignature) === $tokenProvidedSignature && !$exp && $iss;
    }
}