<?php

// auxiliary funcs
// source: http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions

function starts_with($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function ends_with($haystack, $needle) {
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function human_filesize($size) {
      $sizes = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
      if ($size == 0) { return('n/a'); } else {
      return (round($size/pow(1000, ($i = floor(log($size, 1000)))), $i > 1 ? 2 : 0) . $sizes[$i]); }
}
