<?php

require_once(__DIR__.'/../../../../engine/common/Constants.php');
require_once(__DIR__.'/../../../../components/scanner/OptionStrategy.php');
require_once('ComboOrder.php');
require_once('ComboParams.php');

class ComboStrategy extends OptionStrategy {

    public function getName() {
        return "ComboStrategy";
    }

    public function init($engine, $id, $configs) {
        parent::init($engine, $id, $configs);
    }

    public function createParams($configs) {
        return new ComboParams($configs);
    }

    public function findOrders() {
        $quoter = $this->engine->getComponent('quoter');
        $ret = array();
        return $ret;
    }

}
