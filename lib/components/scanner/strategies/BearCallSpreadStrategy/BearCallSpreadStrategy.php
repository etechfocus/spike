<?php

require_once(__DIR__.'/../../../../engine/common/Constants.php');
require_once(__DIR__.'/../../../../components/scanner/OptionStrategy.php');
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
            $scannerComponent = $this->engine->getComponent('scanner');
            foreach ($chain as $expDate => $strikes) {
                foreach ($strikes as $shortStrike => $shortQuotes) {
                    if (isset($shortQuotes[Constants::CALL]) && 
                          $shortQuotes[Constants::CALL]->getDelta() >= $this->getParams()->getMinDelta() &&
                          $shortQuotes[Constants::CALL]->getDelta() <= $this->getParams()->getMaxDelta() &&
                          $shortQuotes[Constants::CALL]->getAsk() > 0) {

                        $shortLeg = $scannerComponent->createShortLeg(1, $shortQuotes[Constants::CALL]);

                        foreach ($strikes as $longStrike => $longQuotes) {
                            $longLeg = $scannerComponent->createLongLeg(1, $longQuotes[Constants::CALL]);
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
