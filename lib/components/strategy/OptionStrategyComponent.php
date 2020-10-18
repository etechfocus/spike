<?php

require_once(__DIR__.'/../../engine/common/Constants.php');
require_once(__DIR__.'/../../engine/common/EngineComponent.php');

class OptionStrategyComponent extends EngineComponent {

    public function createSellLeg($qty, $quote) {
        $attrs = array();
        $attrs['qty'] = $qty;
        $attrs['type'] = Constants::SHORT;
        $attrs['quote'] = $quote;
        $leg = new OptionLeg($attrs);
        return $leg;
    }

    public function createBuyLeg($qty, $quote) {
        $attrs = array();
        $attrs['qty'] = $qty;
        $attrs['type'] = Constants::LONG;
        $attrs['quote'] = $quote;
        $leg = new OptionLeg($attrs);
        return $leg;
    }
}
