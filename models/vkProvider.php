<?php

namespace provider;

class vkProvider {

  protected $token = null; 
 
  protected $appId = null;

  protected $appSecret = null;
  
  public function __construct( $token ){
     

     $this->appId = VK_APP_ID;

     $this->appSecret = VK_APP_SECRET;

     $this->token = $token;  

  }
  
  public function getPosts(){

     $apiQuery = 'newsfeed.get?filters=post,photo,note';

     $posts = $this->getQuery( $apiQuery );
     
     return $posts;

  }

  public function getQuery( $query ){
   
    $query_url = 'https://api.vk.com/method/' . $query
                     .'&access_token=' . $this->token;
    
    $result = \json_decode( $this->cURL( $query_url ), true ); 

    return  $result['response']['items'];

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
