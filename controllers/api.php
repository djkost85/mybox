<?php
namespace box;

class api {

  public function getPosts( $app ) {
	

	if( $app['user']->lastupdate == 0 ){
	
		$this->setPosts( $app['user'] );
		
	}
	
	$likes = $app['request']->get( 'likes', 0 );
	
	$posts =array();
	
	$options = array('conditions' => array( 'user = ? AND likes > ?' , $app['user']->id, $likes ), 'order' => 'date desc', 'limit'=>100);
	
	$postList = \box\post::find('all',  $options );
	
	$lc = count( $postList );
	
	for( $i = 0; $i < $lc ; $i++){
		
		$posts[] = $postList[$i]->attributes();
		unset( $postList[$i] );

		$posts[ count( $posts ) - 1 ]['attachments'] = json_decode( $posts[ count( $posts ) - 1 ]['attachments'], true );
	}
	
	return $posts;
	
  }
  
  public function setPosts( $user ) {
     
     $posts = array();
     
     $vk_posts = array();

     $fb_posts = array();

     if( $user == null ) return array();

     if( ! empty( $user->vkid ) ){
   
         //$vk = $app['session']->get('vk');

         $vkP = new \provider\vkProvider( $user->vktoken );

         $vk_posts = $vkP->getPosts();


     }

     if( ! empty( $user->fbid ) ){
     
         //$fb = $app['session']->get('facebook');

         $fbP = new \provider\facebookProvider( $user->fbtoken );
         
         $fb_posts = $fbP->getPosts();
     
     }
	 
	 $vk_posts = ( $vk_posts == null ) ?  array() : $vk_posts;
	 
	 $fb_posts = ( $fb_posts == null ) ?  array() : $fb_posts;
	  
     $postList = array_merge( $fb_posts, $vk_posts );
	 
	 foreach( $postList as $k=>$post ){
		
		$posts[ $post->getId() ] = $post;
	 
	 }
	 
	 foreach( $posts as $k=>$post ){
	 
		$post->userId = $user->id;
		
		if( null ==  \box\post::find_by_id( $post->getId() ) ){
		
			$post = new \box\post( $post->getArray() );
			
			$post->save();
			
		}

	 }

  
  }

}
