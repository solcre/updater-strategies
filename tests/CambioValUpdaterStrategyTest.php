<?php

namespace Solcre\UpdatersStrategies\Tests;

use Solcre\UpdatersStrategies\Strategies\CambioValUpdaterStrategy;
use PHPUnit_Framework_TestCase;

class CambioValUpdaterStrategyTest extends PHPUnit_Framework_TestCase {

    private $strategy;
    private $siteString;
    private $data;

    public function setUp() {
        $this->strategy = new CambioValUpdaterStrategy('http://www2.valsf.com.uy/mvdexchange/apizarradeldia.aspx', 1);

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

        $search = array('us_buy', 'us_sell', 'arg_buy', 'arg_sell', 'br_buy', 'br_sell', 'eur_buy', 'eur_sell');
        $replace = array($this->data['us_buy'], $this->data['us_sell'], $this->data['arg_buy'], $this->data['arg_sell'], $this->data['br_buy'], $this->data['br_sell'], $this->data['eur_buy'], $this->data['eur_sell']);

        //Replaces our web template of the source with the actual data.
        $webReplaced = str_replace($search, $replace, file_get_contents(__DIR__ . '/CambioValWeb.html'));

        $webReplaced = preg_replace('/\s+/', '', $webReplaced);
        $getSiteString = preg_replace('/\s+/', '', $this->siteString);

        // if it’s binary UTF-8 with get the rest of the string.
        $getCleanString = substr($getSiteString, 3);

        $this->assertEquals($webReplaced, $getCleanString);
    }

}
