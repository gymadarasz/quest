<?php

namespace Madlib;

class Input
{
    protected Request $request;
    protected Mysql $mysql;

    public function __construct(Request $request, Mysql $mysql)
    {
        $this->request = $request;
        $this->mysql = $mysql;
    }

    public function getString(string $key): string
    {
        return $this->mysql->esc($this->request->get($key));
    }

    public function getInt(string $key): int
    {
        return (int)$this->request->get($key);
    }

    public function getNumber(string $key, int $decimals, string $decimal_separator, string $thousand_separator): string
    {
        return $this->mysql->esc(number_format((float)$this->request->get($key), $decimals, $decimal_separator, $thousand_separator));
    }

    public function getStringArrayAssoc(string $key): array
    {
        $strings = [];
        foreach ($this->request->getArray($key) as $index => $string) {
            $strings[$this->mysql->esc($index)] = $this->mysql->esc($string);
        }
        return $strings;
    }

    public function getIntArrayAssoc(string $key): array {
        $ints = [];
        foreach ($this->request->getArray($key) as $index => $int) {
            $ints[$this->mysql->esc($index)] = (int)$int;
        }
        return $ints;
    }

    public function getNumberArrayAssoc(string $key, int $decimals, string $decimal_separator, string $thousand_separator): array
    {
        $number = [];
        foreach ($this->request->getArray($key) as $index => $string) {
            $number[$this->mysql->esc($index)] = $this->mysql->esc(number_format((float)$string, $decimals, $decimal_separator, $thousand_separator));
        }
        return $number;
    }
}
