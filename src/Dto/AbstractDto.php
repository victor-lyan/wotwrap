<?php
namespace WotWrap\Dto;

abstract class AbstractDto
{
    protected $info;
    
    public function __construct($info)
    {
        $this->info = $info;
    }

    /**
     * Gets the attribute of this Dto
     * 
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Sets a new attribute for this Dto.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Gets the attribute of this Dto.
     *
     * @param string $key
     * @return string|null
     */
    public function get($key)
    {
        if (isset($this->info[$key])) {
            return $this->info[$key];
        }
        return null;
    }

    /**
     * Sets a new attribute for this Dto.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @chainable
     */
    public function set($key, $value)
    {
        $this->info[$key] = $value;
        return $this;
    }

    /**
     * Returns info array
     * 
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }
}
