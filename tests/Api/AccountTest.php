<?php

use WotWrap\Api;
use Mockery as m;

class AccountTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    
    public function setUp()
    {
        $this->client = m::mock('WotWrap\Client');
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testFindByName()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.json'), 200));
        
        $api = new Api('id', $this->client);
        $azzzrael = $api->account()->findByName('Azzzrael');
        $this->assertEquals(11782434, $azzzrael[0]->account_id);
    }
    
    public function testFindByNameNotExisted()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael123', 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.notexisted.json'), 200));

        $api = new Api('id', $this->client);
        $azzzrael = $api->account()->findByName('Azzzrael123');
        $this->assertTrue(count($azzzrael) == 0);
    }

    /**
     * @expectedException WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testFindByNameParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->account()->findByName(321);
    }

    public function testInfoByIdentities()
    {
        $this->client->shouldReceive('baseUrl')->twice();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.json'), 200));

        $this->client->shouldReceive('request')
            ->once()
            ->with('account/info/', ['account_id' => 11782434, 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.info.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael');
        $azzzraelInfo = $account->info($azzzrael);
        $this->assertEquals(10859, $azzzraelInfo[11782434]->statistics['all']['spotted']);
    }
    
    public function testInfoByIntegers()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/info/', ['account_id' => 11782434, 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.info.json'), 200));

        $api = new Api('id', $this->client);
        $azzzraelInfo = $api->account()->info(11782434);
        $this->assertEquals(10859, $azzzraelInfo[11782434]->statistics['all']['spotted']);
    }

    /**
     * @expectedException WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testInfoParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->account()->info(true);
    }

    /**
     * @expectedException WotWrap\Response\Api407
     */
    public function testInfoInvalidApplicationId()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/info/', ['account_id' => 11782434, 'application_id' => 'demo1'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/error.application_id_invalid.json'), 200));

        $api = new Api('demo1', $this->client);
        $api->account()->info(11782434);
    }
    
    public function testTanksByIdentities()
    {
        $this->client->shouldReceive('baseUrl')->twice();
        $this->client->shouldReceive('setRequestType')->once()
            ->with('POST');
        $this->client->shouldReceive('setTimeout')->once()
            ->with(10);
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id', 'type' => 'exact'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.json'), 200));

        $this->client->shouldReceive('request')
            ->once()
            ->with('account/tanks/', ['account_id' => 11782434, 'application_id' => 'id', 'tank_id' => '1057,2583'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.tanks.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael', ['type' => 'exact']);
        
        //let's also test setRequestType and setTimeout of AbstractApi
        $azzzraelInfo = $account->setRequestType('POST')->setTimeout(10)->tanks($azzzrael, ['tank_id' => '1057,2583']);
        $this->assertEquals(354, $azzzraelInfo[11782434]->get(1057)['wins']);
    }
    
    public function testTanksByIntegers()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/tanks/', ['account_id' => 11782434, 'application_id' => 'id', 'tank_id' => '1057,2583'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.tanks.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzraelInfo = $account->tanks(11782434, ['tank_id' => '1057,2583']);
        $this->assertEquals(354, $azzzraelInfo[11782434]->get(1057)['wins']);
    }

    /**
     * @expectedException WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testTanksParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->account()->tanks(true);
    }
    
    public function testAchievementsByIdentities()
    {
        $this->client->shouldReceive('baseUrl')->twice();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id', 'type' => 'exact'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.json'), 200));

        $this->client->shouldReceive('request')
            ->once()
            ->with('account/achievements/', ['account_id' => 11782434, 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.achievements.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael', ['type' => 'exact']);
        $azzzraelInfo = $account->achievements($azzzrael);
        $this->assertEquals(2, $azzzraelInfo[11782434]->achievements['medalCarius']);
    }

    public function testAchievementsByIntegers()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/achievements/', ['account_id' => 11782434, 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.achievements.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzraelInfo = $account->achievements(11782434);
        $this->assertEquals(2, $azzzraelInfo[11782434]->achievements['medalCarius']);
    }

    /**
     * @expectedException WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testAchievementsParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->account()->achievements('a203');
    }
}
