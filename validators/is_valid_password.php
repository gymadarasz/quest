<?php

function is_valid_password(string $value): bool {
    return 
        preg_match('/.{6,}/', $value) && 
        preg_match('/[a-z]/', $value) &&
        preg_match('/[A-Z]/', $value) &&
        preg_match('/[0-9]/', $value);
}