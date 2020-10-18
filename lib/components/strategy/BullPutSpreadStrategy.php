<?php

require_once('OptionStrategy.php');
require_once('BullPutSpreadOrder.php');

class BullPutSpreadStrategy extends OptionStrategy {

    public function getName() {
        return "BullPutSpreadStrategy";
    }

    public function findOrders() {
        $quoter = $this->engine->getComponent('quoter');
        $startDate = time();
        $endDate = time()+$this->configs['days']*24*60*60;
        $ret = array();
        foreach ($this->configs['symbols'] as $symbol) {
            $chain = $quoter->getOptionChain($symbol, $startDate, $endDate,
                   'PUT', 1000 /* strikes */);
            $optionStrategyComponent = $this->engine->getComponent('strategy');
            foreach ($chain as $expDate => $strikes) {
                foreach ($strikes as $shortStrike => $shortQuotes) {
                    if (isset($shortQuotes['PUT']) && 
                        $shortQuotes['PUT']->getDelta() > (-1 * $this->configs['maxDelta']) &&
                        $shortQuotes['PUT']->getDelta() < (-1 * $this->configs['minDelta']) &&
                        $shortQuotes['PUT']->getAsk() > 0) {

                        $shortLeg = $optionStrategyComponent->createSellLeg(1, $shortQuotes['PUT']);

                        foreach ($strikes as $longStrike => $longQuotes) {
                            $longLeg = $optionStrategyComponent->createBuyLeg(1, $longQuotes['PUT']);

                            $order = new BullPutSpreadOrder($shortLeg->getSymbol());
                            $order->addLeg($shortLeg);
                            $order->addLeg($longLeg);

                            if ($shortStrike < $longStrike) {
                                continue;
                            }
                            $credit = $order->getPrice();
                            if ($credit >= 0) {
                                continue;
                            }
                            $risk = $order->getRisk();
                            if ($risk <= 0) {
                                continue;
                            }
                            if ($order->getROI() < $this->configs['minROI'] ||
                                $order->getROI() > $this->configs['maxROI']) {
                                continue;
                            }
                            $ret[] = $order;
                        }
                    }
                }
            }
        }
        return $ret;
    }

}
