<?php
include '../vendor/autoload.php';
include '../src/Wrike.php';
include '../src/Provider/Wrike.php';
include '../src/Provider/WrikeResourceOwner.php';

session_start();

use IsevLtd\OAuth2\Client\Provider\Wrike as WrikeProvider;
use IsevLtd\Wrike\Client as Wrike;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

$oauth = new WrikeProvider([
  'clientId'     => 'fXXpw8Sp',
  'clientSecret' => 'CQOXFijEcFhYaBV95P9Wd9R60pgJBx3McrG6f1Xv2pJgSS5NR8F1WsndSNsmUinE',
  'redirectUri'  => 'http://localhost'
]);

function authenticate( $oauth ) {
  if ( !isset( $_GET[ 'code' ] ) ) {
    $oauth->authorize([], function( $url, $oauth ){
      $_SESSION[ 'wrike_oauth2_state' ] = $oauth->getState();
      header( 'Location: ' . $url );
      exit;
    });
  }
  elseif ( empty( $_GET[ 'state' ] ) || ( $_GET[ 'state' ] !== $_SESSION[ 'wrike_oauth2_state' ] ) ) {
    unset( $_SESSION[ 'wrike_oauth2_state' ] );
    echo 'Invalid state';
    echo '<br><a href="/">Retry</a>';
    exit;
  }
  else {
    try {
      $token = $oauth->getAccessToken( 'authorization_code', [ 'code' => $_GET[ 'code' ] ]);
      $_SESSION[ 'wrike_oauth2_state' ] = $oauth->getState();

      $_SESSION[ 'wrike_token' ] = $token;
    }
    catch ( IdentityProviderException $e ) {
      // Failed to get the access token or user details.
      echo 'Error: ' . $e->getMessage();
    }

    header('Location: /');
    exit;
  }
}

if ( ! $_SESSION[ 'wrike_token' ] instanceof AccessToken ) {
  authenticate( $oauth );
}
elseif ( $_SESSION[ 'wrike_token' ]->getExpires() < time() ) {
  $token = $oauth->getAccessToken( 'refresh_token', [ 'refresh_token' => $_SESSION[ 'wrike_token' ]->getRefreshToken() ]);
  $_SESSION[ 'wrike_token' ] = $token;
}
else {
  // Test an API call
  echo '<br><a href="/">Retry</a>';
  $client = new Wrike( $_SESSION[ 'wrike_token' ], $oauth );
  var_dump($client->get_folder_tree());
}


function test_api_call() {
  //new WrikeClient()
}