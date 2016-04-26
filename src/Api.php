<?php
namespace WotWrap;

use WotWrap\Exception\NoValidLimitInterfaceException;
use WotWrap\Limit\Limit;
use WotWrap\Api\AbstractApi;
use WotWrap\Limit\Collection;
use WotWrap\Limit\LimitInterface;
use WotWrap\Exception\ApiClassNotFoundException;
use WotWrap\Exception\NoIdException;
use WotWrap\Exception\InvalidRegionException;


/**
 * @method \WotWrap\Api\Tanks tanks()
 * @method \WotWrap\Api\Account account()
 * @method \WotWrap\Api\Auth auth()
 * @method \WotWrap\Api\Encyclopedia encyclopedia()
 */
class Api {

	/**
	 * The region to be used. Defaults to 'ru'.
	 *
	 * @var string
	 */
	protected $region = 'ru';

	/**
	 * The client used to connect with the WOT API.
	 *
	 * @var object
	 */
	protected $client;

	/**
	 * The amount of seconds we will wait for a response from the wot
	 * server. 0 means wait indefinitely.
	 */
	protected $timeout = 0;

	/**
	 * This contains the cache container that we intend to use.
	 *
	 * @var CacheInterface
	 */
	protected $cache;

	/**
	 * Only check the cache. Do not do any actual request.
	 *
	 * @var bool
	 */
	protected $cacheOnly = false;

	/**
	 * How long, in seconds, should we remember a query's response.
	 *
	 * @var int
	 */
	protected $remember = null;

	/**
	 * The collection of limits to be used for all requests in this api.
	 *
	 * @var Collection
	 */
	protected $limits = null;

	/**
	 * This is the application id, very important.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Protocol to be used in the queries (http or https)
	 * 
	 * @var string
	 */
	private $protocol;

	/**
	 * Initiate the default client and application id.
	 *
	 * @param string $id
	 * @param ClientInterface $client
	 * @param string $protocol
	 * @throws NoIdException
	 */
	public function __construct($id = null, ClientInterface $client = null, $protocol = 'https')
	{
		if (is_null($id)) {
			throw new NoIdException('We need an application id... it\'s very important!');
		}

		if (is_null($client)) {
			// set up the default client
			$client = new Client;
		}
		$this->client = $client;

		$this->id = $id;
		
		$this->protocol = $protocol;

		// set up the limit collection
		$this->collection = new Collection;
	}

	/**
	 * This is the primary service locator if you utilize the api (which you should) to
	 * load instance of the abstractApi. This is the method that is called when you attempt
	 * to invoke "Account()", "Tank()", etc.
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return AbstractApi
	 * @throws ApiClassNotFoundException
	 */
	public function __call($method, $arguments)
	{
		// we don't use the arguments at the moment.
		unset($arguments);

		$className = 'WotWrap\\Api\\'.ucwords(strtolower($method));
		if (!class_exists($className)) {
			// This class does not exist
			throw new ApiClassNotFoundException('The api class "'.$className.'" was not found.');
		}
		$api = new $className($this->client, $this->collection, $this);
		if (!$api instanceof AbstractApi) {
			// This is not an api class.
			throw new ApiClassNotFoundException('The api class "'.$className.'" was not found.');
		}

		$api->setId($this->id)
				->setRegion($this->region, $this->protocol)
				->setTimeout($this->timeout)
				->setCacheOnly($this->cacheOnly);

		if ($this->cache instanceof CacheInterface) {
			$api->remember($this->remember, $this->cache);
		}

		return $api;
	}

	/**
	 * Set the region to be used in the requests
	 *
	 * @param string $region
	 * @return $this
	 * @throws InvalidRegionException
	 * @chainable
	 */
	public function setRegion($region)
	{
		if (!in_array($region, ['ru', 'na', 'eu', 'kr', 'asia'])) {
			throw new InvalidRegionException("Invalid region, please use one of these: ru, na, eu, kr, asia.");
		}
		$this->region = $region;
		return $this;
	}

	/**
	 * Set a timeout in seconds for how long we will wait for the server
	 * to respond. If the server does not respond within the set number
	 * of seconds we throw an exception.
	 *
	 * @param float $seconds
	 * @return $this
	 * @chainable
	 */
	public function setTimeout($seconds)
	{
		$this->timeout = floatval($seconds);
		return $this;
	}

	/**
	 * Sets the api endpoint to only use the cache to get the needed
	 * information for the requests.
	 *
	 * @param $cacheOnly bool
	 * @return $this
	 * @chainable
	 */
	public function setCacheOnly($cacheOnly = true)
	{
		$this->cacheOnly = $cacheOnly;
		return $this;
	}

	/**
	 * Sets the amount of seconds we should remember the response for.
	 * Leave it empty (or null) if you want to use the default set for
	 * each api request.
	 *
	 * @param int $seconds
	 * @param CacheInterface $cache
	 * @return $this
	 * @chainable
	 */
	public function remember($seconds = null, CacheInterface $cache = null)
	{
		if (is_null($cache)) {
			// use the built in cache interface
			$cache = new Cache;
		}
		$this->cache    = $cache;
		$this->remember = $seconds;
		return $this;
	}

	/**
	 * Sets a limit to be added to the collection.
	 *
	 * @param int $hits
	 * @param int $seconds
	 * @param string $region
	 * @param LimitInterface $limit
	 * @throws NoValidLimitInterfaceException
	 * @return $this
	 * @chainable
	 */
	public function limit($hits, $seconds, $region = 'all', LimitInterface $limit = null)
	{
		if (is_null($limit)) {
			// use the built in limit interface
			$limit = new Limit;
		}
		if (!$limit->isValid()) {
			throw new NoValidLimitInterfaceException("We could not load a valid limit interface.");
		}

		if ($region == 'all') {
			foreach (['ru', 'eu', 'na', 'kr', 'asia',] as $region) {
				$newLimit = $limit->newInstance();
				$newLimit->setRate($hits, $seconds, $region);
				$this->collection->addLimit($newLimit);
			}
		} else {
			// lower case the region
			$region = strtolower($region);
			$limit->setRate($hits, $seconds, $region);
			$this->collection->addLimit($limit);
		}

		return $this;
	}

	/**
	 * @return array of Limit
	 */
	public function getLimits()
	{
		return $this->collection->getLimits();
	}
}
