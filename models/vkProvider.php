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

     $apiQuery = 'newsfeed.get?filters=post,note,photo';

     $posts = $this->getQuery( $apiQuery );
     
     return $posts;

  }

  public function getQuery( $query ){
   
    $query_url = 'https://api.vk.com/method/' . $query
                     .'&count=100&access_token=' . $this->token;
    
    $result = \json_decode( $this->cURL( $query_url ), true ); 

    foreach( $result['response']['items'] as $k=>$item){

         $item['image'] = '';

         $item['attachment'] = isset( $item['attachment'] ) ? $item['attachment'] : array();

         $item['links'] = array();
         
         $item['text'] = isset( $item['text'] ) ? $item['text'] : '';

         $item['post_id'] = isset( $item['post_id'] ) ? $item['post_id'] : 0;


         if( isset( $item['photos'] ) ){

             foreach( $item['photos'] as $phk=>$photo ){
             
                $item['attachment'][] = $photo['src']; 

                $item['links'][] = $photo['src_big'];
             
             }

             $item['image'] = $item['photos'][0]['src'];
         }

         if( isset( $item['attachment']['photo'] ) ){
            
             $item['image'] = $item['attachment']['photo']['src'];
                     
         }

         if( isset( $item['notes'] ) ){

            foreach( $item['notes'] as $nk=>$note ){

                $item['links'][] = 'http://vk.com/note' . $note['owner_id'] . '_' . $note['nid'];

                $item['text'] .= $item['title'] . "\n";
             
            }
         }


         $posts[] = new \box\post(md5('vk' . $item['post_id'] ), 'vk', $item['post_id'], $item['text'], $item['attachment'], $item['date'], isset( $item['likes']['count'] ) ? $item['likes']['count'] : 0, $item['links'], $item['source_id'], $item['image']);
     
    }

    return $posts;

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
