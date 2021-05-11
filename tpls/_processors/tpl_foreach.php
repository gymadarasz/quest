<?php

use Madlib\Template;

/**
 * process foreaches
 * examples:
 *      <foreach $array as $item:>
 *      ...
 *      </foreach>
 * results: =>
 *      <?php foreach ($array as $item): ?>
 *      ...
 *      <?php endforeach; ?>
 */
function tpl_foreach(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/<foreach\s+(.*):>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php foreach ($arg): ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<\/foreach>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php endforeach; ?>", $tpl);
    }

    return $tpl;
}
