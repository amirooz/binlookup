<?php

namespace App;

abstract class CheckEu
{
    protected function isEu($c) 
    {
        $result = false;
        switch($c) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                $result = 'yes';
                return $result;
            default:
                $result = 'no';
        }
        return $result;
    }
}

abstract class BinLookup extends CheckEu
{
    protected $bin;

    protected $rate;

    protected function binData($url,$value)
    {
        $binResults = file_get_contents($url.$value);
        if (!$binResults)
            die('error!');
        $this->bin = json_decode($binResults);
        return $this->bin;
        
    }

    protected function getRate($url,$cur)
    {
        $this->rate = @json_decode(file_get_contents($url), true)['rates'][$cur];
        return $this->rate;
    }

    protected function isEu($c)
    {
        return parent::isEu($c);
    }

}

class App extends BinLookup
{

    public $bin;

    public $rate;

    public function index($arg)
    {
        foreach (explode("\n", file_get_contents($arg)) as $row) {
            
            if (empty($row)) break;
            $p = explode(",",$row);
            $p2 = explode(':', $p[0]);
            $value[0] = trim($p2[1], '"');
            $p2 = explode(':', $p[1]);
            $value[1] = trim($p2[1], '"');
            $p2 = explode(':', $p[2]);
            $value[2] = trim($p2[1], '"}');
        
            $this->binData('https://lookup.binlist.net/',$value[0]);

            $this->getRate('https://api.exchangeratesapi.io/latest',$value[2]);

            $isEu = $this->isEu($this->bin->country->alpha2);
            
            if ($value[2] == 'EUR' or $this->rate == 0) {
                $amntFixed = $value[1];
            }
            if ($value[2] != 'EUR' or $this->rate > 0) {
                @$amntFixed = $value[1] / $this->rate;
            }
        
            echo round($amntFixed * ($isEu == 'yes' ? 0.01 : 0.02),2);
            print "\n";
        }
    }

    public function binData($url,$value)
    {
        $this->bin = parent::binData($url, $value);
        return;
    }

    public function getRate($url,$cur)
    {
        $this->rate = parent::getRate($url, $cur);
        return;
    }

    public function isEu($c) 
    {
        return parent::isEu($c);
    }    

}

$app = new App();
$app->index($argv[1]);

