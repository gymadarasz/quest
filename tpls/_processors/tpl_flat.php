<?php

use Madlib\Template;

/**
 * process flats - flats an associative array as template variable
 * so that the array items available directly as variables in template
 * examples:
 *      <flat $array />
 * result =>
 *      <?php foreach($array as $__key => $__value) $$__key = $_value; ?>
 */
function tpl_flat(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/<flat\s+([^>]*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php foreach($arg as \$__key => \$__value) \$\$__key = \$__value; ?>", $tpl);
    }

    return $tpl;
}
