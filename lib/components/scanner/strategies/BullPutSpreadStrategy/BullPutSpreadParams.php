<?php

require_once(__DIR__.'/../../../../components/scanner/StrategyParams.php');

class BullPutSpreadParams extends StrategyParams {

    public function getSymbols() {
        return $this->get('symbols');
    }

    public function getDays() {
        return $this->get('days');
    }

    public function getMinROI() {
        return $this->get('minROI');
    }

    public function getMaxROI() {
        return $this->get('maxROI');
    }

    public function getMinDelta() {
        return $this->get('minDelta');
    }

    public function getMaxDelta() {
        return $this->get('maxDelta');
    }

}
