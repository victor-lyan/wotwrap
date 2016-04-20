<?php
namespace WotWrap\Api;

use WotWrap\Dto;

class Tanks extends AbstractApi
{
    /**
     * Retrieves per tank statistics
     * 
     * @param $identity
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     * @throws \WotWrap\Exception\InvalidIdentityException
     */
    public function stats($identity, $params = [])
    {
        $accountId = $this->extractId($identity);
        $params['account_id'] = $accountId;

        $array = $this->request('tanks/stats/', $params);
        $stats = [];
        foreach ($array['data'][$accountId] as $info) {
            $stats[] = new Dto\Tanks($info);
        }

        return $stats;
    }

    /**
     * Retrieves per tank achievements
     * 
     * @param $identity
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     * @throws \WotWrap\Exception\InvalidIdentityException
     */
    public function achievements($identity, $params = [])
    {
        $accountId = $this->extractId($identity);
        $params['account_id'] = $accountId;

        $array = $this->request('tanks/achievements/', $params);
        $achievements = [];
        foreach ($array['data'][$accountId] as $info) {
            $achievements[] = new Dto\Tanks($info);
        }

        return $achievements;
    }
}
