<?php

require_once('OptionStrategy.php');
require_once('BearCallSpreadOrder.php');

class BearCallSpreadStrategy extends OptionStrategy {

    public function getName() {
        return "BearCallSpreadStrategy";
    }

    public function findOrders() {
        $quoter = $this->engine->getComponent('quoter');
        $startDate = time();
        $endDate = time()+$this->configs['days']*24*60*60;
        $ret = array();
        foreach ($this->configs['symbols'] as $symbol) {
            $chain = $quoter->getOptionChain($symbol, $startDate, $endDate,
                   'CALL', 1000 /* strikes */);
            $optionStrategyComponent = $this->engine->getComponent('strategy');
            foreach ($chain as $expDate => $strikes) {
                foreach ($strikes as $shortStrike => $shortQuotes) {
                    if (isset($shortQuotes['CALL']) && 
                          $shortQuotes['CALL']->getDelta() >= $this->configs['minDelta'] &&
                          $shortQuotes['CALL']->getDelta() <= $this->configs['maxDelta'] &&
                          $shortQuotes['CALL']->getAsk() > 0) {

                        $shortLeg = $optionStrategyComponent->createSellLeg(1, $shortQuotes['CALL']);

                        foreach ($strikes as $longStrike => $longQuotes) {
                            $longLeg = $optionStrategyComponent->createBuyLeg(1, $longQuotes['CALL']);
                            $order = new BearCallSpreadOrder($shortLeg->getSymbol());
                            $order->addLeg($shortLeg);
                            $order->addLeg($longLeg);

                            if ($shortStrike > $longStrike) {
                                continue;
                            }
                            $price = $order->getPrice();
                            if ($price >= 0) {
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
