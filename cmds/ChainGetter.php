<?php

require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    const DEFAULT_ENGINE_CONFIG = 'engine.json';
    const DEFAULT_SYMBOL = 'TSLA';
    const DEFAULT_CONTRACT_TYPE = 'ALL';
    const DEFAULT_STRIKES = 10;
    const DEFAULT_DAYS = 30;

    public function isColorEnabled() {
        return true;
    }

    public function getCallOpenColor() {
        if (!$this->isColorEnabled()) {
            return "";
        }
        return "\033[32m";
    }

    public function getCallCloseColor() {
        if (!$this->isColorEnabled()) {
            return "";
        }
        return "\033[39m";
    }

    public function getPutOpenColor() {
        if (!$this->isColorEnabled()) {
            return "";
        }
        return "\033[31m";
    }

    public function getPutCloseColor() {
        if (!$this->isColorEnabled()) {
            return "";
        }
        return "\033[39m";
    }

    public function process() {
        $quoter = $this->engine->getComponent('quoter');
        $quotes = $quoter->getOptionChain($this->symbol, $this->startDate, $this->endDate, 
                      $this->contractType, $this->strikes);
        foreach ($quotes as $expDate => $strikes) {
            printf("%-12s %-20s %-6s %-6s %-6s %-6s %-20s %-6s %-6s %-6s\n", "EXP", "CALL", "BID", "ASK", "DELTA", "STRIKE", "PUT", "BID", "ASK", "DELTA");
            printf("%-12s %-20s %-6s %-6s %-6s %-6s %-20s %-6s %-6s %-6s\n", "---", "----", "---", "---", "-----", "------", "---", "---", "---", "-----");
            foreach ($strikes as $strike => $putCalls) {
                print(str_pad($expDate, 12));
                print(" ");
                if ($putCalls['CALL']->getDelta() > 0.5) {
                    print($this->getCallOpenColor());
                }
                print(str_pad($putCalls['CALL']->getSymbol(), 20));
                print(" ");
                print(str_pad($putCalls['CALL']->getBid(), 6));
                print(" ");
                print(str_pad($putCalls['CALL']->getAsk(), 6));
                print(" ");
                print(str_pad($putCalls['CALL']->getDelta(), 6));
                print(" ");
                if ($putCalls['CALL']->getDelta() > 0.5) {
                    print($this->getCallCloseColor());
                }
                print(str_pad($strike, 6));
                print(" ");
                if ($putCalls['PUT']->getDelta() < -0.5) {
                    print($this->getPutOpenColor());
                }
                print(str_pad($putCalls['PUT']->getSymbol(), 20));
                print(" ");
                print(str_pad($putCalls['PUT']->getBid(), 6));
                print(" ");
                print(str_pad($putCalls['PUT']->getAsk(), 6));
                print(" ");
                print(str_pad($putCalls['PUT']->getDelta(), 6));
                if ($putCalls['PUT']->getDelta() < -0.5) {
                    print($this->getPutCloseColor());
                }
                print("\n");
            }
            print("\n");
        }
    }

    public function usage() {
        print("Usage: php ChainGetter.php ".
                    "[-h] ".
                    "[--engine_config <engine_config default:".self::DEFAULT_ENGINE_CONFIG.">] ".
                    "[--symbol <symbol default:".self::DEFAULT_SYMBOL.">] ".
                    "[--contract_type <contract type possible:ALL|CALL|PUT default:".self::DEFAULT_CONTRACT_TYPE."] ".
                    "[--strikes <strikes default:".self::DEFAULT_STRIKES.">] ".
                    "[--days <days default:".self::DEFAULT_DAYS.">] "
        );
        print("\n");
        exit();
    }

    public function initEngine() {
        $this->engine_config = __DIR__."/../configs/".self::DEFAULT_ENGINE_CONFIG;
        if (isset($options['engine_config'])) {
            $this->engine_config = __DIR__."/../configs/".$options['engine_config'];
        }
        $configs = $this->loadConfig($this->engine_config);
        $this->engine = new TradeEngine();
        $this->engine->init($configs);
    }

    public function init() {
        $options = getopt("h", array("help", "engine_config::", "symbol::", "contract_type::", "strikes::", "days::"));
        if (isset($options['h']) || isset($options['help'])) {
            $this->usage();
        }
        $this->symbol = self::DEFAULT_SYMBOL;
        if (isset($options['symbol'])) {
            $this->symbol = $options['symbol'];
        }
        $this->contractType = self::DEFAULT_CONTRACT_TYPE;
        if (isset($options['contract_type'])) {
            $this->contractType = $options['contract_type'];
        }
        $this->strikes = self::DEFAULT_STRIKES;
        if (isset($options['strikes'])) {
            $this->strikes = $options['strikes'];
        }
        $this->days = self::DEFAULT_DAYS;
        if (isset($options['days'])) {
            $this->days = $options['days'];
        }
        $this->startDate = time();
        $this->endDate = time()+$this->days*24*60*60;

        $this->initEngine();
    }

}

$cmd = new Main();
$cmd->execute();
