<?php

require_once('OptionOrder.php');

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
        return $this->getShortLeg()->getQuote()->getDelta();
    }

    public function getRisk() {
        $shortQuote = $this->getShortLeg()->getQuote();
        $longQuote = $this->getLongLeg()->getQuote();
        return $longQuote->getStrike() - $shortQuote->getStrike();
    }

    public function getROI() {
        return round(-1*$this->getPrice()/$this->getRisk(),3);
    }
}
