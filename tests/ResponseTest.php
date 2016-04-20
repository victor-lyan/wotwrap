<?php


class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testSetCode()
    {
        $response = new WotWrap\Response('{"status": "ok"}', 200);
        $this->assertEquals(200, $response->getCode());
    }
}
