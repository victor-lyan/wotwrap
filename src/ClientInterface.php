<?php
namespace WotWrap;

interface ClientInterface
{
    /**
     * Set the request type (GET or POST)
     * 
     * @param $type
     * @return mixed
     */
    public function setRequestType($type);
    
    /**
     * Set the base url for all future requests.
     *
     * @param string $url
     * @return void
     */
    public function baseUrl($url);

    /**
     * Set a timeout in seconds for how long we will wait for the server
     * to respond. If the server does not respond within the set number
     * of seconds we throw an exception.
     *
     * @param int $seconds
     * @return void
     */
    public function setTimeout($seconds);

    /**
     * Attempts to make a request of the given path with any
     * additional parameters. It should return the response as
     * an LeagueWrap\Response object.
     *
     * @param string $url
     * @param array $params
     * @return Response
     */
    public function request($url, array $params);
}
