<?php

require_once(__DIR__.'/../lib/engine/common/OptionLeg.php');
require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    const DEFAULT_ENGINE_CONFIG = 'engine.json';
    const DEFAULT_SCANNER_CONFIG = 'scanner.json';

    public function printOrder($strategy, $order) {
        printf("%-12s %-52s %-6s %-6s %-6s %-6s\n", $strategy->getId(),
            $order->getDescription(), $order->getPrice(), 
            $order->getDelta(), $order->getRisk(), $order->getROI());
    }

    public function process() {
        foreach ($this->strategies as $strategyId => $strategy) {
            $orders = $strategy->findOrders();
            printf("%-12s %-52s %-6s %-6s %-6s %-6s\n", "STRATEGY", "ORDER", "PRICE", "DELTA", "RISK", "ROI");
            printf("%-12s %-52s %-6s %-6s %-6s %-6s\n", "--------", "-----", "-----", "-----", "----", "---");
            foreach ($orders as $order) {
                $this->printOrder($strategy, $order);
            }
            print("\n");
        }
    }

    public function usage() {
        print("Usage: php OptionScanner.php ".
                    "[-h] ".
                    "[--engine_config <engine config default:".self::DEFAULT_ENGINE_CONFIG.">] ".
                    "[--scanner_config <scanner config default:".self::DEFAULT_SCANNER_CONFIG.">] "
        );
        print("\n");
        exit();
    }

    public function initStrategies() {
        foreach ($this->configs['strategies'] as $strategyId => $strategyConfig) {
            $path = __DIR__.'/../lib/components/strategy/'.$strategyConfig['class'].'.php';
            if (isset($strategyConfig['path'])) {
                $path = __DIR__.'/../'.$strategyConfig['path'].'.php';
            }
            require_once($path);
            $this->strategies[$strategyId] = new $strategyConfig['class']();
            $configs = array();
            if (isset($this->configs['strategies'][$strategyId])) {
                $configs = $this->configs['strategies'][$strategyId];
            }
            $this->strategies[$strategyId]->init($this->engine, $strategyId, $configs);
        }
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

    public function initScanner() {
        $this->scanner_config = __DIR__."/../configs/".self::DEFAULT_SCANNER_CONFIG;
        if (isset($options['scanner_config'])) {
            $this->scanner_config = __DIR__."/../configs/".$options['scanner_config'];
        }
        $this->configs = $this->loadConfig($this->scanner_config);
        $this->initStrategies();
    }

    public function init() {
        $options = getopt("h", array("help", "engine_config::", "scanner_config::"));
        if (isset($options['h']) || isset($options['help'])) {
            $this->usage();
        }

        $this->initEngine();
        $this->initScanner();
    }
}

$cmd = new Main();
$cmd->execute();
