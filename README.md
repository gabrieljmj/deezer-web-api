DeezerWebApi
============
[![Build Status](https://travis-ci.org/GabrielJMJ/deezer-web-api.svg)](https://travis-ci.org/GabrielJMJ/deezer-web-api) [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/gabrieljmj/deezer-web-api.svg)](https://scrutinizer-ci.com/g/GabrielJMJ/deezer-web-api/?branch=master) [![](https://img.shields.io/packagist/v/gabrieljmj/deezer-web-api.svg)](https://packagist.org/packages/gabrieljmj/deezer-web-api)

Deezer Web API with PHP.

## Install
Via [Composer](http://getcomposer.org):
```console
$ composer require gabrieljmj/deezer-web-api
```

## Usage
### Authentication
```php
use DeezerWebApi\DeezerSession;
use DeezerWebApi\DeezerWebApi;

session_start();

$session = new DeezerSession(getenv('DEEZER_APP_ID'), getenv('DEEZER_APP_SECRET'), getenv('DEEZER_REDIRECT_URI'));

if (empty($_GET['code'])) {
    $state = md5(uniqid(rand(), true));
    $session->setState($state);
    $_SESSION['state'] = $state;
    $perms = ['manage_library'];
    
    header('Location: ' . $session->getAuthUri($perms);
}

$code = $_GET['code'];
$accessToken = $session->getAccessToken($code);
$deezer = new DeezerWebApi();
$deezer->setAccessToken($accessToken); // Or new DeezerWebApi(null, $accessToken);

$me = $deezer->get('user/me');
```

### Methods
#### ```get($resource[, array $params = []])```
```php
$me = $deezer->get('user/me');
```

#### ```post($resource[, array $params = []])```
```php
$response = $deezer->post('user/me/playlists', ['title' => 'PLAYLIST_TITLE']);
```