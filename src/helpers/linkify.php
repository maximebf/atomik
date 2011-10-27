<?php

function linkify($string)
{
    $string = str_replace('-', ' ', $string);
    $string = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $string);
    $string = trim(strtolower($string));
    return $string;
}

