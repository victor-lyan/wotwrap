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
}
