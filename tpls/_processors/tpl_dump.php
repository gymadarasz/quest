<?php

use Madlib\Template;

/**
 * process dumps
 * examples:
 *      <dump ... />
 *  results: =>
 *      <pre><?php var_dump(...) ?></pre>
 */
function tpl_dump(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/<dump\s+(.*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<pre><?php var_dump($arg) ?></pre>", $tpl);
    }

    return $tpl;
}