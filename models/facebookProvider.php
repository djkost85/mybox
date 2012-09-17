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
           . 'target_id, message FROM stream WHERE '
           . 'filter_key IN ( SELECT filter_key FROM stream_filter WHERE uid=me()'
           . ' and type=\'friendlist\')';

     $posts = array();
     
     $posts = $this->getQuery( $fql );
     
     return $posts;

  }

  private function getQuery( $query ){
   
    $fql_query_url = 'https://graph.facebook.com/fql?q=' . urlencode($query)
    . '&limit=100&access_token=' . $this->token;
    
    $result = \json_decode( $this->cURL( $fql_query_url ), true );            

    $posts = array();

    $item['text'] = implode("\n", array( $item['description'], $item['message'] ) );

    $item['title'] = mb_substr( $item['text'], 0, 100 );

    foreach( $result['data'] as $k=>$item){
    
         $posts[] = new \box\post(md5('fb' . $item['post_id'] ), 'fb', $item['post_id']. $item['title'], $item['text'], $item['attachment'], $item['updated_time'], $item['likes']['count'], $item['action_links'], $item['actor_id']);
     
    }
   

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
