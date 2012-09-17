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

         var_dump( $vk_posts );

     }

     if( $app['session']->has('facebook') ){
     
         $fb = $app['session']->get('facebook');

         $fbP = new \provider\vkProvider( $fb['token'] );
         
         $fb_posts = $fbP->getPosts();
     
         var_dump( $fb_posts );
     }

     return $posts;
     //return \array_merge( $fb_posts, $vk_posts );
  
  }

}
