<?php

require_once(__DIR__.'/../lib/engine/common/OptionLeg.php');
require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    public function printOrder($strategy, $order) {
        printf("%-12s %-52s %-6s %-6s %-6s %-6s\n", $strategy->getId(),
            $order->getDescription(), $order->getPrice(), 
            $order->getDelta(), $order->getRisk(), $order->getROI());
    }

    public function process() {
        foreach ($this->strategies as $strategyId => $strategy) {
            $orders = $strategy->findOrders();
            printf("%-12s %-52s %-6s %-6s %-6s %-6s\n", "STRATEGY", "TRADE", "PRICE", "DELTA", "RISK", "ROI");
            printf("%-12s %-52s %-6s %-6s %-6s %-6s\n", "--------", "-----", "-----", "-----", "----", "---");
            foreach ($orders as $order) {
                $this->printOrder($strategy, $order);
            }
            print("\n");
        }
    }

    public function initStrategies() {
        foreach ($this->configs['strategies'] as $strategyId => $strategyConfig) {
            require_once(__DIR__.'/../'.$strategyConfig['path'].'.php');
            $this->strategies[$strategyId] = new $strategyConfig['class']();
            $configs = array();
            if (isset($this->configs['strategies'][$strategyId])) {
                $configs = $this->configs['strategies'][$strategyId];
            }
            $this->strategies[$strategyId]->init($this->engine, $strategyId, $configs);
        }
    }

    public function init() {
        $configs = $this->loadConfig(__DIR__."/../configs/engine.json");
        $this->engine = new TradeEngine();
        $this->engine->init($configs);

        $this->configs = $this->loadConfig(__DIR__."/../configs/scanner.json");
        $this->initStrategies();
    }

}

$cmd = new Main();
$cmd->execute();
