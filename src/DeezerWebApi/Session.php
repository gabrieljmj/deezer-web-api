<?php
/**
 * DeezerWebApi
 * 
 * @author GabrielJMJ
 * @license MIT
 */
 
namespace DeezerWebApi;

use GuzzleHttp\Client;
use GuuzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Request;
use DeezerWebApi\AccessToken;

class Session
{
    const OAUTH_URL = 'https://connect.deezer.com/oauth';
    
    /**
     * APP ID from Deezer API
     * 
     * @var integer
     */
    private $appId;
    
    /**
     * APP Secret from Deezer API
     * 
     * @var string
     */
    private $secret;
    
    /**
     * Redirect URI for call Deezer API
     * 
     * @var string
     */
    private $redirectUri;
    
    /**
     * Authentication code from Deezer API
     * 
     * @var string
     */
    private $code;
    
    /**
     * State to avoid CSRF
     *
     * @var string
     */
    private $state;
    
    /**
     * Access token
     * 
     * @var \DeezerWebApi\AccessToken
     */
    private $accessToken;
    
    /**
     * HTTP client
     * 
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;
    
    /**
     * @param integer                          $appId
     * @param string                           $secret
     * @param string                           $redirectUri
     * @param \GuzzleHttp\ClientInterface|null $client
     */
    public function __construct($appId, $secret, $redirectUri, ClientInterface $client = null)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->client = $client ?: new Client();
    }
    
    /**
     * Sets the authentication code
     * 
     * @param string $code
     * 
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    
    /**
     * Returns the authentication code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Sets the state against CSRF
     *
     * @param string $state
     * 
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }
    
    /**
     * Returns the state agains CSRF
     * 
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * Returns the APP ID from Deezer API
     * 
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }
    
    /**
     * Returns the APP Secret from Deezer API
     * 
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }
    
    /**
     * Retruns the redirect URI for call Deezer API
     * 
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }
    
    /**
     * Call Deezer API to get a valid access token
     * 
     * @param string $code
     * 
     * @return \DeezerWebApi\AccessToken
     */
    public function getAccessToken($code)
    {
        if (!$this->accessToken) {
            $params = [
                'app_id' => $this->getAppId(),
                'secret' => $this->getSecret(),
                'code' => $code
            ];
            $url = self::OAUTH_URL . '/access_token.php?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $data = null;
            parse_str($response->getBody(), $data);

            $this->accessToken = new AccessToken($data['access_token'], $data['expires']);
        }
        
        return $this->accessToken;
    }
    
    /**
     * Calls Deezer API to get an authentication URI
     * 
     * @param array $perms
     * 
     * @return string
     */
    public function getAuthUri(array $perms = []) {
        $params = [
            'app_id' => $this->getAppId(),
            'secret' => $this->getSecret(),
            'redirect_uri' => $this->getRedirectUri(),
            'perms' => implode(',', $perms),
            'state' => $this->state
        ];
        
        $uri = self::OAUTH_URL . '/auth.php?' . http_build_query($params);
        
        return $uri;
    }
}