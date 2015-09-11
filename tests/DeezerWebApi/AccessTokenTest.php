<?php
namespace Test\DeezerWebApi;

use DeezerWebApi\AccessToken;

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider accessTokenProvider
     */
    public function testGetterForExpires($token, $expires)
    {
        $accessToken = new AccessToken($token, $expires->getTimestamp());
        
        $this->assertEquals($expires, $accessToken->expiresAt());
    }
    
    /**
     * @dataProvider accessTokenProvider
     */
    public function testGetterForToken($token, $expires)
    {
        $accessToken = new AccessToken($token, $expires->getTimestamp());
        
        $this->assertEquals($token, $accessToken->getAccessToken());
    }
    
    /**
     * @dataProvider accessTokenProvider
     */
    public function testMagicMethod__toStringReturnsAccessToken($token, $expires)
    {
        $accessToken = new AccessToken($token, $expires->getTimestamp());
        
        $this->assertEquals($token, (string) $accessToken);
    }
    
    public function accessTokenProvider()
    {
        $date = new \DateTime();
        $date->modify('+5 minutes');
        return [
            ['nyl9ZFtFMA55f2115f4b13bUNBAkarm55f2115f4b178XCDLEfy', $date]
        ];
    }
}