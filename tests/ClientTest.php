<?php

use Mockery as m;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
    
    public function testRequest()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '{"status": "ok"}')
        ]);

        $client = new WotWrap\Client;
        $client->baseUrl('http://google.com');
        $client->setTimeout(10);
        $client->addMock($mock);
        $response = $client->request('test', []);
        $this->assertEquals('ok', $response->getDecodedContent()['status']);
        $this->assertEquals(200, $response->getCode());
    }
    
    public function testRequestPost()
    {
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '{"status": "ok"}')
        ]);

        $client = new WotWrap\Client;
        $client->setRequestType('POST');
        $client->addMock($mock);
        $response = $client->request('test', []);
        $this->assertEquals('ok', $response->getDecodedContent()['status']);
    }

    /**
     * @expectedException WotWrap\Exception\BaseUrlException
     */
    public function testRequestNoBaseUrl()
    {
        $client = new WotWrap\Client;
        $client->request('');
    }
}
