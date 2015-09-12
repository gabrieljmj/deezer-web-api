<?php
/**
 * DeezerWebApi
 * 
 * @author GabrielJMJ
 * @license MIT
 */

namespace DeezerWebApi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use DeezerWebApi\AccessToken;

class DeezerWebApi
{
    const API_URL = 'https://api.deezer.com';
    
    /**
     * HTTP client
     * 
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;
    
    /**
     * Access token
     * 
     * @var \DeezerWebApi\AccessToken
     */
    private $accessToken;
    
    /**
     * @param \GuzzleHttp\ClientInterface|null $client
     * @param \DeezerWebApi\AccessToken|null   $token
     */
    public function __construct(ClientInterface $client = null, AccessToken $token = null)
    {
        $this->client = $client ?: new Client();
    }
    
    /**
     * Sets the access token
     * 
     * @param \DeezerWebApi\AccessToken
     * 
     * @return self
     */
    public function setAccessToken(AccessToken $token)
    {
        $this->accessToken = $token;
        return $this;
    }
    
    /**
     * Do a GET request to Deezer API and returns the response with an array
     * 
     * @param string $resource
     * @param array  $params
     * 
     * @return array
     */
    public function get($resource, array $params = [])
    {
        return $this->request('GET', $resource, $params);
    }
    
    /**
     * Do a POST request to Deezer API and returns the response with an array
     * 
     * @param string $resource
     * @param array  $params
     * 
     * @return array
     */
    public function post($resource, array $params = [])
    {
        return $this->request('POST', $resource, $params);
    }
    
    /**
     * Do a request to Deezer API and returns the response with an array
     * 
     * @param string $method
     * @param string $resource
     * @param array  $params
     * 
     * @return array
     */
    public function request($method, $resource, array $params = [])
    {
        !$this->accessToken ?: $params['access_token'] = $this->accessToken->getAccessToken();
        $response = $this->client->request($method, self::API_URL . $resource, ['query' => http_build_query($params)]);
        
        return json_decode($response->getBody());
    }
}