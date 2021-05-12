<?php

namespace Madlib;

use GuzzleHttp\Client;

class Curl
{
    public function getClient(...$params): Client
    {
        return new Client(...$params);
    }
}
