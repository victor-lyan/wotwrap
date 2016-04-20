<?php

use WotWrap\Region;

class RegionTest extends PHPUnit_Framework_TestCase
{
    public function testGetDomain()
    {
        $region = new Region('eu', 'https');
        $this->assertEquals('https://api.worldoftanks.eu/wot/', $region->getDomain());
    }
}
