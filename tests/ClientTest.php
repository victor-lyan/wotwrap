<?php

use Mockery as m;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }
    
    /*public function testRequest()
    {
        $response = new Guzzle\Http\Message\Response(200, ['X-Foo' => 'Bar']);
        $response->setBody(GuzzleHttp\Stream\Stream::factory('foo'));
        $mock = new GuzzleHttp\Subscriber\Mock([
            $response,
        ]);
    }*/

    /**
     * @expectedException WotWrap\Exception\BaseUrlException
     */
    public function testRequestNoBaseUrl()
    {
        $client = new WotWrap\Client;
        $client->request('');
    }
}
