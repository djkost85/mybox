<?php
namespace box;

class api {

  public function setToken( Silex\Application $app ) {

    var_dump( $app );

  }

  public function getPosts( Silex\Application $app ) {

     $provider = ''; // get provider name
     
     $token = ''; // load token
     
     require_once __DIR__.'/../models/'.$provider.'Provider.php'; // include provider class
     
     $class = 'provider\\'.$provider.'Provider';

     $provider = new $class( $token );
     
     echo call_user_func_array( array( $provider, "getPosts" ), array() );
  
  }

}
