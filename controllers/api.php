<?php
namespace box;

class api {
  
  public function getLink( $app ){
      
        $link = $app['request']->get( 'url', null );
        
        return $this->getLinkData($link);
        
  }
      
  public function getLinkData( $link ){
           
      if( $link == null ) return null;
      
      $token = '93f300934628716b1bc619783ca1d773beac60da';
      
      return $this->cURL( 'http://www.readability.com/api/content/v1/parser?url='.urlencode($link).'&token='.$token );
  
  }
  
  public function getPost( $app ){
      
      $id = $app['request']->get( 'id', null );
      
      if( $id == null ) return null;
      
      $post = \box\post::find($id);

      $attachments = json_decode( $post->attachments, true );

      if( $post->parsed != 1 ){
          
          foreach($attachments as $i=>$att){
              
              $article = json_Decode( $this->getLinkData( $att['src'] ), true );
              
              $article['lead_image_url'] = (isset($article['lead_image_url']) and $article['lead_image_url']!=null ) ? $article['lead_image_url'] :'';
              
              $article['title'] = ( $att['title']=='' ) ? $article['title'] : $att['title'];
              
              switch( $att['type'] ){
                  
                  case 'photo':
                        
                       $att['src'] = $article['lead_image_url'];
                      
                       $att['description'] = $article['content'];
                       
                       $att['title'] = $article['title'];
                      
                      break;
                  
                  case 'video':
                      
                       $att['image'] = $article['lead_image_url'];
                      
                       $att['description'] = $article['content'];
                       
                       $att['title'] = $article['title'];
                      
                      break;
                  
                  case 'link':
                      
                       $att['image'] = $article['lead_image_url'];
                       
                       $att['description'] = $article['content'];
                       
                       $att['title'] = $article['title'];
                      
                      break;
              }
              
              $attachments[$i] = $att;
              
          }
          
          $post->save();
          
      }
      
      $post = $post->attributes();
      
      $post['attachments'] = $attachments;
      
      return $post;
      
  }
  
  public function getPosts( $app ) {
	

        $daysLimit = 30;
	
	$likes = intval( $app['request']->get( 'likes', 0 ) );
	
	$likes = $likes < 0 ? 0 : $likes;
	
	$limit = intval( $app['request']->get( 'limit', 30 ) ) ;
	
	$limit = ( $limit > 0 and $limit < 100 ) ? $limit : 30 ;
        
        $offset = $app['request']->get( 'offset', 0 );
	
        $beginDate = intval( $app['request']->get( 'beginDate', 0 ) );
        
 	if( $app['user']->lastupdate == 0 ){
	
		$this->setPosts( $app['user'] );
		
	}
        
        if( $offset < 0 and $beginDate > 0){
            
            $this->setPosts( $app['user'] );
            
            $options = array('conditions' => array( 'user = ? AND likes > ? AND date > ?' , $app['user']->id, $likes, $beginDate ), 'order' => 'date DESC', 'limit' => $limit, 'offset' => 0 );
            
        }else{
            
            $options = array('conditions' => array( 'user = ? AND likes > ? AND date > ?' , $app['user']->id, $likes, time() - $daysLimit*3600*24 ), 'order' => 'date DESC', 'limit' => $limit, 'offset' => $offset );
        
        }
        
	$posts =array();
	
	$postList = \box\post::find('all',  $options );
	
	$lc = count( $postList );
	
	for( $i = 0; $i < $lc ; $i++){
		
		$posts[] = $postList[$i]->attributes();
		unset( $postList[$i] );

		$posts[ count( $posts ) - 1 ]['attachments'] = json_decode( $posts[ count( $posts ) - 1 ]['attachments'], true );
	
                
        }
        
	//$posts = array_reverse($posts);
        
	return array('offset'=>$offset, 'posts'=>$posts, 'isEnd'=>(count($posts)<$limit));
	
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

            return null;

        }
        
  }

}
