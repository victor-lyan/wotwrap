<?php

use Mockery as m;
use WotWrap\Api;

class AuthTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected $applicationIds;
    
    public function setUp()
    {
        $this->applicationIds = ['ru' => 'id'];
        $this->client = m::mock('WotWrap\Client');
    }
    
    public function tearDown()
    {
        m::close();
    }
    
    public function testLogin()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('request')
            ->with('auth/login/', [
                'application_id' => 'id',
                'nofollow' => 1,
                'redirect_uri' => 'http://example.com' 
            ])
            ->once()
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/auth.login.json'), 200));
        
        $api = new Api($this->applicationIds, $this->client);
        $loginResult = $api->auth()->login('http://example.com');
        $this->assertContains('ru.wargaming.net', $loginResult);
    }
}
