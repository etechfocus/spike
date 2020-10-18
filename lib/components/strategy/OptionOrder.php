<?php

class OptionOrder {

    const LEG_TYPE_BUY = 'BUY';
    const LEG_TYPE_SELL = 'SELL';

    protected $attrs = array();
    protected $legs = array();

    public function __construct($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getDescription() {
        $ret = array();
        foreach ($this->legs as $leg) {
            $ret[] = ($leg->isLong()?'+':'-').$leg->getQty()."x".$leg->getSymbol();
        }
        return implode("/", $ret);
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
        $legs = $this->getLegsByTypes('BUY', 'CALL');
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function firstShortCallLeg() {
        $legs = $this->getLegsByTypes('SELL', 'CALL');
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function firstLongPutLeg() {
        $legs = $this->getLegsByTypes('BUY', 'PUT');
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function firstShortPutLeg() {
        $legs = $this->getLegsByTypes('SELL', 'PUT');
        if (count($legs) <= 0) {
            return false;
        }
        return $legs[0];
    }

    public function getPrice() {
        $ret = 0;
        foreach ($this->legs as $leg) {
            if ($leg->getType() == 'SELL') {
                $ret -= ($leg->getQty() * $leg->getBid());
            } else {
                $ret += ($leg->getQty() * $leg->getAsk());
            }
        }
        return round($ret, 3);
    }

}
