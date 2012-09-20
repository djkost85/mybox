<?php

namespace box;

class boxPost{

    public $source = 0;
	
	public $text = '';

	public $likes = 0;
	
	public $date = 0;
	
	public $attachments = array();
	
	public $from = '';

	public $userId = 0;
	
	public $source_name = '';
	
	public $source_link = '';
	
	public function __construct( $data ){
	
		foreach( $data as $name=>$value ) $this->$name = $value;
	
	}

    public function getId(){

		return md5( $this->userId  . $this->date . $this->source . $this->from );
	}
	
	public function getArray(){
	
		$title = trim( strip_tags( $this->text ) );
		
		if( mb_strlen( $title ) > 120 ){
		
			$title = mb_substr( $title, 0, 117 ).'...';
			
		}else{
			
			$this->text ='';
			
		}
		return array(	
		
				'id' => $this->getId(),
				'title' => $title,
				'text' => $this->text,
				'attachments' => json_encode( $this->attachments ),
				'likes' => $this->likes,
				'from' => $this->from,
				'source' => $this->source,
				'user' => $this->userId,
				'date' => $this->date,
				'source_name' => $this->source_name,
				'source_link' => $this->source_link
				
			);
	
	}
}

?>
