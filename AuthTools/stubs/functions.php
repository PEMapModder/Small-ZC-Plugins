<?php

/**
 * Compares two strings using the same time whether they're equal or not.
 *
 * This function should be used to mitigate timing attacks; for instance, when testing crypt() password hashes.
 * @param string $known_string
 * @param string $user_string
 * @return bool
 */
function hash_equals($known_string, $user_string){}
