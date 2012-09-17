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

     $fql = 'SELECT post_id,  attachment, likes, description, message, created_time, actor_id,'
           . 'target_id, message FROM stream WHERE type in (80,128,247,308) and '
           . 'filter_key IN ( SELECT filter_key FROM stream_filter WHERE '
           . 'uid=me() and type=\'friendlist\') ';

     $posts = array();
     
     $posts = $this->getQuery( $fql );

     return $posts;

  }

  private function getQuery( $query ){
   
    $fql_query_url = 'https://graph.facebook.com/fql?q=' . urlencode($query)
    . '&limit=100&access_token=' . $this->token;
    
    $result = \json_decode( $this->cURL( $fql_query_url ), true );            

    $posts = array();

    foreach( $result['data'] as $k=>$item){

          $item['image'] = '';

          $item['text'] = implode("\n", array( isset( $item['description'] )? $item['description'] : '' ,  isset( $item['message'] )? $item['message'] : '' ) );



         if( isset( $item['attachment'] ) ){

             $item['text'] = empty( $item['text'] ) ? $item['attachment'][0]['description'] : $item['text'];
             
             if( isset( $item['attachment']['media'][0] ) ){

                 $item['image'] = $item['attachment']['media'][0]['src'];

             }

         }

         

         $posts[] = new \box\post(md5('fb' . $item['post_id'] ), 'fb', $item['post_id'], $item['text'], isset($item['attachment']) ? $item['attachment'] : array(), $item['created_time'], isset($item['likes']['count']) ? $item['likes']['count'] : 0, isset( $item['action_links'] ) ? $item['action_links'] : array() , $item['actor_id'], $item['image']);
     
    }

    return $posts;
   

  }

  private function parseAttachments(){
  }

  private function parseLinks(){
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
