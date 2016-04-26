<?php

use WotWrap\Api;
use Mockery as m;

class ApiTest extends PHPUnit_Framework_TestCase
{
    public function testAccount()
    {
        $api = new Api('id');
        $account = $api->account();
        $this->assertTrue($account instanceof WotWrap\Api\Account);
    }
    
    public function testAuth()
    {
        $api = new Api('id');
        $auth = $api->auth();
        $this->assertTrue($auth instanceof WotWrap\Api\Auth);
    }

    public function testEncyclopedia()
    {
        $api = new Api('id');
        $encyclopedia = $api->encyclopedia();
        $this->assertTrue($encyclopedia instanceof WotWrap\Api\Encyclopedia);
    }

    public function testTanks()
    {
        $api = new Api('id');
        $tanks = $api->tanks();
        $this->assertTrue($tanks instanceof WotWrap\Api\Tanks);
    }

    /**
     * @expectedException WotWrap\Exception\NoIdException
     */
    public function testNoIdException()
    {
        $api = new Api;
    }

    /**
     * @expectedException WotWrap\Exception\ApiClassNotFoundException
     */
    public function testApiClassNotFoundException()
    {
        $api = new Api('id');
        $api->nope();
    }

    public function testGetLimits()
    {
        $api = new Api('id');
        $api->limit(5, 5);
        $this->assertEquals(5, count($api->getLimits()));
    }

    public function testGetLimitsOneRegion()
    {
        $api = new Api('id');
        $api->limit(5, 5, 'na');
        $this->assertEquals(1, count($api->getLimits()));
    }
    
    public function testSetRegion()
    {
        $api = new Api('id');
        $resultSetRegion = $api->setRegion('na');
        $this->assertTrue($resultSetRegion instanceof Api);
    }

    /**
     * @expectedException WotWrap\Exception\InvalidRegionException 
     */
    public function testSetRegionInvalidRegion()
    {
        $api = new Api('id');
        $api->setRegion('br');
    }
    
    public function testSetTimeout()
    {
        $api = new Api('id');
        $resultSetTimeout = $api->setTimeout(3.17);
        $this->assertTrue($resultSetTimeout instanceof Api);
    }
    
    public function testRemember()
    {
        $api = new Api('id');
        $rememberResult = $api->remember(60);
        $this->assertTrue($rememberResult instanceof Api);
    }
}
