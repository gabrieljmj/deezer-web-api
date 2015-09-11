<?php
/**
 * DeezerWebApi
 * 
 * @author GabrielJMJ
 * @license MIT
 */

namespace DeezerWebApi;

class AccessToken
{
    /**
     * Access token
     * 
     * @var string
     */
    private $accessToken;
    
    /**
     * Time that access token expires 
     * 
     * @var integer
     */
    private $expires;
    
    /**
     * @param string  $accessToken
     * @param integer $expires
     */
    public function __construct($accessToken, $expires)
    {
        $this->accessToken = $accessToken;
        $this->expires = $expires;
    }
    
    /**
     * Returns the access token 
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
    
    /**
     * Returns the access token 
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getAccessToken();
    }
    
    /**
     * Returns the expires time of access token
     * 
     * @return \DateTime
     */
    public function expiresAt()
    {
        return new \DateTime('@' . $this->expires);
    }
}