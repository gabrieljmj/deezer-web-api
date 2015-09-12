<?php
namespace Test\DeezerWebApi;

use DeezerWebApi\DeezerWebApi;
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
        $deezer = new DeezerWebApi($mockedClient);
        $deezer->setAccessToken($this->accessToken);
        
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
        $deezer = new DeezerWebApi($mockedClient);
        $deezer->setAccessToken($this->accessToken);
        
        $response = $deezer->post($uri, $params);
        
        $this->assertEquals(json_decode($expectedResponse), $response);
    }
    
    public function testGetRequestWithNoAccesstokenSettedOnResourceThatDoesNotNeedIt()
    {
        $expectedStatus = 200;
        $expectedResponse = '{
          "data": [
            {
              "id": "88486011",
              "readable": true,
              "title": "My House",
              "title_short": "My House",
              "title_version": "",
              "link": "http://www.deezer.com/track/88486011",
              "duration": "242",
              "rank": "818376",
              "explicit_lyrics": false,
              "preview": "http://cdn-preview-c.deezer.com/stream/cbc5964dfe7527d63ef33e90060abfca-4.mp3",
              "artist": {
                "id": "5979468",
                "name": "PVRIS",
                "link": "http://www.deezer.com/artist/5979468",
                "picture": "https://api.deezer.com/artist/5979468/image",
                "picture_small": "https://cdns-images.deezer.com/images/artist/57c85633a967775a42ec22c2a7a406ba/56x56-000000-80-0-0.jpg",
                "picture_medium": "https://cdns-images.deezer.com/images/artist/57c85633a967775a42ec22c2a7a406ba/250x250-000000-80-0-0.jpg",
                "picture_big": "https://cdns-images.deezer.com/images/artist/57c85633a967775a42ec22c2a7a406ba/500x500-000000-80-0-0.jpg",
                "tracklist": "https://api.deezer.com/artist/5979468/top?limit=50",
                "type": "artist"
              },
              "album": {
                "id": "8940141",
                "title": "White Noise",
                "cover": "https://api.deezer.com/album/8940141/image",
                "cover_small": "https://cdns-images.deezer.com/images/cover/0461c973bf377a036a851f560bcbe202/56x56-000000-80-0-0.jpg",
                "cover_medium": "https://cdns-images.deezer.com/images/cover/0461c973bf377a036a851f560bcbe202/250x250-000000-80-0-0.jpg",
                "cover_big": "https://cdns-images.deezer.com/images/cover/0461c973bf377a036a851f560bcbe202/500x500-000000-80-0-0.jpg",
                "tracklist": "https://api.deezer.com/album/8940141/tracks",
                "type": "album"
              },
              "type": "track"
            }
          ],
          "total": 1
        }';
        $uri = '/search/track';
        $params = ['q' => 'pvris my house'];
        $mockedClient = $this->mockClient(
            'GET',
            self::API_URL . $uri,
            ['query' => http_build_query($params)],
            $expectedStatus,
            $expectedResponse
        );
        $deezer = new DeezerWebApi($mockedClient);
        
        $response = $deezer->get($uri, $params);
        
        $this->assertEquals(json_decode($expectedResponse), $response);
    }
    
    public function testGetRequestWithNoAccessTokenSettedOnResourceThatNeedIt()
    {
        $expectedStatus = 200;
        $expectedResponse = '{"error":{"type":"OAuthException","message":"An active access token must be used to query information about the current user","code":200}}';
        $uri = '/user/me';
        $params = ['q' => 'pvris my house'];
        $mockedClient = $this->mockClient(
            'GET',
            self::API_URL . $uri,
            ['query' => http_build_query($params)],
            $expectedStatus,
            $expectedResponse
        );
        $deezer = new DeezerWebApi($mockedClient);
        
        $response = $deezer->get($uri, $params);
        
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