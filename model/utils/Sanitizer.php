<?php

class Sanitizer
{
    public static function Sanitize(mixed $var):mixed {
        return filter_var($var, FILTER_SANITIZE_ENCODED);
    }
}