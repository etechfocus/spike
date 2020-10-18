<?php

require_once(__DIR__.'/../../components/strategy/OptionOrder.php');

class BearCallSpreadOrder extends OptionOrder {

    public function isValid() {
        return $this->numOfLegs() == 2;
    }

    public function getShortLeg() {
        return $this->firstShortCallLeg();
    }

    public function getLongLeg() {
        return $this->firstLongCallLeg();
    }

    public function getDelta() {
        return $this->getShortLeg()->getDelta();
    }

    public function getRisk() {
        $shortStrike = $this->getShortLeg()->getStrike();
        $longStrike = $this->getLongLeg()->getStrike();
        return $longStrike - $shortStrike;
    }

    public function getROI() {
        return round(-1*$this->getPrice()/$this->getRisk(),3);
    }
}
