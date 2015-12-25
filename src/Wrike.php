<?php namespace SimonHamp\Wrike;

use SimonHamp\OAuth2\Client\Provider\Wrike as WrikeProvider;
use League\OAuth2\Client\Token\AccessToken;

class Client {
  private $api_credentials;

  private $access_token;
  private $oauth_client;

  const BASE_URL = 'https://www.wrike.com/api/v3';

  public function __construct( $access_token = null, $oauth_client = null ) {
    if ( !is_null( $access_token ) && is_null( $this->access_token ) ) {
      $this->setAccessToken( $access_token );
    }

    if ( !is_null( $oauth_client ) && is_null( $this->oauth_client ) ) {
      $this->setOauthClient( $oauth_client );
    }
  }

  public function get_contacts( $ids = [], $metadata = [], $fields = [] ) {
    if ( count( $ids ) > 100 )
      throw new \Exception( 'Maximum number of IDs is 100' );

    $ids_string = '';
    if ( !empty( $ids ) )
      $ids_string = '/' . implode( ',', $ids );

    $params = [];

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/contacts' . $ids_string,
      'params' => $params
    ]);
  }

  public function get_account_contacts( $account, $metadata = [], $fields = [] ) {
    $params = [];

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/accounts/' . $account . '/contacts',
      'params' => $params
    ]);
  }

  public function modify_contact( $id, $metadata = [] ) {
    $params = [];

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    return $this->requestFactory([
      'method' => 'PUT',
      'action' => '/contacts/' . $id,
      'params' => $params
    ]);
  }

  public function get_group( $id, $account = null, $fields = [] ) {
    $params = [];

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/groups/' . $id,
      'params' => $params
    ]);
  }

  public function get_account_groups( $account, $metadata = [], $fields = [] ) {
    $params = [];

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/accounts/' . $account . '/groups',
      'params' => $params
    ]);
  }

  public function create_group( $account, $title, $members = [], $parent = null, WrikeGroupAvatar $avatar = null, $metadata = [] ) {
    $body = [
      'title'    => $title,
      'members'  => json_encode( $members ),
      'parent'   => $parent,
      'avatar'   => json_encode( $avatar ),
      'metadata' => json_encode( $metadata )
    ];

    return $this->requestFactory([
      'method' => 'POST',
      'action' => '/accounts/' . $account . '/groups',
      'body'   => $body
    ]);
  }

  public function modify_group( $id, $title, $add_members = [], $remove_members = [], $parent = null, WrikeGroupAvatar $avatar = null, $metadata = []  ) {
    $body = [
      'title'         => $title,
      'addMembers'    => json_encode( $add_members ),
      'removeMembers' => json_encode( $remove_members ),
      'parent'        => $parent,
      'avatar'        => json_encode( $avatar ),
      'metadata'      => json_encode( $metadata )
    ];

    return $this->requestFactory([
      'method' => 'PUT',
      'action' => '/groups/' . $id,
      'body'   => $body
    ]);
  }

  public function delete_group( $id, $test = false ) {
    $params = [];

    if ( $test )
      $params[ 'test' ] = true;

    return $this->requestFactory([
      'method' => 'DELETE',
      'action' => '/groups/' . $id,
      'params' => $params
    ]);
  }

  public function get_accounts( $id = null, $metadata = [], $fields = [] ) {
    $params = [];

    if ( !empty( $metadata ) && is_null( $id ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/accounts' . ( $id ? '/' . $id : '' ),
      'params' => $params
    ]);
  }

  public function modify_account( $id, $metadata = [] ) {
    $body = [
      'metadata' => json_encode( $metadata )
    ];

    return $this->requestFactory([
      'method' => 'PUT',
      'action' => '/accounts/' . $id,
      'body'   => $body
    ]);
  }

  public function get_workflows( $account ) {
    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/accounts/' . $account . '/workflows'
    ]);
  }

  public function create_workflow( $account, $title ) {
    $body = [
      'title' => $title
    ];

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/accounts/' . $account . '/workflows',
      'body'   => $body
    ]);
  }

  public function update_workflow( $id, $name = null, $hidden = false, WrikeCustomStatus $custom_status = null ) {
    $body = [
      'name' => $name,
      'hidden' => (bool) $hidden,
      'customStatus' => json_encode( $custom_status )
    ];

    return $this->requestFactory([
      'method' => 'PUT',
      'action' => '/workflows/' . $id,
      'body'   => $body
    ]);
  }

  public function get_folder_tree( $permalink = null, $descendants = true, $metadata = [], $custom_field = [], $fields = [] ) {
    $params = [];

    if ( !empty( $permalink ) )
      $params[ 'permalink' ] = $permalink;

    $params[ 'descendants' ] = $descendants;

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $custom_field ) )
      $params[ 'customField' ] = json_encode( $custom_field );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/folders',
      'params' => $params
    ]);
  }

  public function get_account_folder_tree( $account, $permalink = null, $descendants = true, $metadata = [], $custom_field = [], $fields = [] ) {
    $params = [];

    if ( !empty( $permalink ) )
      $params[ 'permalink' ] = $permalink;

    $params[ 'descendants' ] = $descendants;

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $custom_field ) )
      $params[ 'customField' ] = json_encode( $custom_field );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/accounts/' . $account . '/folders',
      'params' => $params
    ]);
  }

  public function get_folder_sub_tree( $folder, $permalink = null, $descendants = true, $metadata = [], $custom_field = [], $fields = [] ) {
    $params = [];

    if ( !empty( $permalink ) )
      $params[ 'permalink' ] = $permalink;

    $params[ 'descendants' ] = $descendants;

    if ( !empty( $metadata ) )
      $params[ 'metadata' ] = json_encode( $metadata );

    if ( !empty( $custom_field ) )
      $params[ 'customField' ] = json_encode( $custom_field );

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/folders/' . $folder . '/folders',
      'params' => $params
    ]);
  }

  public function get_folders( $ids = [], $fields = [] ) {
    if ( count( $ids ) < 1 )
      throw new \Exception( 'Must supply at least 1 ID' );

    if ( count( $ids ) > 100 )
      throw new \Exception( 'Maximum number of IDs is 100' );

    $ids_string = '';
    if ( !empty( $ids ) )
      $ids_string = '/' . implode( ',', $ids );

    $params = [];

    if ( !empty( $fields ) )
      $params[ 'fields' ] = json_encode( $fields );

    return $this->requestFactory([
      'method' => 'GET',
      'action' => '/folders' . $ids_string,
      'params' => $params
    ]);
  }

  public function create_folder( $folder, $title, $description = null, $shareds = [], $metadata = [], $custom_fields = [] ) {
    $body = [
      'title'        => $title,
      'description'  => $description,
      'shareds'      => json_encode( $shareds ),
      'metadata'     => json_encode( $metadata ),
      'customFields' => json_encode( $custom_fields )
    ];

    return $this->requestFactory([
      'method' => 'POST',
      'action' => '/folders/' . $folder . '/folders',
      'body'   => $body
    ]);
  }

  public function modify_folder( $id, $title, $description = null, $add_parents = [], $remove_parents = [], $add_shareds = [], $remove_shareds = [], $metadata = [], $restore = false, $custom_fields = [] ) {
    $body = [
      'title'         => $title,
      'description'   => $description,
      'addParents'    => json_encode( $add_parents ),
      'removeParents' => json_encode( $remove_parents ),
      'addShareds'    => json_encode( $add_shareds ),
      'removeShareds' => json_encode( $remove_shareds ),
      'metadata'      => json_encode( $metadata ),
      'restore'       => $restore,
      'customFields'  => json_encode( $custom_fields )
    ];

    return $this->requestFactory([
      'method' => 'PUT',
      'action' => '/folders/' . $id,
      'body'   => $body
    ]);
  }

  public function delete_folder( $id ) {
    return $this->requestFactory([
      'method' => 'DELETE',
      'action' => '/folders/' . $id
    ]);
  }


  public function requestFactory( $options = [] ) {
    if ( !array_key_exists( 'method', $options ) )
      throw new \Exception( 'Please provide a request method' );

    $method = strtoupper( $options[ 'method' ] );

    if ( !array_key_exists( 'action', $options ) )
      throw new Exception( 'Please provide an action URL' );

    $qsa = null;
    if ( array_key_exists( 'params', $options ) )
      $qsa = http_build_query( $options[ 'params' ] );

    $action = self::BASE_URL . $options[ 'action' ] . ( $qsa ? '?' . $qsa : '' );

    return $this->oauth_client->getResponse( $this->oauth_client->getAuthenticatedRequest( $method, $action, $this->access_token, $options ) )[ 'data' ];
  }

  public function setAccessToken( AccessToken $access_token ) {
    $this->access_token = $access_token;
  }

  public function setOauthClient( WrikeProvider $oauth_client ) {
    $this->oauth_client = $oauth_client;
  }
}

class WrikeGroupAvatar {
  public $letters;
  public $color;
}

class WrikeCustomStatus {
  protected $is_create = false;
  protected $is_update = false;

  public $id;
  public $group;
  public $name;
  public $color;
  public $hidden;

  public function create( $group, $name, $color = null, $hidden = false ) {
    if ( !$this->is_update ) {
      $this->is_create = true;

      $this->group = $group;
      $this->name = $name;
      $this->color = $color;
      $this->hidden = $hidden;
    }
  }

  public function update( $id, $name, $color = null, $hidden = false ) {
    if ( !$this->is_create ) {
      $this->is_update = true;

      $this->id = $id;
      $this->name = $name;
      $this->color = $color;
      $this->hidden = $hidden;
    }
  }
}