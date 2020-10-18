<?php

require_once(__DIR__.'/../../engine/common/Constants.php');
require_once(__DIR__.'/../../components/strategy/OptionStrategy.php');
require_once('BullPutSpreadOrder.php');
require_once('BullPutSpreadParams.php');

class BullPutSpreadStrategy extends OptionStrategy {

    public function getName() {
        return "BullPutSpreadStrategy";
    }

    public function init($engine, $id, $configs) {
        parent::init($engine, $id, $configs);
    }

    public function createParams($configs) {
        return new BullPutSpreadParams($configs);
    }

    public function findOrders() {
        $quoter = $this->engine->getComponent('quoter');
        $startDate = time();
        $endDate = time()+$this->getParams()->getDays()*24*60*60;
        $ret = array();
        foreach ($this->getParams()->getSymbols() as $symbol) {
            $chain = $quoter->getOptionChain($symbol, $startDate, $endDate,
                   Constants::PUT, 1000 /* strikes */);
            $optionStrategyComponent = $this->engine->getComponent('strategy');
            foreach ($chain as $expDate => $strikes) {
                foreach ($strikes as $shortStrike => $shortQuotes) {
                    if (isset($shortQuotes['PUT']) && 
                        $shortQuotes['PUT']->getDelta() > (-1 * $this->getParams()->getMaxDelta()) &&
                        $shortQuotes['PUT']->getDelta() < (-1 * $this->getParams()->getMinDelta()) &&
                        $shortQuotes['PUT']->getAsk() > 0) {

                        $shortLeg = $optionStrategyComponent->createShortLeg(1, $shortQuotes['PUT']);

                        foreach ($strikes as $longStrike => $longQuotes) {
                            $longLeg = $optionStrategyComponent->createLongLeg(1, $longQuotes['PUT']);

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
                            if ($order->getROI() < $this->getParams()->getMinROI() ||
                                $order->getROI() > $this->getParams()->getMaxROI()) {
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
