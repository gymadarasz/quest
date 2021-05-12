<?php

use Madlib\Factory;
use Madlib\Application;

include_once __DIR__ . '/vendor/autoload.php';


error_reporting(E_ALL);
ini_set('display_errors', '1');

(new Factory)->getInstance(Application::class)->process();