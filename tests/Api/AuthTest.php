<?php

use Mockery as m;
use WotWrap\Api;

class AuthTest extends PHPUnit_Framework_TestCase
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
    
    public function testLogin()
    {
        $this->client->shouldReceive('baseUrl')->once();
        $this->client->shouldReceive('setRequestType')->once()->with('POST');
        $this->client->shouldReceive('request')
            ->with('auth/prolongate/', [
                'application_id' => 'id', 
                'access_token' => 'c91999c155b495a694463b9eca7d1c99ea34cba0', 
                'expires_at' => 1462888073
            ])
            ->once()
            ->andReturn(new WotWrap\Response(file_get_contents('tests/json/auth.login.json'), 200));
        
        $_GET['access_token'] = 'c91999c155b495a694463b9eca7d1c99ea34cba0';
        $_GET['expires_at'] = 1462888073;
        $api = new Api('id', $this->client);
        $loginResult = $api->auth()->login('/test');
        $this->assertEquals(11782434, $loginResult->account_id);
    }
}
