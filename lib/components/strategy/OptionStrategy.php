<?php

require_once(__DIR__.'/../../engine/common/Constants.php');

class OptionStrategy {

    protected $legs = array();

    public function init($engine, $id, $configs) {
        $this->engine = $engine;
        $this->id = $id;
        $this->configs = $configs;
    }

    public function getId() {
        return $this->id;
    }

    public function getConfigs() {
        return $this->configs;
    }

    public function getDescription() {
        return $this->configs['description'];
    }

    public function getName() {
        return "OptionStrategy";
    }

    public function getEngine() {
        return $this->engine;
    }

    public function addLeg($leg) {
        $this->legs[] = $leg;
    }

    public function getLegs() {
        return $this->legs;
    }

    public function numOfLegs() {
        return count($this->legs);
    }

    public function getLegsByTypes($legType, $optionType) {
        $ret = array();
        foreach ($this->legs as $leg) {
            if ($leg->getType() == $legType && $leg->getOptionType() == $optionType) {
                $ret[] = $leg;
            }
        }
        return $ret;
    }

    public function getLegsByLegType($type) {
        $ret = array();
        foreach ($this->legs as $leg) {
            if ($leg->getType() == $type) {
                $ret[] = $leg;
            }
        }
        return $ret;
    }

    public function getLegsByOptionType($type) {
        $ret = array();
        foreach ($this->legs as $leg) {
            if ($leg->getOptionType() == $type) {
                $ret[] = $leg;
            }
        }
        return $ret;
    }

    public function firstLongCallLeg() {
        $legs = $this->getLegsByTypes(Constants::LONG, Constants::CALL);
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function firstShortCallLeg() {
        $legs = $this->getLegsByTypes(Constants::SHORT, Constants::CALL);
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function firstLongPutLeg() {
        $legs = $this->getLegsByTypes(Constants::LONG, Constants::PUT);
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function firstShortPutLeg() {
        $legs = $this->getLegsByTypes(Constants::SHORT, Constants::PUT);
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function getPrice() {
        $ret = 0;
        foreach ($this->legs as $leg) {
            if ($leg->isShort()) {
                $ret -= ($leg->getQty() * $leg->getBid());
            } else {
                $ret += ($leg->getQty() * $leg->getAsk());
            }
        }
        return round($ret, 3);
    }

}
