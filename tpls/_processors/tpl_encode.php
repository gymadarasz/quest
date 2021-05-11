<?php

use Madlib\Template;

/**
 * process echos with htmlentities encode
 * examples:
 *      {$var}
 *      <$var />
 *      <encode $var />
 * result =>
 *      <?php echo htmlentities($var) ?>
 * OR:
 *      {{ ... }}
 * result:
 *      <?php echo htmlentities(...) ?>
 * 
 * can supress error for non-required fields
 * examples:
 *      {@$var}
 *      <@$var />
 * result =>
 *      <?php echo htmlentities($var ?? '') ?>
 */
function tpl_encode(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/{\$\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $var = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo htmlentities(\$$var); ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/{{\s*(.*)\s*}}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo htmlentities($arg); ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<\$\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $var = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo htmlentities(\$$var); ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<encode\s+([^>]*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo htmlentities($arg); ?>", $tpl);
    }

    // supressors
    $matches = null;
    if (false === preg_match_all('/{@\$\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $var = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo htmlentities(\$$var ?? ''); ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<@\$\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $var = $matches[1][$key];
        $tpl = str_replace($match, "<?php echo htmlentities(\$$var ?? ''); ?>", $tpl);
    }

    return $tpl;
}
