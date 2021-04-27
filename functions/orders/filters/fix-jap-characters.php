<?php

/**
 * Filters and returns any potentially problematic Japanese characters used in shipping addresses
 *
 * @param object $value
 * @return void
 */
function sbwc_opf_fix_jap_chars($value)
{
    $search_chars = array("ー", "－", "・");
    $fix_chars = array("-", "-", "·");
    $address = str_replace($search_chars, $fix_chars, $value);
    return $address;
}
