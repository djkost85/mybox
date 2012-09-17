<?php
namespace box;

class api {


  public function getPosts( $app ) {
     
     $posts = array();
     
     $vk_posts = array();

     $fb_posts = array();

     if( $app['session']->has('vk') ){
   
         $vk = $app['session']->get('vk');

         $vkP = new \provider\vkProvider( $vk['token'] );

         $vk_posts = $vkP->getPosts();


     }

     if( $app['session']->has('facebook') ){
     
         $fb = $app['session']->get('facebook');

         $fbP = new \provider\facebookProvider( $fb['token'] );
         
         $fb_posts = $fbP->getPosts();
     
     }

     $posts = array_merge( $fb_posts, $vk_posts );

     return $posts;

  
  }

}
