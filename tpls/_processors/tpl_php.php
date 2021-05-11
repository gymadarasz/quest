<?php

use Madlib\Template;

/**
 * process phps
 * examples:
 *      <php ... />
 *      <php> ... </php>
 *  results: =>
 *      <?php ... ?>
 */
function tpl_php(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/<php\s+(.*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php $arg ?>", $tpl);
    }

    $tpl = str_replace('<php>', "<?php ", $tpl);
    $tpl = str_replace('</php>', " ?>", $tpl);
    return $tpl;
}
