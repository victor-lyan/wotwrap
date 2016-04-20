<?php

use Mockery as m;

class CacheTest extends PHPUnit_Framework_TestCase
{
    protected $cache;
    protected $client;
    
    public function setUp()
    {
        $this->cache = m::mock('WotWrap\CacheInterface');
        $this->client = m::mock('WotWrap\Client');
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testRememberEncyclopedia()
    {
        $encyclopediaInfo = new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200);
        $this->cache->shouldReceive('set')
            ->once()
            ->with('7a3f2c5eb65314664c6df2fbe0cca658', $encyclopediaInfo, 60)
            ->andReturn(true);
        $this->cache->shouldReceive('has')
            ->twice()
            ->with('7a3f2c5eb65314664c6df2fbe0cca658')
            ->andReturn(false, true);
        $this->cache->shouldReceive('get')
            ->once()
            ->with('7a3f2c5eb65314664c6df2fbe0cca658')
            ->andReturn($encyclopediaInfo);
        
        $this->client->shouldReceive('baseUrl')->twice();
        $this->client->shouldReceive('request')
            ->once()
            ->with('encyclopedia/info/', ['application_id' => 'id'])
            ->andReturn($encyclopediaInfo);
        
        $api = new WotWrap\Api('id', $this->client);
        $encyclopedia = $api->encyclopedia()->remember(60, $this->cache);
        $encyclopedia->info();
        $encyclopedia->info();
        $this->assertEquals(1, $encyclopedia->getRequestCount());
    }
    
    public function testRememberEncyclopediaCacheOnly()
    {
        $encyclopediaInfo = new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200);
        $this->cache->shouldReceive('has')
            ->twice()
            ->with('7a3f2c5eb65314664c6df2fbe0cca658')
            ->andReturn(true);
        $this->cache->shouldReceive('get')
            ->twice()
            ->with('7a3f2c5eb65314664c6df2fbe0cca658')
            ->andReturn($encyclopediaInfo);
        $this->client->shouldReceive('baseUrl')->twice();

        $api = new WotWrap\Api('id', $this->client);
        $encyclopedia = $api->setCacheOnly()->remember(60, $this->cache)->encyclopedia();
        $encyclopedia->info();
        $encyclopedia->info();
        $this->assertEquals(0, $encyclopedia->getRequestCount());
    }

    /**
     * @expectedException WotWrap\Exception\CacheNotFoundException
     */
    public function testRememberSummonerCacheOnlyNoHit()
    {
        $encyclopediaInfo = new WotWrap\Response(file_get_contents('tests/json/encyclopedia.info.json'), 200);
        $this->cache->shouldReceive('has')
            ->once()
            ->with('7a3f2c5eb65314664c6df2fbe0cca658')
            ->andReturn(false);
        $this->client->shouldReceive('baseUrl')
            ->once();

        $api = new WotWrap\Api('id', $this->client);
        $api->remember(null, $this->cache)->setCacheOnly();
        $enc = $api->encyclopedia()->info();
    }
}
