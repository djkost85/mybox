<?php

namespace provider;

class twitterProvider {

  protected $token = null;
  
  protected $token_secret = null;
  
  protected $consumer_key = null;

  protected $consumer_secret = null;
  
  public function __construct( $credits ){
  
     $this->consumer_key = $credits['consumer_key'];   
     
     $this->consumer_secret = $credits['consumer_secret'];
  
  }
  
  public function getPosts(){

     $apiQuery = 'newsfeed.get?filters=post,photo,note';

     $posts = $this->getQuery( $apiQuery );
     
     return $posts;

  }

  public function getQuery( $query ){
   
      $url = "https://api.twitter.com/1.1/statuses/home_timeline.json";
    
      $oauth = array( 'oauth_consumer_key' => $this->consumer_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $this->token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0');
      $base_info = $this->buildBaseString($url, 'GET', $oauth);
      $composite_key = \rawurlencode($this->consumer_secret) . '&' . \rawurlencode($this->token_secret);
      $oauth_signature = \base64_encode(\hash_hmac('sha1', $base_info, $composite_key, true));
      $oauth['oauth_signature'] = $oauth_signature;
      $header = array(buildAuthorizationHeader($oauth), 'Expect:');
      $options = array( CURLOPT_HTTPHEADER => $header,
                  CURLOPT_HEADER => false,
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_SSL_VERIFYPEER => false);
      $feed = curl_init();
      curl_setopt_array($feed, $options);
      $json = curl_exec($feed);
      curl_close($feed);

      return \json_decode($json);     

   }

   protected function buildBaseString($baseURI, $method, $params)
   {
       $r = array();
       \ksort($params);
       foreach($params as $key=>$value){
           $r[] = "$key=" . \rawurlencode($value);
       }
       return $method."&" . \rawurlencode($baseURI) . '&' . \rawurlencode(\implode('&', $r));

   }

   protected function buildAuthorizationHeader($oauth){

      $r = 'Authorization: OAuth ';
      $values = array();
      foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . \rawurlencode($value) . "\"";
      $r .= \implode(', ', $values);
      return $r;
   }

}
