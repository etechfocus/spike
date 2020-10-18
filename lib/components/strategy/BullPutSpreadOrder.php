<?php

require_once('OptionOrder.php');

class BullPutSpreadOrder extends OptionOrder {

    public function getName() {
        return "BullPutSpreadOrder";
    }

    public function isValid() {
        return $this->numOfLegs() == 2;
    }

    public function getShortLeg() {
        return $this->firstShortPutLeg();
    }

    public function getLongLeg() {
        return $this->firstLongPutLeg();
    }

    public function getDelta() {
        return $this->getShortLeg()->getQuote()->getDelta();
    }

    public function getRisk() {
        $shortQuote = $this->getShortLeg()->getQuote();
        $longQuote = $this->getLongLeg()->getQuote();
        return $shortQuote->getStrike() - $longQuote->getStrike();
    }

    public function getROI() {
        return round(-1*$this->getPrice()/$this->getRisk(),3);
    }
}
