<?php

namespace Solcre\UpdatersStrategies\Tests;

use Solcre\UpdatersStrategies\Strategies\BrouUpdaterStrategy;
use PHPUnit_Framework_TestCase;

class BrouUpdaterStrategyTest extends PHPUnit_Framework_TestCase {

    private $strategy;
    private $siteString;
    private $data;

    public function setUp() {
        $this->strategy = new BrouUpdaterStrategy('http://www.bancorepublica.com.uy//c/portal/render_portlet?p_l_id=123137&p_p_id=ExchangeLarge_WAR_ExchangeRate5121_INSTANCE_P2Af', 1);

        $source_url = new \ReflectionProperty($this->strategy, 'source_url');
        $source_url->setAccessible(true);

        $method = new \ReflectionMethod($this->strategy, 'getSiteString');
        $method->setAccessible(true);

        $update = new \ReflectionMethod($this->strategy, 'parser');
        $update->setAccessible(true);

        $this->siteString = $method->invoke($this->strategy, $source_url->getValue($this->strategy));
        $this->data = $update->invoke($this->strategy, $this->siteString);
    }

    public function testGetSiteStringNotEmpty() {
        $this->assertNotEmpty($this->siteString);
    }

    public function testParserIsArray() {
        $this->assertInternalType('array', $this->data);
    }

    public function testParserCountArray() {
        $this->assertEquals(9, count($this->data));
    }

    public function testParserData() {
        $this->assertNotEmpty($this->data['arg_buy']);
        $this->assertNotEmpty($this->data['arg_sell']);
        $this->assertNotEmpty($this->data['br_sell']);
        $this->assertNotEmpty($this->data['br_buy']);
        $this->assertNotEmpty($this->data['eur_sell']);
        $this->assertNotEmpty($this->data['eur_buy']);
        $this->assertNotEmpty($this->data['us_sell']);
        $this->assertNotEmpty($this->data['us_buy']);
        $this->assertNotEmpty($this->data['source']);
    }

    public function testParserSameData() {

        $site = trim(preg_replace('/\s+/', ' ', $this->siteString));
        $site = preg_replace('/\t+/', ' ', $site);


        preg_match_all('/<td class="currency">(.*?)<\/td>/', $site, $data);
        $dataWeb  = array(
            trim($data[1][0]),
            trim($data[1][2]),
            trim($data[1][3]),
            trim($data[1][4]),
        );


        $dataOrder = array(
            'Dólar',
            'Euro',
            'Peso Argentino',
            'Real',
        );
        
       $this->assertEquals($dataOrder, $dataWeb);
        
    }

}
