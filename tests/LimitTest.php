<?php

use Mockery as m;

class LimitTest extends PHPUnit_Framework_TestCase
{
    protected $limit1;
    protected $limit2;
    protected $client;
    protected $response;
    
    protected $applicationIds;
    
    public function setUp()
    {
        $this->applicationIds = ['ru' => 'id'];
        $this->limit1 = m::mock('WotWrap\Limit\LimitInterface');
        $this->limit2 = m::mock('WotWrap\Limit\LimitInterface');
        $this->client = m::mock('WotWrap\Client');
    }
    
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException WotWrap\Exception\LimitReachedException
     */
    public function testSingleLimit()
    {
        $this->limit1->shouldReceive('setRate')
                     ->once()
                     ->with(1, 10, 'ru')
                     ->andReturn(true);

        $this->limit1->shouldReceive('hit')
                     ->twice()
                     ->with(1)
                     ->andReturn(true, false);
        
        $this->limit1->shouldReceive('isValid')
                     ->once()
                     ->andReturn(true);
        
        $this->limit1->shouldReceive('getRegion')
                     ->twice()
                     ->andReturn('ru');
        
        $this->client->shouldReceive('baseUrl')
                     ->twice();
        
        $this->client->shouldReceive('request')
                     ->with('encyclopedia/info/', [
                         'application_id' => 'id',
                     ])->once()
                     ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200));

        $api = new WotWrap\Api($this->applicationIds, $this->client);
        $api->limit(1, 10, 'ru', $this->limit1);
        $encyclopedia = $api->encyclopedia();
        $encyclopedia->info();
        $encyclopedia->info();
    }

    /**
     * @expectedException WotWrap\Exception\LimitReachedException
     */
    public function testDoubleLimit()
    {
        $this->limit1->shouldReceive('setRate')
            ->once()
            ->with(5, 10, 'ru')
            ->andReturn(true);
        $this->limit1->shouldReceive('hit')
            ->times(3)
            ->with(1)
            ->andReturn(true);
        $this->limit1->shouldReceive('isValid')
            ->once()
            ->andReturn(true);
        $this->limit1->shouldReceive('getRegion')
            ->times(3)
            ->andReturn('ru');
        
        $this->limit2->shouldReceive('setRate')
            ->once()
            ->with(2, 5, 'ru')
            ->andReturn(true);
        $this->limit2->shouldReceive('hit')
            ->times(3)
            ->with(1)
            ->andReturn(true, true, false);
        $this->limit2->shouldReceive('isValid')
            ->once()
            ->andReturn(true);
        $this->limit2->shouldReceive('getRegion')
            ->times(3)
            ->andReturn('ru');
        $this->client->shouldReceive('baseUrl')
            ->times(3);
        
        $this->client->shouldReceive('request')
            ->with('encyclopedia/info/', [
                'application_id' => 'id',
            ])->twice()
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200));

        $api = new WotWrap\Api($this->applicationIds, $this->client);
        $api->limit(5, 10, 'ru', $this->limit1);
        $api->limit(2, 5, 'ru', $this->limit2);
        $encyclopedia = $api->encyclopedia();
        $encyclopedia->info();
        $encyclopedia->info();
        $encyclopedia->info();
    }
    
    public function testAllRegionsLimit()
    {
        $this->limit1->shouldReceive('setRate')
            ->once()
            ->with(10, 5, 'ru')
            ->andReturn(true);
        $this->limit1->shouldReceive('setRate')
            ->once()
            ->with(10, 5, 'eu')
            ->andReturn(true);
        $this->limit1->shouldReceive('setRate')
            ->once()
            ->with(10, 5, 'na')
            ->andReturn(true);
        $this->limit1->shouldReceive('setRate')
            ->once()
            ->with(10, 5, 'asia')
            ->andReturn(true);
        $this->limit1->shouldReceive('setRate')
            ->once()
            ->with(10, 5, 'kr')
            ->andReturn(true);
        $this->limit1->shouldReceive('hit')
            ->once()
            ->with(1)
            ->andReturn(true);
        $this->limit1->shouldReceive('isValid')
            ->once()
            ->andReturn(true);
        $this->limit1->shouldReceive('getRegion')
            ->times(5)
            ->andReturn('eu', 'kr', 'na', 'asia', 'ru');
        $this->limit1->shouldReceive('newInstance')
            ->times(5)
            ->andReturn($this->limit1);
        $this->client->shouldReceive('baseUrl')
            ->once();
        $this->client->shouldReceive('request')
            ->with('encyclopedia/info/', [
                'application_id' => 'id',
            ])->once()
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200));

        $api = new WotWrap\Api($this->applicationIds, $this->client);
        $api->limit(10, 5, 'all', $this->limit1);
        $encyclopedia = $api->encyclopedia();
        $info = $encyclopedia->info();
        $this->assertTrue(is_array($info->getInfo()));
    }
}
