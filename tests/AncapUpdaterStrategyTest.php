<?php

namespace Solcre\UpdatersStrategies\Tests;

use Solcre\UpdatersStrategies\Strategies\AncapUpdaterStrategy;
use PHPUnit_Framework_TestCase;

class AncapUpdaterStrategyTest extends PHPUnit_Framework_TestCase {

    private $strategy;
    private $siteStringFuel;
    private $data;

    public function setUp() {
        $this->strategy = new AncapUpdaterStrategy('http://www.ancap.com.uy/XML/Precios/precios.txt', 1);

        $source_url = new \ReflectionProperty($this->strategy, 'source_url');
        $source_url->setAccessible(true);

        $getFileFromUrl = new \ReflectionMethod($this->strategy, 'getFileFromUrl');
        $getFileFromUrl->setAccessible(true);

        $update = new \ReflectionMethod($this->strategy, 'parser');
        $update->setAccessible(true);

        $this->siteStringFuel = $getFileFromUrl->invoke($this->strategy, $source_url->getValue($this->strategy));
        $this->data = $update->invoke($this->strategy, $this->siteStringFuel);
    }

    public function testGetSiteFuelStringNotEmpty() {
        $this->assertNotEmpty($this->siteStringFuel);
    }

    public function testGetSiteGasStringNotEmpty() {
        $getGasPrice = new \ReflectionMethod($this->strategy, 'getGasPrice');
        $getGasPrice->setAccessible(true);
        $gasPrice = $getGasPrice->invoke($this->strategy);
        $this->assertNotEmpty($gasPrice);
    }

    public function testParserIsArray() {
        $this->assertInternalType('array', $this->data);
    }

    public function testParserCountArray() {
        $this->assertEquals(7, count($this->data));
    }

    public function testParserData() {
        $this->assertNotEmpty($this->data['premium']);
        $this->assertNotEmpty($this->data['super']);
        $this->assertNotEmpty($this->data['especial']);
        $this->assertNotEmpty($this->data['gasoil']);
        $this->assertNotEmpty($this->data['gasoil_esp']);
        $this->assertNotEmpty($this->data['supergas']);
        $this->assertNotEmpty($this->data['source']);
    }

    public function testParserSameData() {
        $dataOrder = array(
            'premium',
            'super',
            'especial',
            'gasoil',
            'gasoil_esp',
            'supergas',
            'source',
        );
        $keys = array_keys($this->data);

        $this->assertEquals($dataOrder, $keys);
    }

}
