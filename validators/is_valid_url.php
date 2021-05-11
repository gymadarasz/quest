<?php

function is_valid_url(string $value): bool {
    return $value && filter_var($value, FILTER_VALIDATE_URL) !== false;
}