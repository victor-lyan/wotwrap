<?php

use WotWrap\Api;
use Mockery as m;

class EncyclopediaTest extends PHPUnit_Framework_TestCase
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
    
    public function testVehicles()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/vehicles/', ['nation' => 'japan', 'application_id' => 'id', 'tier' => 10])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.vehicles.japan.tier10.json'), 200));

        $api = new Api('id', $this->client);
        $vehicles = $api->encyclopedia()->vehicles(['nation' => 'japan', 'tier' => 10]);
        $this->assertEquals("STB-1", $vehicles[3681]->short_name);
    }
    
    public function testVehicleProfile()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/vehicleprofile/', ['tank_id' => 1057, 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.vehicleprofile.m4.json'), 200));

        $api = new Api('id', $this->client);
        $vehicleProfile = $api->encyclopedia()->vehicleprofile(1057);
        $this->assertEquals(350, $vehicleProfile[1057]->engine['power']);
    }

    /**
     * @expectedException WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testVehicleProfileParameterMismatch()
    {
        $api = new Api('id', $this->client);
        $api->encyclopedia()->vehicleprofile('a22');
    }

    public function testAchievements()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/achievements/', ['fields' => 'name', 'application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.achievements.onlynames.json'), 200));

        $api = new Api('id', $this->client);
        $achievements = $api->encyclopedia()->achievements(['fields' => 'name']);
        $this->assertEquals('armorPiercer', $achievements['armorPiercer']->name);
    }

    public function testInfo()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/info/', ['application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200));

        $api = new Api('id', $this->client);
        $info = $api->encyclopedia()->info();
        $this->assertEquals('9.14', $info->game_version);
    }

    public function testArenas()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/arenas/', ['application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.arenas.json'), 200));

        $api = new Api('id', $this->client);
        $arenas = $api->encyclopedia()->arenas();
        $this->assertEquals('summer', $arenas['11_murovanka']->camouflage_type);
    }

    public function testProvisions()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/provisions/', ['application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.provisions.json'), 200));

        $api = new Api('id', $this->client);
        $provisions = $api->encyclopedia()->provisions();
        $this->assertEquals(100, $provisions[249]->weight);
    }

    public function testBoosters()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/provisions/', ['application_id' => 'id'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.provisions.json'), 200));

        $api = new Api('id', $this->client);
        $provisions = $api->encyclopedia()->provisions();
        $this->assertEquals(100, $provisions[249]->weight);
    }

    public function testPersonalMissions()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/personalmissions/', ['application_id' => 'id', 'operation_id' => 2])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.personalmissions.campaign2.json'), 200));

        $api = new Api('id', $this->client);
        $missions = $api->encyclopedia()->personalMissions(['operation_id' => 2]);
        $this->assertEquals(15, $missions[2]->operations[5]['missions_in_set']);
    }

    public function testModules()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/modules/', ['application_id' => 'id', 'type' => 'vehicleGun', 'nation' => 'france'])
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.modules.json'), 200));

        $api = new Api('id', $this->client);
        $missions = $api->encyclopedia()->modules('vehicleGun', 'france');
        $this->assertEquals(1520, $missions[68]->weight);
    }

    /**
     * @expectedException WotWrap\Exception\ParameterTypeMismatchException
     */
    public function testModulesParametersMismatch()
    {
        $api = new Api('id', $this->client);
        $api->encyclopedia()->modules(22, true);
    }
}
