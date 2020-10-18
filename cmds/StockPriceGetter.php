<?php

require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    const DEFAULT_ENGINE_CONFIG = 'engine.json';
    const DEFAULT_SYMBOL = 'TSLA';
    const DEFAULT_DAYS = 30;

    public function process() {
        $quoter = $this->engine->getComponent('quoter');
        $quotes = $quoter->getStockPriceHistory($this->symbol, $this->startDate, $this->endDate);
        printf("%-12s %-6s\n", "DATE", "CLOSE");
        printf("%-12s %-6s\n", "----", "-----");
        foreach ($quotes as $quote) {
            printf("%-12s %0.3f\n", $quote->getDate(), $quote->getClose());
	}
    }

    public function usage() {
        print("Usage: php StockPriceGetter.php ".
                    "[-h] ".
                    "[--engine_config <engine_config default:".self::DEFAULT_ENGINE_CONFIG.">] ".
                    "[--symbol <symbol default:".self::DEFAULT_SYMBOL.">] ".
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
        $options = getopt("h", array("help", "engine_config::", "symbol::", "days::"));
        if (isset($options['h']) || isset($options['help'])) {
            $this->usage();
        }
        $this->symbol = self::DEFAULT_SYMBOL;
        if (isset($options['symbol'])) {
            $this->symbol = $options['symbol'];
        }
        $this->days = self::DEFAULT_DAYS;
        if (isset($options['days'])) {
            $this->days = $options['days'];
        }
        $this->startDate = time()-$this->days*24*60*60;
        $this->endDate = time();

        $this->initEngine();
    }
}

$cmd = new Main();
$cmd->execute();
