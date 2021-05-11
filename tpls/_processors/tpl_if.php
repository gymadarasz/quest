<?php

use Madlib\Template;

/**
 * process ifs
 * examples:
 *      <if $foo == 'bar':>
 *      ...
 *      <elseif $bar == 'bazz':>
 *      ...
 *      <else>
 *      ...
 *      </if>
 * results: =>
 *      <?php if ($foo == 'bar'): ?>
 *      ...
 *      <?php elseif ($bar == 'bazz'): ?>
 *      ...
 *      <?php else: ?>
 *      ...
 *      <?php endif; ?>
 */
function tpl_if(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/<if\s+(.*):>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php if ($arg): ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/\{if\s+([^}]*)\}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        $tpl = str_replace($match, "<?php if ($arg): ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<else\s*if\s+(.*):>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php elseif ($arg): ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/\{else\s*if\s+([^}]*)\}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php elseif ($arg): ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<else>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php else: ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/\{else\}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php else: ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/<\/if>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php endif; ?>", $tpl);
    }

    $matches = null;
    if (false === preg_match_all('/\{\/if\}/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $tpl = str_replace($match, "<?php endif; ?>", $tpl);
    }

    return $tpl;
}
