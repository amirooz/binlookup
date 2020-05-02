<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\App;

class AppTest extends TestCase
{
   public function testBinData()
    {
        $app = new App();

        $app->binData('https://lookup.binlist.net/',41417360);

        $this->assertEquals($app->bin->country->alpha2, 'US');

    }

    public function testGetRate()
    {
        $app = new App();

        $app->getRate('https://api.exchangeratesapi.io/latest','USD');

        $this->assertEquals($app->rate, '1.0876');
    }

    public function testIsEu()
    {
        $app = new App();
        
        $this->assertEquals($app->isEu('DK'), 'yes');
    }

}