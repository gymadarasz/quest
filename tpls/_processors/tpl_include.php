<?php

use Madlib\Template;

/**
 * process includes
 * examples:
 *      <include 'your-file.html.php':>
 *  results: =>
 *      <?php include 'your-file.html.php'; ?>
 */
function tpl_include(Template $that, string $tpl): string
{
    $matches = null;
    if (false === preg_match_all('/<include\s+(.*)\s*\/>/', $tpl, $matches)) {
        throw new Exception("Template error");
    }
    foreach ($matches[0] as $key => $match) {
        $arg = $matches[1][$key];
        //$tpl = str_replace($match, "<?php \$this->show($arg, \$__data); ? >", $tpl);
        $tpl = str_replace($match, "<?php \$__cacheFile = \$this->getCachefile($arg); if (!file_exists(\$__cacheFile )) \$this->process($arg, get_defined_vars()); include \$this->getCachefile($arg); ?>", $tpl);
    }

    return $tpl;
}
