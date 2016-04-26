<?php
namespace WotWrap\Api;

use WotWrap\Region;
use WotWrap\ClientInterface;
use WotWrap\Limit\Collection;
use WotWrap\Cache;
use WotWrap\CacheInterface;
use WotWrap\Api;
use WotWrap\Response;
use WotWrap\Exception\CacheNotFoundException;
use WotWrap\Exception\LimitReachedException;
use WotWrap\Exception\InvalidIdentityException;
use WotWrap\Dto\Account;
/*
use WotWrap\Dto\AbstractDto;

use WotWrap\Exception\RegionException;
*/

abstract class AbstractApi {

	/**
	 * The client used to communicate with the api.
	 *
	 * @var ClientInterface
	 */
	protected $client;

	/**
	 * The collection of limits to be used on this api.
	 *
	 * @var Collection
	 */
	protected $collection;

	/**
	 * The application id to be used by the api.
	 *
	 * @param string
	 */
	protected $id;

	/**
	 * The region to be used by the api.
	 *
	 * @var Region
	 */
	protected $region;

	/**
	 * Provides access to the api object to perform requests on
	 * different api endpoints.
	 */
	protected $api;

	/**
	 * A list of all permitted regions for this API call. Leave
	 * it empty to not lock out any region string.
	 *
	 * @param array
	 */
	protected $permittedRegions = [];

	/**
	 * List of http error response codes and associated error
	 * message with each code.
	 *
	 * @param array
	 */
	protected $responseErrors = [
		'400' => 'Bad request.',
		'401' => 'Unauthorized.',
		'403' => 'Forbidden.',
		'404' => 'Resource not found.',
		'429' => 'Rate limit exceeded.',
		'500' => 'Internal server error.',
		'503' => 'Service unavailable.',
	];

	/**
	 * A count of the amount of API request this object has done
	 * so far.
	 *
	 * @param int
	 */
	protected $requests = 0;

	/**
	 * The amount of seconds we will wait for a responde fromm the riot
	 * server. 0 means wait indefinitely.
	 */
	protected $timeout = 0;

	/**
	 * This is the cache container that we intend to use.
	 *
	 * @var CacheInterface
	 */
	protected $cache = null;

	/**
	 * Only check the cache. Do not do any actual request.
	 *
	 * @var bool
	 */
	protected $cacheOnly = false;

	/**
	 * The amount of time we intend to remember the response for.
	 *
	 * @var int
	 */
	protected $defaultRemember = 0;

	/**
	 * The amount of seconds to keep things in cache
	 *
	 * @var int
	 */
	protected $seconds = 0;

	/**
	 * Default DI constructor.
	 *
	 * @param ClientInterface $client
	 * @param Collection $collection
	 * @param Api $api
	 */
	public function __construct(ClientInterface $client, Collection $collection, Api $api)
	{
		$this->client     = $client;
		$this->collection = $collection;
		$this->api        = $api;
	}

	/**
	 * Returns the amount of requests this object has done
	 * to the api so far.
	 *
	 * @return int
	 */
	public function getRequestCount()
	{
		return $this->requests;
	}

	/**
	 * Set the application id to be used in the api.
	 *
	 * @param string $id
	 * @return $this
	 * @chainable
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Set the region to be used in the api.
	 *
	 * @param string $region
	 * @param string $protocol
	 * @return $this
	 * @chainable
	 */
	public function setRegion($region, $protocol)
	{
		$this->region = new Region($region, $protocol);
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
		$this->cache = $cache;
		if (is_null($seconds)) {
			$this->seconds = $this->defaultRemember;
		} else {
			$this->seconds = $seconds;
		}

		return $this;
	}

	/**
	 * Sets request type (GET OR POST)
	 * 
	 * @param $type
	 * @return $this
	 * @chainable
	 */
	public function setRequestType($type)
	{
		$this->client->setRequestType($type);
		return $this;
	}

	/**
	 * Wraps the request of the api in this method.
	 *
	 * @param string $url
	 * @param array $params
	 * @throws CacheNotFoundException
	 * @return array
	 */
	protected function request($url, $params = [])
	{
		$this->client->baseUrl($this->region->getDomain());

		if ($this->timeout > 0) {
			$this->client->setTimeout($this->timeout);
		}

		// add the application id to the param list
		$params['application_id'] = $this->id;

		// check cache
		if ($this->cache instanceof CacheInterface) {
			$cacheKey = md5($url.'?'.http_build_query($params));
			if ($this->cache->has($cacheKey)) {
				$content = $this->cache->get($cacheKey);
			} elseif ($this->cacheOnly)	{
				throw new CacheNotFoundException("A cache item for '$url?".http_build_query($params)."' was not found!");
			} else {
				$content = $this->clientRequest($url, $params);

				// we want to cache this response
				$this->cache->set($cacheKey, $content, $this->seconds);
			}
		} elseif ($this->cacheOnly)	{
			throw new CacheNotFoundException('The cache is not enabled but we were told to use only the cache!');
		} else {
			$content = $this->clientRequest($url, $params);
		}

		return $content->getDecodedContent();
	}

	/**
	 * Make the actual request.
	 *
	 * @param string $url
	 * @param array $params
	 * @return Response
	 * @throws LimitReachedException
	 */
	protected function clientRequest($url, $params)
	{
		// check if we have hit the limit
		if (!$this->collection->hitLimits($this->region->getRegion())) {
			throw new LimitReachedException('You have hit the request limit in your collection.');
		}
		$response = $this->client->request($url, $params);
		// check if it's a valid response object
		if ($response instanceof Response) {
			$this->checkResponseErrors($response);
		}

		// request was succesful
		++$this->requests;

		return $response;
	}

	/**
	 * Attempts to extract an ID from the object/value given
	 *
	 * @param mixed $identity
	 * @return int
	 * @throws InvalidIdentityException
	 */
	protected function extractId($identity)
	{
		if ($identity instanceof Account) {
			return $identity->account_id;
		} elseif (filter_var($identity, FILTER_VALIDATE_INT) !== false) {
			return $identity;
		} else {
			throw new InvalidIdentityException("The identity '$identity' is not valid.");
		}
	}

	/**
	 * Attempts to extract IDs from the given array
	 *
	 * @param mixed $identities
	 * @return array
	 * @uses extractId()
	 */
	protected function extractIds($identities)
	{
		$ids = [];
		if (is_array($identities)) {
			foreach ($identities as $identity) {
				$ids[] = $this->extractId($identity);
			}
		} else {
			$ids[] = $this->extractId($identities);
		}

		return $ids;
	}

	/**
	 * Checks the given response object for errors
	 * @param Response $response
	 */
	protected function checkResponseErrors(Response $response)
	{
		$code = $response->getCode();
		if (intval($code/100) != 2) {
			//we have an http error!
			$message = "Http Error.";
			if (isset($this->responseErrors[$code])) {
				$message = trim($this->responseErrors[$code]);
			}

			$class = "WotWrap\Response\Http$code";
			throw new $class($message, $code);
		}
		
		$apiErrorArray = $response->getErrorArray(); 
		if (!empty($apiErrorArray)) {
			//we have a WOT API error
			$message = $apiErrorArray['message'];
			$errorCode = $apiErrorArray['code'];
			
			$class = "WotWrap\Response\Api$errorCode";
			throw new $class($message, $errorCode);
		}
	}
}
