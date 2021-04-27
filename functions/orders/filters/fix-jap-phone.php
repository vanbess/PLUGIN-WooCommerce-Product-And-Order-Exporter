<?php

/**
 * Inserts proper internation prefix to Japanese phone numbers and returns the fixed number
 *
 * @param string $value
 * @return void
 */
function sbwc_opf_fix_jap_phone($value)
{
    $firstTwo = substr($value, 0, 2);
    if ($firstTwo == "81") {
        $value = "+" . $value;
    }
    return $value;
}
