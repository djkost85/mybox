<?php

namespace provider;

class facebookProvider {

  protected $token = null;
  
  protected $appId = null;

  protected $appSecret = null;

  public function __construct( $credits ){
  
     $this->appId = $credits['appId'];

     $this->appSecret = $credits['appSecret'];     

  }
  


  public function getPosts(){

     $fql = 'SELECT post_id,  attachment, likes, description, message, actor_id,'
           . 'target_id, message FROM stream WHERE'
           . 'filter_key in (SELECT filter_key FROM stream_filter WHERE uid=me()'
           . ' AND type=\'friendlist\')';

     $posts = array();

     $posts = $this->getQuery( $fql );
     
     return $posts;

  }

  public function getQuery( $query ){
   
    $fql_query_url = 'https://graph.facebook.com/' . '/fql?q=' . $query
    . '&access_token=' . $this->token;
    
    $ch = \curl_init(); 
    \curl_setopt($ch, CURLOPT_URL,$fql_query_url); // set url to post to 
    \curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
    \curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
    \curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s 
    \curl_setopt($ch, CURLOPT_GET, 1); // set POST method 
    $result = \curl_exec($ch); // run the whole process 
    \curl_close($ch);            

    return \json_decode($result);

  }

}
