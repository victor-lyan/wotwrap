<?php
namespace WotWrap\Limit;

interface LimitInterface
{
    /**
     * Returns a new instance of the current limit object.
     *
     * @return static $this
     */
    public function newInstance();

    /**
     * Sets the rate limit for the given region.
     *
     * @param int $hits
     * @param int $seconds
     * @param string $region
     * @chainable
     */
    public function setRate($hits, $seconds, $region);

    /**
     * Returns the region that is tied to this limit counter.
     *
     * @return string
     */
    public function getRegion();

    /**
     * Applies a hit to the given regions rate limiting.
     *
     * @param int $count Default 1 (minimum amount of hits that must be left)
     * @return bool
     */
    public function hit($count = 1);

    /**
     * Check how many hits the given region has remaining.
     *
     * @return int
     */
    public function remaining();

    /**
     * Is the current limit object valid on this machine (i.e. does
     * the machine have memcache).
     *
     * @return bool
     */
    public function isValid();
}
