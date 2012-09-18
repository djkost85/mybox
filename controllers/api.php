<?php
namespace box;

class api {


  public function getPosts( $app ) {
     
     $posts = array();
     
     $vk_posts = array();

     $fb_posts = array();

     if( ! empty( $app['user']->vkId ) ){
   
         //$vk = $app['session']->get('vk');

         $vkP = new \provider\vkProvider( $app['user']->vkToken );

         $vk_posts = $vkP->getPosts();


     }

     if( ! empty( $app['user']->fbId ) ){
     
         //$fb = $app['session']->get('facebook');

         $fbP = new \provider\facebookProvider( $app['user']->fbToken );
         
         $fb_posts = $fbP->getPosts();
     
     }

     $app['user']->lastUpdate = time();

     $app['user']->save();

     $posts = array_merge( $fb_posts, $vk_posts );

     return $posts;

  
  }

}
