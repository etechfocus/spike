<?php

class TradeComponent {

    protected $engine;
    protected $configs;

    public function init($engine, $configs) {
        $this->engine = $engine;
        $this->configs = $configs;
    }

    public function getConfig($name) {
        return $this->configs[$name];
    }
}
