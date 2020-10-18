<?php

require_once(__DIR__.'/../lib/engine/TradeEngine.php');
require_once(__DIR__.'/../lib/cmd/BaseCmd.php');

class Main extends BaseCmd {

    const DEFAULT_ENGINE_CONFIG = 'engine.json';

    public function process() {
        $trader = $this->engine->getComponent('trader');
    }

    public function usage() {
        print("Usage: php Trader.php ".
                    "[-h] ".
                    "[--engine_config <engine_config default:".self::DEFAULT_ENGINE_CONFIG.">] "
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
        $options = getopt("h", array("help", "engine_config::"));
        if (isset($options['h']) || isset($options['help'])) {
            $this->usage();
        }

        $this->initEngine();
    }
}

$cmd = new Main();
$cmd->execute();
