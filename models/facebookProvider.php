<?php

namespace provider;

class facebookProvider {

  protected $token = null;
  
  protected $appId = null;

  protected $appSecret = null;

  public function __construct( $token ){
  
     $this->appId = FACEBOOK_APP_ID;

     $this->appSecret = FACEBOOK_APP_SECRET;   

     $this->token = $token;  

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

  private function getQuery( $query ){
   
    $fql_query_url = 'https://graph.facebook.com/' . '/fql?q=' . $query
    . '&access_token=' . $this->token;
    
    $result = $this->cURL( $fql_query_url );            

    return \json_decode($result);

  }

  private function cURL($url, $header=NULL, $cookie=NULL, $p=NULL){
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_HEADER, $header);
        \curl_setopt($ch, CURLOPT_NOBODY, $header);
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        \curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        \curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if ($p) {
            \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            \curl_setopt($ch, CURLOPT_POST, 1);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
        }

        $result = \curl_exec($ch);
        \curl_close($ch);
        
        if ($result) {

            return $result;

        } else {

            return \curl_error($ch);

        }
        
  }

}
