<?php

function is_valid_email(string $value): bool {
    return $value && filter_var($value, FILTER_VALIDATE_EMAIL);
}