<?php
namespace WotWrap\Api;

use WotWrap\Dto;
use WotWrap\Exception\RequiredParametersMissingException;

class Encyclopedia extends AbstractApi
{
    /**
     * Retrieves tanks information
     * The amount of data retrieved is big, so it's strongly advised
     * to limit it by passing $params
     * 
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function vehicles($params = [])
    {
        $array = $this->request('encyclopedia/vehicles/', $params);
        $vehicles = [];
        foreach ($array['data'] as $tankId => $info) {
            $vehicles[$tankId] = new Dto\Encyclopedia($info);
        }
        
        return $vehicles;
    }

    /**
     * Retrieves technical information about the tank
     * 
     * @param $tankId
     * @param array $params
     * @return array
     * @throws RequiredParametersMissingException
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function vehicleprofile($tankId, $params = [])
    {
        if (!is_int($tankId)) {
            throw new RequiredParametersMissingException("Required parameter 'tankId' is missing.");
        }
        
        $params['tank_id'] = $tankId;
        $array = $this->request('encyclopedia/vehicleprofile/', $params);
        $profile = [];
        foreach ($array['data'] as $tank_id => $info) {
            $profile[$tank_id] = new Dto\Encyclopedia($info);
        }
        
        return $profile;
    }

    /**
     * Retrieves information about the achievements
     * 
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function achievements($params = [])
    {
        $array = $this->request('encyclopedia/achievements/', $params);
        $achievements = [];
        foreach ($array['data'] as $achievement => $info) {
            $achievements[$achievement] = new Dto\Encyclopedia($info);
        }
        
        return $achievements;
    }

    /**
     * Retrieves information about encyclopedia itself
     * 
     * @param array $params
     * @return Dto\Encyclopedia
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function info($params = [])
    {
        $info = $this->request('encyclopedia/info/', $params);
        return new Dto\Encyclopedia($info['data']);
    }

    /**
     * Retrieves information about game maps
     * 
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function arenas($params = [])
    {
        $array = $this->request('encyclopedia/arenas/', $params);
        $arenas = [];
        foreach ($array['data'] as $arena => $info) {
            $arenas[$arena] = new Dto\Encyclopedia($info);
        }
        
        return $arenas;
    }

    /**
     * Retrieves information about provisions
     * 
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function provisions($params = [])
    {
        $array = $this->request('encyclopedia/provisions/', $params);
        $provisions = [];
        foreach ($array['data'] as $provision_id => $info) {
            $provisions[$provision_id] = new Dto\Encyclopedia($info);
        }

        return $provisions;
    }

    /**
     * Retrieves information about boosters
     * 
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function boosters($params = [])
    {
        $array = $this->request('encyclopedia/boosters/', $params);
        $boosters = [];
        foreach ($array['data'] as $booster_id => $info) {
            $boosters[$booster_id] = new Dto\Encyclopedia($info);
        }

        return $boosters;
    }
}
