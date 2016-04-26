<?php

use WotWrap\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testSetCode()
    {
        $response = new Response('{"status": "ok"}', 200);
        $this->assertEquals(200, $response->getCode());
    }
    
    public function testErrorInDecodedContent()
    {
        $response = new Response(file_get_contents('tests/json/error.account_id_invalid.json'), 200);
        $error = $response->getErrorArray();
        $this->assertEquals("INVALID_ACCOUNT_ID", $error['message']);
    }
}
