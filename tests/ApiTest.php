<?php

use WotWrap\Api;
use Mockery as m;

class ApiTest extends PHPUnit_Framework_TestCase
{
    protected $applicationIds;

    public function setUp()
    {
        $this->applicationIds = ['ru' => 'id'];
    }
    
    public function testAccount()
    {
        $api = new Api($this->applicationIds);
        $account = $api->account();
        $this->assertTrue($account instanceof WotWrap\Api\Account);
    }
    
    public function testAuth()
    {
        $api = new Api($this->applicationIds);
        $auth = $api->auth();
        $this->assertTrue($auth instanceof WotWrap\Api\Auth);
    }

    public function testEncyclopedia()
    {
        $api = new Api($this->applicationIds);
        $encyclopedia = $api->encyclopedia();
        $this->assertTrue($encyclopedia instanceof WotWrap\Api\Encyclopedia);
    }

    public function testTanks()
    {
        $api = new Api($this->applicationIds);
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
        $api = new Api($this->applicationIds);
        $api->nope();
    }

    public function testGetLimits()
    {
        $api = new Api($this->applicationIds);
        $api->limit(5, 5);
        $this->assertEquals(5, count($api->getLimits()));
    }

    public function testGetLimitsOneRegion()
    {
        $api = new Api($this->applicationIds);
        $api->limit(5, 5, 'na');
        $this->assertEquals(1, count($api->getLimits()));
    }
    
    public function testSetRegion()
    {
        $api = new Api($this->applicationIds);
        $resultSetRegion = $api->setRegion('na');
        $this->assertTrue($resultSetRegion instanceof Api);
    }

    /**
     * @expectedException WotWrap\Exception\InvalidRegionException 
     */
    public function testSetRegionInvalidRegion()
    {
        $api = new Api($this->applicationIds);
        $api->setRegion('br');
    }
    
    public function testSetRegionAbstractApi()
    {
        $api = new Api($this->applicationIds);
        $account = $api->account();
        $resultSetRegion = $account->setRegion('na');
        $this->assertTrue($resultSetRegion instanceof Api\AbstractApi);
    }

    /**
     * @expectedException WotWrap\Exception\InvalidRegionException
     */
    public function testSetRegionAbstractApiInvalidRegion()
    {
        $api = new Api($this->applicationIds);
        $account = $api->account();
        $resultSetRegion = $account->setRegion('nc');
        $this->assertTrue($resultSetRegion instanceof Api\AbstractApi);
    }
    
    public function testSetTimeout()
    {
        $api = new Api($this->applicationIds);
        $resultSetTimeout = $api->setTimeout(3.17);
        $this->assertTrue($resultSetTimeout instanceof Api);
    }
    
    public function testRemember()
    {
        $api = new Api($this->applicationIds);
        $rememberResult = $api->remember(60);
        $this->assertTrue($rememberResult instanceof Api);
    }
}
