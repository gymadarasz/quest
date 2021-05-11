<?php

namespace Madlib;

class Exception extends \Exception
{
    public function __construct($message = null, $code = 0, $previous = null)
    {
        if (Config::SITE['env'] === Config::ENV_LIVE) {
            sleep(15);
        }
        parent::__construct($message, $code, $previous);
    }

    public function getAsString($e = null): string
    {
        if (null === $e) {
            $e = $this;
        }
        $class = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();
        $previous = $e->getPrevious();
        $output = "$message\nClass: $class\nCalled at: $file($line)\nTrace:\n$trace" . ($previous ? "\nPrevious: " . $this->getAsString($previous) : '');
        return $output;
    }
}
