<?php
namespace Test\DeezerWebApi;

use DeezerApi\DeezerWebApi;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;

class DeezerWebApiTest extends \PHPUnit_Framework_TestCase
{
    const API_URL = 'https://api.deezer.com';
    
    public function setUp()
    {
        $this->accessToken = $this->getMockBuilder('\DeezerWebApi\AccessToken')
                                  ->disableOriginalConstructor()
                                  ->getMock();
        
        $this->accessToken
            ->method('getAccessToken')
            ->willReturn('nyl9ZFtFMA55f2115f4b13bUNBAkarm55f2115f4b178XCDLEfy');
        $this->accessToken
             ->method('expiresAt')
             ->willReturn((new \DateTime())->modify('+5 minutes'));
    }
    
    /**
     * @covers ::get
     */
    public function testGetRequestsForApiSuccessfully()
    {
        $expectedStatus = 200;
        $expectedResponse = '{
          "id": "122505003",
          "name": "Gabriel Jacinto",
          "lastname": "Jacinto",
          "firstname": "Gabriel",
          "birthday": "1994-02-04",
          "inscription_date": "2013-01-18",
          "gender": "M",
          "link": "http://www.deezer.com/profile/122505003",
          "picture": "https://api.deezer.com/user/122505003/image",
          "picture_small": "https://cdns-images.deezer.com/images/user/3d02334ee88d347a7dc393bea6bcd69b/56x56-000000-80-0-0.jpg",
          "picture_medium": "https://cdns-images.deezer.com/images/user/3d02334ee88d347a7dc393bea6bcd69b/250x250-000000-80-0-0.jpg",
          "picture_big": "https://cdns-images.deezer.com/images/user/3d02334ee88d347a7dc393bea6bcd69b/500x500-000000-80-0-0.jpg",
          "country": "BR",
          "lang": "PT",
          "tracklist": "https://api.deezer.com/user/122505003/flow",
          "type": "user",
          "status": 0
        }';
        $uri = '/user/me';
        $mockedClient = $this->mockClient(
            'GET',
            self::API_URL . $uri,
            ['query' => http_build_query(['access_token' => $this->accessToken->getAccessToken()])],
            $expectedStatus,
            $expectedResponse
        );
        $deezer = new DeezerWebApi($this->accessToken, $mockedClient);
        
        $response = $deezer->get($uri);
        
        $this->assertEquals(json_decode($expectedResponse), $response);
    }
    
    /**
     * @covers ::post
     */
    public function testPostRequestsForApiSuccessfully()
    {
        $expectedStatus = 200;
        $expectedResponse = '{
          "id": "64071909"
        }';
        $uri = '/user/me/playlists';
        $params = ['title' => 'PLAYLIST_TITLE'];
        $mockedClient = $this->mockClient(
            'POST',
            self::API_URL . $uri,
            ['query' => http_build_query(array_merge($params, ['access_token' => $this->accessToken->getAccessToken()]))],
            $expectedStatus,
            $expectedResponse
        );
        $deezer = new DeezerWebApi($this->accessToken, $mockedClient);
        
        $response = $deezer->post($uri, $params);
        
        $this->assertEquals(json_decode($expectedResponse), $response);
    }
    
    public function testGetRequestWithStatusDifferentOf200()
    {
        $expectedStatus = 404;
        $expectedResponse = '{
          "error": {
            "type": "DataException",
            "message": "no data",
            "code": 800
          }
        }';
        $uri = '/playlist/INVALID_PLAYLIST';
        $mockedClient = $this->mockClient(
            'GET',
            self::API_URL . $uri,
            ['query' => http_build_query(['access_token' => $this->accessToken->getAccessToken()])],
            $expectedStatus,
            $expectedResponse
        );
        $deezer = new DeezerWebApi($this->accessToken, $mockedClient);
        
        $response = $deezer->get($uri);
        
        $this->assertEquals(json_decode($expectedResponse), $response);
    }
    
    public function testPostRequestWithStatusDifferentOf200()
    {
        $expectedStatus = 403;
        $expectedResponse = '{
          "error": {
            "type": "OAuthException",
            "message": "Invalid OAuth access token.",
            "code": 300
          }
        }';
        $uri = '/user/me/playlists';
        $params = ['title' => 'PLAYLIST_TITLE'];
        $mockedClient = $this->mockClient(
            'POST',
            self::API_URL . $uri,
            ['query' => http_build_query(array_merge($params, ['access_token' => $this->accessToken->getAccessToken()]))],
            $expectedStatus,
            $expectedResponse
        );
        $deezer = new DeezerWebApi($this->accessToken, $mockedClient);
        
        $response = $deezer->post($uri, $params);
        
        $this->assertEquals(json_decode($expectedResponse), $response);
    }
    
    private function mockClient($method, $uri, $params, $expectedStatus, $expectedResponse)
    {
        $request = $this->getMock('\GuzzleHttp\Psr7\Response');
        
        $request->method('getBody')
                ->willReturn($expectedResponse);
        $request->method('getStaus')
                ->willReturn($expectedStatus);
             
        $client = $this->getMock('\GuzzleHttp\Client');
                       
        $client->expects($this->once())
               ->method('request')
               ->with($method, $uri, $params)
               ->will($this->returnValue($request));

        return $client;
    }
}