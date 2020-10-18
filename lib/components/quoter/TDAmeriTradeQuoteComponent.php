<?php

require_once(__DIR__.'/../../engine/common/Constants.php');
require_once(__DIR__.'/../../engine/common/StockQuote.php');
require_once(__DIR__.'/../../engine/common/OptionQuote.php');
require_once(__DIR__.'/../../engine/common/EngineComponent.php');

class TDAmeriTradeQuoteComponent extends EngineComponent {

    const OPTION_TYPE_CALL = 'CALL';
    const OPTION_TYPE_PUT = 'PUT';

    const CONFIG_API_KEY = 'api_key';

    private function request($uri, $params) {
        $params['apikey'] = $this->getConfig(self::CONFIG_API_KEY);
        $query = $uri.'?'.http_build_query($params);
        if ($this->engine->isDebug()) {
            $params['apikey']="...";
            $this->engine->debug($uri.'?'.http_build_query($params));
        }
        $content = file_get_contents($query);
        $resp = json_decode($content, true);
        return $resp;
    }

    public function getStockQuote($symbol) {
        $symbol = strtoupper($symbol);
        $params = array();
        $resp = $this->request('https://api.tdameritrade.com/v1/marketdata/'.$symbol.'/quotes', $params);
        $candle = $resp[$symbol];
        $attrs = array();
        $attrs['date'] = date("Y-m-d",$candle['quoteTimeInLong']/1000);
        $attrs['datetime'] = date("Y-m-d H:i:s",$candle['quoteTimeInLong']/1000);
        $attrs['symbol'] = $symbol;
        $attrs['open'] = $candle['openPrice'];
        $attrs['close'] = $candle['closePrice'];
        $attrs['bid'] = $candle['bidPrice'];
        $attrs['ask'] = $candle['askPrice'];
        $quote = new StockQuote($attrs);
        return $quote;
    }

    public function getStockPriceHistory($symbol, $startDate, $endDate) {
        $symbol = strtoupper($symbol);
        $params = array();
        $params['periodType']='month';
        $params['frequencyType']='daily';
        $params['startDate']=($startDate*1000);
        $params['endDate']=($endDate*1000);
        $resp = $this->request('https://api.tdameritrade.com/v1/marketdata/'.$symbol.'/pricehistory', $params);
        $quotes = array();
        foreach ($resp['candles'] as $candle) {
            $attrs = array();
            $attrs['date'] = date("Y-m-d",$candle['datetime']/1000);
            if (isset($candle['quoteTimeInLong'])) {
                $attrs['datetime'] = date("Y-m-d H:i:s",$candle['quoteTimeInLong']/1000);
            } else {
                $attrs['datetime'] = date("Y-m-d H:i:s",$candle['datetime']/1000);
            }
            $attrs['symbol'] = $symbol;
            $attrs['open'] = $candle['open'];
            $attrs['close'] = $candle['close'];
            $attrs['bid'] = $candle['close'];
            $attrs['ask'] = $candle['close'];
            $attrs['volume'] = $candle['volume'];
            $quote = new StockQuote($attrs);
            $quotes[] = $quote;
        }
        return $quotes;
    }

    public function getOptionChain($symbol, $startDate, $endDate, $contractType, $strikeCount) {
        $symbol = strtoupper($symbol);
        $params = array();
        $params['symbol']=$symbol;
        $params['contractType']=$contractType;
        $params['strikeCount']=$strikeCount;
        $params['includeQuotes']='TRUE';
        $params['fromDate']=date("Y-m-d", $startDate);
        $params['toDate']=date("Y-m-d", $endDate);
        $resp = $this->request('https://api.tdameritrade.com/v1/marketdata/chains', $params);
        $quotes = array();
        foreach ($resp['callExpDateMap'] as $expDate => $strikes) {
            $tokens = explode(":", $expDate);
            $expDate = $tokens[0];
            foreach ($strikes as $strike => $price) {
                $attrs = array();
                $attrs['expDate'] = $expDate;
                $attrs['symbol'] = $price[0]['symbol'];
                $attrs['type'] = Constants::CALL;
                $attrs['strike'] = $strike;
                $attrs['description'] = $price[0]['description'];
                $attrs['bid'] = $price[0]['bid'];
                $attrs['ask'] = $price[0]['ask'];
                $attrs['bidSize'] = $price[0]['bidSize'];
                $attrs['askSize'] = $price[0]['askSize'];
                $attrs['delta'] = $price[0]['delta'];
                $attrs['theta'] = $price[0]['theta'];
                $attrs['vega'] = $price[0]['vega'];
                $quote = new OptionQuote($attrs);
                $quotes[$expDate][$strike][Constants::CALL] = $quote;
            }
        }
        foreach ($resp['putExpDateMap'] as $expDate => $strikes) {
            $tokens = explode(":", $expDate);
            $expDate = $tokens[0];
            foreach ($strikes as $strike => $price) {
                $attrs = array();
                $attrs['expDate'] = $expDate;
                $attrs['symbol'] = $price[0]['symbol'];
                $attrs['type'] = Constants::PUT;
                $attrs['strike'] = $strike;
                $attrs['description'] = $price[0]['description'];
                $attrs['bid'] = $price[0]['bid'];
                $attrs['ask'] = $price[0]['ask'];
                $attrs['bidSize'] = $price[0]['bidSize'];
                $attrs['askSize'] = $price[0]['askSize'];
                $attrs['delta'] = $price[0]['delta'];
                $attrs['theta'] = $price[0]['theta'];
                $attrs['vega'] = $price[0]['vega'];
                $quote = new OptionQuote($attrs);
                $quotes[$expDate][$strike][Constants::PUT] = $quote;
            }
        }
        return $quotes;
    }
}
