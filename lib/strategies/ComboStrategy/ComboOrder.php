<?php

require_once(__DIR__.'/../../components/strategy/OptionOrder.php');

class ComboOrder extends OptionOrder {

    public function getName() {
        return "ComboOrder";
    }

    public function isValid() {
        return true;
    }

}
