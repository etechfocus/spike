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

    public function getType() {
        return $this->get('type');
    }

    public function getQuote() {
        return $this->get('quote');
    }
}
