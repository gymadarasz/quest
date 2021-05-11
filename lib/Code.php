<?php

namespace Madlib;

class Code
{
    public function generate(int $length, string $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[rand(0, strlen($chars)-1)];
        }
        return $code;
    }
}
