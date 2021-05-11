<?php

use Madlib\Factory;
use Madlib\Application;

include_once __DIR__ . '/vendor/autoload.php';

(new Factory)->getInstance(Application::class)->process();