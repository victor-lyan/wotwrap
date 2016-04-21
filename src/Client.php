<?php
namespace WotWrap;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\HandlerStack;
use WotWrap\Exception\BaseUrlException;
use WotWrap\Response;

class Client implements ClientInterface
{
    /**
     * @var Guzzle
     */
    protected $guzzle;
    
    protected $timeout = 0;
    
    private $requestType = 'GET';

    /**
     * @param $type
     * @return void
     */
    public function setRequestType($type)
    {
        $this->requestType = strtoupper($type);
    }

    /**
     * @param string $url
     * @return void
     */
    public function baseUrl($url)
    {
        $this->guzzle = new Guzzle([
            'base_uri' => $url,
            'defaults' => ['headers' => ['Accept-Encoding' => 'gzip,deflate']]
        ]);
    }

    /**
     * @param int $seconds
     * @return void
     */
    public function setTimeout($seconds)
    {
        $this->timeout = floatval($seconds);
    }
    
    public function addMock($mock)
    {
        $handler = HandlerStack::create($mock);
        $this->guzzle = new Guzzle(['handler' => $handler]);
    }

    /**
     * @param string $url
     * @param array $params
     * @return Response
     * @throws BaseUrlException
     */
    public function request($url, array $params = [])
    {
        if (!$this->guzzle instanceof Guzzle) {
            throw new BaseUrlException('BaseUrl was not set. Please call baseUrl($url).');
        }
        
        if ($this->requestType == 'GET') {
            $uri = $url.'?'.http_build_query($params);
            $response = $this->guzzle->get($uri, ['connect_timeout' => $this->timeout, 'exceptions' => false]);
        } else {
            //POST method
            $response = $this->guzzle->post($url, [
                'form_params' => $params,
                'connect_timeout' => $this->timeout,
                'exceptions' => false
            ]);
        }

        $body = $response->getBody();
        $code = $response->getStatusCode();
        if (is_object($body)) {
            //TODO: don't know whether it is needed in guzzle 6.0
            //$body->seek(0);
            $content = $body->read($body->getSize());
        } else {
            // no content
            $content = '';
        }
        
        $response = new Response($content, $code);

        return $response;
    }
}
