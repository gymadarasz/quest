<?php

namespace Madlib;

class Validator
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function validate(string $rule, $value): bool
    {
        $found = false;
        foreach ($this->config::VALIDATORS as $path => $validators) {
            if (isset($validators[$rule])) {
                $found = true;
                require_once $path . $validators[$rule] . '.php';
                if (!$validators[$rule]($value)) {
                    return false;
                }
            }
        }
        if (!$found) {
            throw new Exception("Validator is not found: [$rule]");
        }
        return true;
    }
}
