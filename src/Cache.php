<?php
namespace WotWrap;

use Memcached;

class Cache implements CacheInterface
{
    /**
     * @var Memcached
     */
    protected $memcached;
    
    public function __construct()
    {
        $this->memcached = new Memcached();
        $this->memcached->addServer('localhost', 11211, 100);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     */
    public function set($key, $value, $seconds)
    {
        $result = $this->memcached->set($key, $value, time() + $seconds); 
        return $result;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $this->memcached->get($key);
        return ($this->memcached->getResultCode() == Memcached::RES_NOTFOUND) ? false : true;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->memcached->get($key);
    }
}
