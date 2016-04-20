<?php
namespace WotWrap\Limit;

use WotWrap\Limit\LimitInterface;

class Collection
{
    protected $limits = [];

    /**
     * Adds limit object to collection
     * @param \WotWrap\Limit\LimitInterface $limit
     */
    public function addLimit(LimitInterface $limit)
    {
        $this->limits[] = $limit;
    }

    /**
     * Checks whether limit count for the given regions has been exhausted
     * @param $region
     * @param int $count
     * @return bool
     */
    public function hitLimits($region, $count = 1)
    {
        foreach ($this->limits as $limit) {
            if ($limit->getRegion() == $region && !$limit->hit($count)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Returns the lowest
     */
    public function remainingHits()
    {
        $remaining = null;
        foreach ($this->limits as $limit) {
            $hitsLeft = $limit->remaining();
            if (is_null($remaining) || $hitsLeft < $remaining) {
                $remaining = $hitsLeft;
            }
        }

        return $remaining;
    }

    /**
     * @return array of all limits in this collection
     **/
    public function getLimits()
    {
        return $this->limits;
    }
}
