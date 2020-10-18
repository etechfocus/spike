<?php

class OptionLeg {

    public function __construct($attrs) {
        $this->attrs = $attrs;
    }

    public function get($name) {
        return $this->attrs[$name];
    }

    public function getQty() {
        return $this->get('qty');
    }

    public function isLong() {
        return $this->getType()=='BUY';
    }

    public function isShort() {
        return $this->getType()=='SELL';
    }

    public function getType() {
        return $this->get('type');
    }

    public function getQuote() {
        return $this->get('quote');
    }

    public function getQuoteType() {
        return $this->getQuote()->getType();
    }

    public function getSymbol() {
        return $this->getQuote()->getSymbol();
    }

    public function getStrike() {
        return $this->getQuote()->getStrike();
    }

    public function getBid() {
        return $this->getQuote()->getBid();
    }

    public function getAsk() {
        return $this->getQuote()->getAsk();
    }

    public function getDelta() {
        return $this->getQuote()->getDelta();
    }
}
