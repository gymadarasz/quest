<?php

namespace Madlib;

class Template
{
    protected Config $config;
    protected Session $session;
    protected Code $code;

    public function __construct(Config $config, Session $session, Code $code)
    {
        $this->config = $config;
        $this->session = $session;
        $this->code = $code;
        if ($this->config::TEMPLATE['devmode']) {
            $this->clearCache();
        }
    }

    protected function clearCache(): void
    {
        $files = glob($this->config::TEMPLATE['cache'] . '*.html.php.*.php');
        foreach ($files as $cachefile) {
            if (is_dir($cachefile)) {
                continue;
            }
            unlink($cachefile);
        }
    }

    public function process(string $__filename, array $__data): string
    {
        if (!file_exists($__filename)) {
            throw new Exception("Template file is not found: [$__filename]");
        }
        $__path = $this->config::TEMPLATE['path'];
        $__base = $this->config::SITE['base'];
        $__csrf = $this->session->get('csrf');
        $__cachefile = $this->getCachefile($__filename);
        if (!file_exists($__cachefile) || filemtime($__filename) > filemtime($__cachefile)) {
            $this->createCache($__filename, $__cachefile);
        }
        foreach ($__data as $__key => $__value) {
            $$__key = $__value;
        }
        ob_start();
        include $__cachefile;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function show(string $filename, array $data): void
    {
        echo $this->process($filename, $data);
    }

    protected function getCachefile(string $filename): string
    {
        $realpath = realpath($filename);
        return $this->config::TEMPLATE['cache'] . basename($realpath) . '.' . md5($realpath) . '.php';
    }

    public function createCache(string $filename, string $cachefile): void
    {
        $tpl = file_get_contents($filename);
        foreach ($this->config::TEMPLATE['processors'] as $path => $processors) {
            foreach ($processors as $processor) {
                include_once $path . $processor . '.php';
                $tpl = $processor($this, $tpl);
            }
        }
        
        if (false === file_put_contents($cachefile, $tpl)) {
            throw new Exception('Unable to write cache');
        }
    }
}
