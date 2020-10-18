<?php

require_once(__DIR__.'/../../engine/common/Constants.php');
require_once(__DIR__.'/../../components/strategy/OptionStrategy.php');
require_once('BearCallSpreadOrder.php');
require_once('BearCallSpreadParams.php');

class BearCallSpreadStrategy extends OptionStrategy {

    public function getName() {
        return "BearCallSpreadStrategy";
    }

    public function init($engine, $id, $configs) {
        parent::init($engine, $id, $configs);
    }

    public function createParams($configs) {
        return new BearCallSpreadParams($configs);
    }

    public function findOrders() {
        $quoter = $this->engine->getComponent('quoter');
        $startDate = time();
        $endDate = time()+$this->getParams()->getDays()*24*60*60;
        $ret = array();
        foreach ($this->getParams()->getSymbols() as $symbol) {
            $chain = $quoter->getOptionChain($symbol, $startDate, $endDate,
                   Constants::CALL, 1000 /* strikes */);
            $optionStrategyComponent = $this->engine->getComponent('strategy');
            foreach ($chain as $expDate => $strikes) {
                foreach ($strikes as $shortStrike => $shortQuotes) {
                    if (isset($shortQuotes['CALL']) && 
                          $shortQuotes['CALL']->getDelta() >= $this->getParams()->getMinDelta() &&
                          $shortQuotes['CALL']->getDelta() <= $this->getParams()->getMaxDelta() &&
                          $shortQuotes['CALL']->getAsk() > 0) {

                        $shortLeg = $optionStrategyComponent->createShortLeg(1, $shortQuotes['CALL']);

                        foreach ($strikes as $longStrike => $longQuotes) {
                            $longLeg = $optionStrategyComponent->createLongLeg(1, $longQuotes['CALL']);
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
