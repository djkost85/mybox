<?php

namespace provider;

class vkProvider {

  protected $token = null; 
 
  protected $appId = null;

  protected $appSecret = null;
  
  public function __construct( $credits ){
     

     $this->appId = $credits['appId'];

     $this->appSecret = $credits['appSecret'];

  }
  
  public function getPosts(){

     $apiQuery = 'newsfeed.get?filters=post,photo,note';

     $posts = $this->getQuery( $apiQuery );
     
     return $posts;

  }

  public function getQuery( $query ){
   
    $fql_query_url = 'https://api.vk.com/method/' . $query
                     .'&access_token=' . $this->token;
    
    $ch = \curl_init(); 
    \curl_setopt($ch, CURLOPT_URL,$url); // set url to post to 
    \curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
    \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects 
    \curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable 
    \curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s 
    \curl_setopt($ch, CURLOPT_GET, 1); // set POST method 
    $result = curl_exec($ch); // run the whole process 
    \curl_close($ch);            

    return \json_decode($result);

  }

}
