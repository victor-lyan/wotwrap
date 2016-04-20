<?php
namespace WotWrap\Limit;

use Memcached;

class Limit implements LimitInterface
{
    /**
     * The key that will be used for the memcached storage.
     *
     * @var string
     */
    protected $key;

    /**
     * The max amount of hits the key can take in the given amount
     * of seconds.
     *
     * @var int
     */
    protected $hits;

    /**
     * The amount of seconds to let the hits accumulate for.
     *
     * @var int
     */
    protected $seconds;

    /**
     * The region that is attached to this limit counter.
     *
     * @var string
     */
    protected $region;

    /**
     * The memcached instance.
     *
     * @var Memcached
     */
    protected $memcached;

    /**
     * Can we load memcached?
     *
     * @var bool
     */
    protected $valid = false;
    
    public function __construct()
    {
        if (class_exists('Memcached')) {
            $this->memcached = new Memcached;
            $this->memcached->addServer('localhost', 11211, 100);
            $this->valid = true;
        }
    }

    /**
     * @return static
     */
    public function newInstance()
    {
        return new static();
    }

    /**
     * @param int $hits
     * @param int $seconds
     * @param string $region
     * @return $this
     */
    public function setRate($hits, $seconds, $region)
    {
        $this->key = "WotWrap.hits.$region.$hits.$seconds";
        $this->hits = (int) $hits;
        $this->seconds = (int) $seconds;
        $this->region = $region;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param int $count
     * @return bool
     */
    public function hit($count = 1)
    {
        $hitsLeft = $this->memcached->get($this->key);
        if ($this->memcached->getResultCode() == Memcached::RES_NOTFOUND) {
            //this is the first hit
            $hitsLeft = $this->hits;
            $this->memcached->set($this->key, $this->hits, time() + $this->seconds);
        }
        
        if ($hitsLeft < $count) {
            return false;
        }
        
        if ($this->memcached->decrement($this->key, $count) === false) {
            //it failed to decrement
            return false;
        }
        
        return true;
    }

    /**
     * @return int|mixed
     */
    public function remaining()
    {
        $hitsLeft = $this->memcached->get($this->key);
        if ($this->memcached->getResultCode() == Memcached::RES_NOTFOUND) {
            //this is the first hit
            $hitsLeft = $this->hits;
        }
        
        return $hitsLeft;
    }

    /**
     * @return bool
     */
    public function isValid()
    {   
        return $this->valid;
    }
}
