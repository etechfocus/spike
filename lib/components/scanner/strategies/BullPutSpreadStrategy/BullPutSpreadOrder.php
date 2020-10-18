<?php

require_once(__DIR__.'/../../../../components/scanner/OptionOrder.php');

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
        return $this->getShortLeg()->getDelta();
    }

    public function getRisk() {
        $shortStrike = $this->getShortLeg()->getStrike();
        $longStrike = $this->getLongLeg()->getStrike();
        return $shortStrike - $longStrike;
    }

    public function getROI() {
        return round(-1*$this->getPrice()/$this->getRisk(),3);
    }
}
