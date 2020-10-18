<?php

class OptionQuote {

    public function __construct($attrs) {
        $this->attrs = $attrs;
    }

    public function get($name) {
        return $this->attrs[$name];
    }

    public function getShortExpDate() {
        $ret = $this->getExpDate();
        $ret = str_replace("-", "", $ret);
        return substr($ret, 2);
    }

    public function getExpDate() {
        return $this->get('expDate');
    }

    public function getSymbol() {
        return $this->get('symbol');
    }

    public function getStrike() {
        return $this->get('strike');
    }

    public function getType() {
        return $this->get('type');
    }

    public function getBid() {
        return $this->get('bid');
    }

    public function getAsk() {
        return $this->get('ask');
    }

    public function getBidSize() {
        return $this->get('bidSize');
    }

    public function getAskSize() {
        return $this->get('askSize');
    }

    public function getDelta() {
        return $this->get('delta');
    }
}
