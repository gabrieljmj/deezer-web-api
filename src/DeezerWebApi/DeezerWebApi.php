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
     * @param \DeezerWebApi\AccessToken        $token
     * @param \GuzzleHttp\ClientInterface|null $client
     */
    public function __construct(AccessToken $token, ClientInterface $client = null)
    {
        $this->accessToken = $token;
        $this->client = $client ?: new Client();
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
        $params['access_token'] = $this->accessToken->getAccessToken();
        $response = $this->client->request($method, self::API_URL . $resource, ['query' => http_build_query($params)]);
        return json_decode($response->getBody());
    }
}