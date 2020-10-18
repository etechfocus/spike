<?php

class TradeEngine {

    const DEBUG = 'debug';

    protected $components = array();

    public function init($configs) {
        $this->configs = $configs;
        $this->initComponents();
    }

    private function initComponents() {
        foreach ($this->configs['components'] as $componentId => $componentConfig) {
            require_once(__DIR__.'/../../'.$componentConfig['path'].'.php');
            $this->components[$componentId] = new $componentConfig['class']();
            $configs = array();
            if (isset($this->configs['components'][$componentId])) {
                $configs = $this->configs['components'][$componentId];
            }
            $this->components[$componentId]->init($this, $configs);
        }
    }

    public function getConfig() {
        return $this->configs;
    }

    public function isDebug() {
        return isset($this->configs[self::DEBUG]) && $this->configs[self::DEBUG];
    }

    public function debug($msg) {
        print($msg);
        print("\n");
    }

    public function getComponent($name) {
        return $this->components[$name];
    }
}
