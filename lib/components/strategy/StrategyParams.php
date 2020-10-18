<?php

class StrategyParams {

    public function __construct($params) {
        $this->params = $params;
    }

    public function get($name) {
        return $this->params[$name];
    }

}
