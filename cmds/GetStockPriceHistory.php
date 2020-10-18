<?php

require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    public function process() {
        $quoter = $this->engine->getComponent('quoter');
        $quotes = $quoter->getStockPriceHistory($this->symbol, $this->startDate, $this->endDate);
        printf("%-12s %-6s\n", "DATE", "CLOSE");
        printf("%-12s %-6s\n", "----", "-----");
        foreach ($quotes as $quote) {
            printf("%-12s %0.3f\n", $quote->getDate(), $quote->getClose());
	}
    }

    public function init() {
        $options = getopt("", array("engine_config::", "symbol::", "days::"));
        $this->symbol = "TSLA";
        if (isset($options['symbol'])) {
            $this->symbol = $options['symbol'];
        }
        $this->days = 30;
        if (isset($options['days'])) {
            $this->days = $options['days'];
        }
        $this->startDate = time()-$this->days*24*60*60;
        $this->endDate = time();

        $this->engine_config = __DIR__."/../configs/engine.json";
        if (isset($options['engine_config'])) {
            $this->engine_config = $options['engine_config'];
        }
        $configs = $this->loadConfig($this->engine_config);
        $this->engine = new TradeEngine();
        $this->engine->init($configs);
    }
}

$cmd = new Main();
$cmd->execute();
