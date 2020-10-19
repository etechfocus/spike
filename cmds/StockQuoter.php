<?php

require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    const DEFAULT_ENGINE_CONFIG = 'engine.json';
    const DEFAULT_SYMBOL = 'TSLA';
    const DEFAULT_DAYS = 30;
    const DEFAULT_PERIOD_TYPE = 'month';
    const DEFAULT_FREQUENCY_TYPE = 'daily';

    public function process() {
        $quoter = $this->engine->getComponent('quoter');
        $quotes = $quoter->getStockPriceHistory($this->symbol, $this->startDate, $this->endDate, $this->period_type, $this->frequency_type);
        printf("%-12s %-6s %-6s\n", "DATE", "SYMBOL", "CLOSE");
        printf("%-12s %-6s %-6s\n", "----", "------", "-----");
        foreach ($quotes as $quote) {
            printf("%-12s %-6s %0.3f\n", $quote->getDate(), $quote->getSymbol(), $quote->getClose());
	}
    }

    public function usage() {
        print("Usage: php StockQuoter.php ".
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
        $options = getopt("h", array("help", "engine_config::", "symbol::", "days::", "period_type::", "frequency_type"));
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

        $this->period_type = self::DEFAULT_PERIOD_TYPE;
        if (isset($options['period_type'])) {
            $this->period_type = $options['period_type'];
        }
        $this->frequency_type = self::DEFAULT_FREQUENCY_TYPE;
        if (isset($options['frequency_type'])) {
            $this->frequency_type = $options['frequency_type'];
        }

        $this->initEngine();
    }
}

$cmd = new Main();
$cmd->execute();
