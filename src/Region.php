<?php
namespace WotWrap;

class Region
{
    /**
     * The region that this object represents
     * @var string
     */
    protected $region;

    /**
     * The default domain to attempt to query
     * @var string
     */
    protected $defaultDomain;

    public function __construct($region, $protocol)
    {
        $this->region = strtolower($region);
        $this->defaultDomain = $protocol.'://api.worldoftanks.%s/wot/';
    }

    /**
     * Returns the region that was passed to the constructor
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Returns the domain that this region needs to make its request.
     *
     * @return string
     */
    public function getDomain()
    {
        return sprintf($this->defaultDomain, $this->region);
    }
}
