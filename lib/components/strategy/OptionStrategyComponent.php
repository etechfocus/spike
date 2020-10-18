<?php

require_once(__DIR__.'/../../engine/common/TradeComponent.php');

class OptionStrategyComponent extends TradeComponent {

    public function createSellLeg($qty, $quote) {
        $attrs = array();
        $attrs['qty'] = $qty;
        $attrs['type'] = 'SELL';
        $attrs['quote'] = $quote;
        $leg = new OptionLeg($attrs);
        return $leg;
    }

    public function createBuyLeg($qty, $quote) {
        $attrs = array();
        $attrs['qty'] = $qty;
        $attrs['type'] = 'BUY';
        $attrs['quote'] = $quote;
        $leg = new OptionLeg($attrs);
        return $leg;
    }
}
