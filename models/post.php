<?php

namespace box;

class post{

    public $source = 0;
	
	public $text = '';

	public $likes = 0;
	
	public $date = 0;
	
	public $attachments = array();
	
	public $from = '';
	
	public function __construct( $data ){
	
		foreach( $data as $name=>$value ) $this->$name = $value;
	
	}
	
}

?>
