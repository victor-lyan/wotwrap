<?php
namespace WotWrap\Api;

use WotWrap\Exception\RequiredParametersMissingException;
use WotWrap\Dto;

class Account extends AbstractApi
{
    /**
     * The accounts we have loaded
     * 
     * @var array
     */
    protected $accounts = [];

    /**
     * Searches accounts for the given name
     * 
     * @param $name
     * @param array $params
     * @return array
     * @throws RequiredParametersMissingException
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function findByName($name, $params = [])
    {
        if (!is_string($name)) {
            throw new RequiredParametersMissingException("Required parameter 'name' is missing.");
        }
        
        $params['search'] = htmlspecialchars($name);
        
        $array = $this->request('account/list/', $params);
        $accounts = [];
        foreach ($array['data'] as $account) {
            $accounts[] = new Dto\Account($account);
        }
        
        return $accounts;
    }

    /**
     * Retrieves accounts personal information
     * 
     * @param $identities
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function info($identities, $params = [])
    {
        $accountIds = $this->extractIds($identities);
        $accountIds = implode(',', $accountIds);
        
        $params['account_id'] = $accountIds;
        
        $array = $this->request('account/info/', $params);
        $accounts = [];
        foreach ($array['data'] as $account_id => $info) {
            $accounts[$account_id] = new Dto\Account($info);
        }
        
        return $accounts;
    }

    /**
     * Retrieves per tank statistics for the given identities
     * 
     * @param $identities
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function tanks($identities, $params = [])
    {
        $accountIds = $this->extractIds($identities);
        $accountIds = implode(',', $accountIds);

        $params['account_id'] = $accountIds;

        $array = $this->request('account/tanks/', $params);
        $accounts = [];
        foreach ($array['data'] as $account_id => $info) {
            $newInfo = [];
            foreach ($info as $key => $i) {
                $newInfo[$i['tank_id']] = [
                    'wins' => $i['statistics']['wins'],
                    'battles' => $i['statistics']['battles'],
                    'mark_of_mastery' => $i['mark_of_mastery'],
                ];
            }
            $accounts[$account_id] = new Dto\Account($newInfo);
        }

        return $accounts;
    }

    /**
     * Retrieves information about achievements for the given identities
     * 
     * @param $identities
     * @param array $params
     * @return array
     * @throws \WotWrap\Exception\CacheNotFoundException
     */
    public function achievements($identities, $params = [])
    {
        $accountIds = $this->extractIds($identities);
        $accountIds = implode(',', $accountIds);

        $params['account_id'] = $accountIds;

        $array = $this->request('account/achievements/', $params);
        $accounts = [];
        foreach ($array['data'] as $account_id => $info) {
            $accounts[$account_id] = new Dto\Account($info);
        }
        
        return $accounts;
    }
}
