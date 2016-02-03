<?php

namespace Solcre\UpdatersStrategies\Tests;

use Solcre\UpdatersStrategies\Strategies\WundergroundUpdaterStrategy;
use PHPUnit_Framework_TestCase;

class WundergroundUpdaterStrategyTest extends PHPUnit_Framework_TestCase {

    private $strategy;
    private $siteString;

    public function setUp() {
        $this->strategy = new WundergroundUpdaterStrategy('http://api.wunderground.com/api/ec436027cd5ff12b/geolookup/conditions/forecast/q/Uruguay/Montevideo.json', 1);

        $source_url = new \ReflectionProperty($this->strategy, 'source_url');
        $source_url->setAccessible(true);

        $getSiteString = new \ReflectionMethod($this->strategy, 'getSiteString');
        $getSiteString->setAccessible(true);

        $this->siteString = $getSiteString->invoke($this->strategy, $source_url->getValue($this->strategy));
    }

    public function testGetSiteStringNotWrong() {
        $json = json_decode($this->siteString, true);
        $this->assertArrayNotHasKey('error', $json['response']);
    }

    public function testGetSiteStringNotEmpty() {
        $this->assertNotEmpty($this->siteString);
    }

    public function testParserIsArray() {

        $update = new \ReflectionMethod($this->strategy, 'parser');
        $update->setAccessible(true);

        $data = $update->invoke($this->strategy, $this->siteString);

        $this->assertInternalType('array', $data['data']);
        $this->assertInternalType('array', $data['data']['actual']);
        $this->assertInternalType('array', $data['data']['pronostico']);
        $this->assertInternalType('array', $data);
    }

    public function testParserCountArray() {
        $update = new \ReflectionMethod($this->strategy, 'parser');
        $update->setAccessible(true);

        $data = $update->invoke($this->strategy, $this->siteString);
        $this->assertEquals(3, count($data));
    }

}
