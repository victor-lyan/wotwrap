<?php
namespace WotWrap\Api;

use WotWrap\Dto;
use WotWrap\Exception\MultipleAccountIdException;
use WotWrap\Exception\ParameterTypeMismatchException;

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
     * @throws MultipleAccountIdException
     * @throws ParameterTypeMismatchException
     */
    public function stats($identity, $params = [])
    {
        if (!is_array($identity) && !is_int($identity) && !(is_string($identity) && ctype_digit($identity))) {
            throw new ParameterTypeMismatchException("Parameter 'identities' type mismatch.");
        }        
        if (is_array($identity)) {
            if (count($identity) > 1) {
                throw new MultipleAccountIdException("Method 'stats' expects an identity with single account_id");
            } else {
                $identity = $identity[0];
            }
        }
        
        $accountId = $this->extractId($identity);
        $params['account_id'] = $accountId;

        $array = $this->request('tanks/stats/', $params);
        $stats = [];
        foreach ($array['data'][$accountId] as $info) {
            $stats[$info['tank_id']] = new Dto\Tanks($info);
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
     * @throws MultipleAccountIdException
     * @throws ParameterTypeMismatchException
     */
    public function achievements($identity, $params = [])
    {
        if (!is_array($identity) && !is_int($identity) && !(is_string($identity) && ctype_digit($identity))) {
            throw new ParameterTypeMismatchException("Parameter 'identities' type mismatch.");
        }
        if (is_array($identity)) {
            if (count($identity) > 1) {
                throw new MultipleAccountIdException("Method 'achievements' expects an identity with single account_id");
            } else {
                $identity = $identity[0];
            }
        }
        
        $accountId = $this->extractId($identity);
        $params['account_id'] = $accountId;

        $array = $this->request('tanks/achievements/', $params);
        $achievements = [];
        foreach ($array['data'][$accountId] as $info) {
            $achievements[$info['tank_id']] = new Dto\Tanks($info);
        }

        return $achievements;
    }
}
