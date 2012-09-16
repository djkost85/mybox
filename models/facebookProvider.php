<?php

namespace provider;

class facebookProvider {

  protected $token = null;
  
  public function __construct( $token ){
  
     $this->token = $token;     

  }
  
  public function getPosts(){

     $fql = 'SELECT post_id,  attachment, likes, description, message, actor_id,'
           .'target_id, message FROM stream WHERE'
           .'filter_key in (SELECT filter_key FROM stream_filter WHERE uid=me()'
           .' AND type=\'friendlist\')';

     $posts = array();
     
     return $posts;

  }

}
