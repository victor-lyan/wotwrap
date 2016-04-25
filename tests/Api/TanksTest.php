<?php

use WotWrap\Api;
use Mockery as m;

class TanksTest extends PHPUnit_Framework_TestCase
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

    public function testStatsByInteger()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('tanks/stats/', ['tank_id' => 1057, 'application_id' => 'id', 'account_id' => 11782434])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/tanks.stats.azzzrael.tank1057.json'), 200));

        $api = new Api('id', $this->client);
        $stats = $api->tanks()->stats(11782434, ['tank_id' => 1057]);
        $this->assertEquals(6, $stats[1057]->all['draws']);
    }
    
    public function testStatsByIdentity()
    {
        $this->client->shouldReceive('baseUrl')->twice();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id', 'type' => 'exact'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.exact.json'), 200));

        $this->client->shouldReceive('request')
            ->once()
            ->with('tanks/stats/', ['account_id' => 11782434, 'application_id' => 'id', 'tank_id' => 1057])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/tanks.stats.azzzrael.tank1057.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael', ['type' => 'exact']);
        $stats = $api->tanks()->stats($azzzrael, ['tank_id' => 1057]);
        $this->assertEquals(6, $stats[1057]->all['draws']);
    }

    /**
     * @expectedException \WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testStatsParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->tanks()->stats('a12303532', ['tank_id' => 1057]);
    }
    
    public function testAchievementsByInteger()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('tanks/achievements/', ['tank_id' => 1057, 'application_id' => 'id', 'account_id' => 11782434])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/tanks.achievements.azzzrael.tank1057.json'), 200));

        $api = new Api('id', $this->client);
        $achievements = $api->tanks()->achievements(11782434, ['tank_id' => 1057]);
        $this->assertEquals(9, $achievements[1057]->achievements['warrior']);
    }

    public function testAchievementsByIdentity()
    {
        $this->client->shouldReceive('baseUrl')->twice();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id', 'type' => 'exact'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.exact.json'), 200));

        $this->client->shouldReceive('request')
            ->once()
            ->with('tanks/achievements/', ['account_id' => 11782434, 'application_id' => 'id', 'tank_id' => 1057])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/tanks.achievements.azzzrael.tank1057.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael', ['type' => 'exact']);
        $achievements = $api->tanks()->achievements($azzzrael, ['tank_id' => 1057]);
        $this->assertEquals(9, $achievements[1057]->achievements['warrior']);
    }

    /**
     * @expectedException \WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testAchievementsParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->tanks()->achievements('a12303532', ['tank_id' => 1057]);
    }

    /**
     * @expectedException WotWrap\Exception\MultipleAccountIdException
     */
    public function testStatsMultipleAccountIds()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael');
        $api->tanks()->stats($azzzrael, ['tank_id' => 1057]);
    }

    /**
     * @expectedException WotWrap\Exception\MultipleAccountIdException
     */
    public function testAchievementsMultipleAccountIds()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('account/list/', ['search' => 'Azzzrael', 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/account.azzzrael.json'), 200));

        $api = new Api('id', $this->client);
        $account = $api->account();
        $azzzrael = $account->findByName('Azzzrael');
        $api->tanks()->achievements($azzzrael, ['tank_id' => 1057]);
    }
}
