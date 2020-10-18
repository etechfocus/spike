<?php

class StockQuote {

    public function __construct($attrs) {
        $this->attrs = $attrs;
    }

    public function get($name) {
        return $this->attrs[$name];
    }

    public function getSymbol() {
        return $this->get('symbol');
    }

    public function getDate() {
        return $this->get('date');
    }

    public function getDateTime() {
        return $this->get('datetime');
    }

    public function getOpen() {
        return $this->get('open');
    }

    public function getClose() {
        return $this->get('close');
    }

    public function getBid() {
        return $this->get('bid');
    }

    public function getAsk() {
        return $this->get('ask');
    }
}
