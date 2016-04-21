<?php

use WotWrap\Limit\Limit;
use WotWrap\Limit\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('memcached')) {
            $this->markTestSkipped('The memcached extension is not available.');
        }
        $memcached = new Memcached;
        if (!$memcached->addServer('localhost', 11211, 100)) {
            $this->markTestSkipped('Could not connect to localhost server 11211.');
        }
        if (!$memcached->flush()) {
            $code = $memcached->getResultCode();
            $this->markTestSkipped('Could not flush memcached (code #'.$code.')');
        }
    }
    
    public function testHitLimits()
    {
        $limit = new Limit;
        $limit->setRate(10, 2, 'ru');
        $collection = new Collection;
        $collection->addLimit($limit);
        $hit = $collection->hitLimits('ru', 10);
        $this->assertTrue($hit);
    }

    public function testHitLimitsFail()
    {
        $limit = new Limit;
        $limit->setRate(10, 2, 'ru');
        $collection = new Collection;
        $collection->addLimit($limit);
        $hit = $collection->hitLimits('ru', 11);
        $this->assertFalse($hit);
    }

    public function testHitLimitsMultiple()
    {
        $limit1 = new Limit;
        $limit1->setRate(9, 2, 'na');
        $limit2 = new Limit;
        $limit2->setRate(5, 2, 'na');
        $collection = new Collection;
        $collection->addLimit($limit1);
        $collection->addLimit($limit2);
        $hit = $collection->hitLimits('na');
        $this->assertTrue($hit);
    }
}
