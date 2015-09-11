<?php
namespace Test\DeezerWebApi;

use DeezerWebApi\Session;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DeezerApi\Session
     */
    private $api;
    
    public function setUp()
    {
        $this->api = new Session(getenv('DEEZER_APP_ID'), getenv('DEEZER_APP_SECRET'), getenv('DEEZER_REDIRECT_URI'));
    }
    
    public function testConstructorParamsSetted()
    {
        $expectedAppId = getenv('DEEZER_APP_ID');
        $expectedAppSecret = getenv('DEEZER_APP_SECRET');
        $expectedRedirectUri = getenv('DEEZER_REDIRECT_URI');
        
        $this->assertEquals($expectedAppId, $this->api->getAppId());
        $this->assertEquals($expectedAppSecret, $this->api->getSecret());
        $this->assertEquals($expectedRedirectUri, $this->api->getRedirectUri());
    }
    
    /**
     * @covers ::getState
     */
    public function testSettingAndGettingState()
    {
        $state = md5(uniqid(), true);
        $this->api->setState($state);
        
        $this->assertObjectHasAttribute('state', $this->api);
        $this->assertEquals($state, $this->api->getState());
    }
    
    /**
     * @covers ::getCode
     */
    public function testSettingAndGettingCode()
    {
        $code = md5(uniqid(), true);
        $this->api->setCode($code);
        
        $this->assertObjectHasAttribute('code', $this->api);
        $this->assertEquals($code, $this->api->getCode());
    }
    
    /**
     * @depends testSettingAndGettingState
     * @depends testSettingAndGettingCode
     * @cover   ::getAuthUri
     */
    public function testReturnForAuthUriWithoutPermissions()
    {
        $this->generateTestForAuthUri();
    }
    
    /**
     * @depends testSettingAndGettingState
     * @depends testSettingAndGettingCode
     * @cover   ::getAuthUri
     */
    public function testReturnForAuthUriWithPermissions()
    {
        $this->generateTestForAuthUri(['manage_library']);
    }
    
    private function generateTestForAuthUri(array $perms = [])
    {
        $state = md5(uniqid(), true);
        $this->api->setState($state);
        $params = http_build_query([
            'app_id' => getenv('DEEZER_APP_ID'),
            'secret' => getenv('DEEZER_APP_SECRET'),
            'redirect_uri' =>  getenv('DEEZER_REDIRECT_URI'),
            'perms' => implode(',', $perms),
            'state' => $state
        ]);
        $expected = 'https://connect.deezer.com/oauth/auth.php?' . $params;
    
        $this->assertEquals($expected, $this->api->getAuthUri($perms));
    }
    
    public function testAccessToken()
    {
    }
}