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

         $data = $this->setItem( $item );
		 
		 if( $data!=null ) $posts[] = new \box\boxPost( $data );
     
    }

    return $posts;

  }

	public function setItem( $input ){


		if( ! isset( $input['likes']['count'] ) or $input['likes']['count'] == 0 ){
		
			return null;
		
		}
		
		$item = array();
		
		$item['from'] = 'vk.com';
		
		$item['source'] = $input['source_id'];
		
		$item['date'] = $input['date'];
		
		$item['likes'] = $input['likes']['count'];
		
		$item['likes'] = isset( $input['reposts']['count'] ) ? $item['likes'] + $input['reposts']['count'] : $item['likes'];
		
		$item['text'] = $input['text'];
		
		if($input['type'] == 'photo'){
		
			$result = array();
			
			foreach( $item['photos'] as $i=>$photo){
			
						$result[] = array('type' => 'photo',
										  'image' => $photo['src'],
										  'src' => $photo['src_big'],
										  'description' => '',
										  'title' => ''
										  );		
			
			}
		
		}	
		
		if($input['type'] == 'note'){
		
			$result = array();
			
			foreach( $item['notes'] as $i=>$note){
			
						$result[] = array('type' => 'link',
										  'image' => '',
										  'src' => 'http://vk.com/note' . $note['title'] . '_' . $note['nid'],
										  'description' => '',
										  'title' => $note['title']
										  );		
			
			}
		
		}	
		
		if($input['type'] == 'post' and isset($input['attachments']) and count($input['attachments'])>0 ){
		
			$result = array();
			
			foreach($input['attachments'] as $k=>$att ){

				switch( $att['type'] ){
				
					case 'photo':
					
						$result[] = array('type' => 'photo',
										  'image' => $att['photo']['src'],
										  'src' => $att['photo']['src_big'],
										  'description' => isset( $att['photo']['description'] ) ? $att['photo']['description'] : '',
										  'title' => isset( $att['photo']['title'] ) ? $att['photo']['title'] : ''
										  );		
					break;
					
					case 'video':
					
						$result[] = array('type' => 'video',
										  'image' => $att['video']['image'],
										  'src' => 'http://vk.com/video_ext.php?oid='.$att['video']['owner_id'].'&id='.$att['video']['vid'].'&hash='.$att['video']['access_key'].'&hd=1',
										  'description' => isset( $att['video']['description'] ) ? $att['video']['description'] : '',
										  'title' => isset( $att['video']['title'] ) ? $att['video']['title'] : ''
										  );			
					break;
					
					
					case 'link':
					
						$result[] = array('type' => 'link',
										  'image' => '',
										  'src' => $att['link']['url'],
										  'description' => isset( $att['link']['description'] ) ? $att['link']['description'] : '',
										  'title' => isset( $att['link']['title'] ) ? $att['link']['title'] : ''
										  );
					break;
					
					default:
					
						return null;
						
					break;
				
				}
			}
			
			$item['attachments'] = $result;
			
		}

		
		
		return $item;

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
