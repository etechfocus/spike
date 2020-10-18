<?php

require_once(__DIR__.'/../../../../components/scanner/StrategyParams.php');

class ComboParams extends StrategyParams {

    public function getStrategies() {
        return $this->get('strategies');
    }

}
