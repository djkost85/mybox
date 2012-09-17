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

     $fql = 'SELECT source_id, attachment, likes, description, message, created_time, actor_id,'
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

         $data = $this->setItem( $item );
		 
		 if( $data!=null ) $posts[] = new \box\post( $data );    
    }

    return $posts;
   

  }

	public function setItem( $input ){

		if( ! isset( $input['likes']['count'] ) or $input['likes']['count'] == 0 ){
		
			return null;
		
		}
		
		$item = array();
		
		$item['from'] = 'facebook.com';
		
		$item['source'] = isset( $input['source_id'] ) ? $input['source_id'] : 0;
		
		$item['date'] = $input['created_time'];
		
		$item['likes'] = $input['likes']['count'];
		
		$item['text'] = implode('<br/>', array( $input['message'], $input['description'] ) );

		if( isset($input['attachment'] ) ){
			
			$item['text'] = array();
			
			if( isset ( $input['attachment']['name'] ) ) $item['text'][] = $input['attachment']['name'];
			
			if( isset ( $input['attachment']['caption'] ) ) $item['text'][] = $input['attachment']['caption'];
			
			if( isset ( $input['attachment']['description'] ) ) $item['text'][] = $input['attachment']['description'];

		
			$item['text'] = implode('<br/>', $item['text'] );
			
			if( isset( $input['attachment']['media'] ) and count( $input['attachment']['media'] ) > 0 ){
			
				$result = array();
					
				foreach($input['attachment']['media'] as $k=>$att ){
					
					switch( $att['type'] ){
					
						case 'photo':
							$result[] = array('type' => 'photo',
											  'image' => $att['src'],
											  'src' => $att['href'],
											  'description' => '',
											  'title' => ''
											  );		
						break;
						
						case 'flash':
							$result[] = array('type' => 'video',
											  'image' => $att['src'],
											  'src' => $att['href'],
											  'description' => '',
											  'title' => ''
											  );			
						break;
						
						
						case 'mp3':
							$result[] = array('type' => 'link',
											  'image' => '',
											  'src' => $att['src'],
											  'description' => ( isset( $att['artist'] ) ? $att['artist'] : '' ) . ' ' . ( isset( $att['album'] ) ? $att['album'] : '' ),
											  'title' => isset( $att['title'] ) ? $att['title'] : ''
											  );
						break;
						
					
					}	
					
				}
				
				$item['attachments'] = $result;
			}
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
