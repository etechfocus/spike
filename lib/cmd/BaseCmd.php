<?php

class BaseCmd {

    public function init() {
    }

    public function process() {
    }

    public function loadConfig($path) {
        if (!file_exists($path)) {
            throw new Exception('config not present - '.$path);
        }
        $content = file_get_contents($path);
        $configs = json_decode($content, true);
        return $configs;
    }

    public function execute() {
        $this->startTime = microtime(true);
        $this->init();
        $this->process();
        $this->endTime = microtime(true);
    }

}
