<?php

use Madlib\Template;

/**
 * process echos without encode
 * examples:
 *      {!$var} => <?php echo $var; ?>
 *      <!$var /> => <?php echo $var; ?>
 *      <echo $var /> => <?php echo $var; ?>
 * result: =>
 *      <?php echo $var; ?>
 * OR:
 *      {{! ... }}
 * result:
 *      <?php echo ... ?>
 */
function tpl_echo(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/{\!\$\s*!\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $var = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo \$$var; ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/{{\!\s*!\s*(.*)\s*}}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo $arg; ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<!\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $var = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo \$$var; ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<echo\s+([^>]*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo $arg; ?>", $tpl);
    }

    return $tpl;
}
