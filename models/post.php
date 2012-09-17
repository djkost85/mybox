<?php

namespace box;

class post{

   public $id = 0;

   public $from = '';

   public $postId=0;
 
   public $text='';

   public $attachment = array();

   public $date = '';

   public $likes = 0;

   public $links = array();

   public $authorId=0;

   public $image='';

   public function __construct( $id = 0, $from = '', $postid = 0,  $text = '', $attachment = array(), $date = '', $likes = 0, $links = array(), $authorId = 0, $image = '' ){
   
       $this->id = $id;
       $this->from = $from;
       $this->postId = $postid;
       $this->text = $text;
       $this->attachment = $attachment;
       $this->date = $date;
       $this->likes = $likes;
       $this->links = $links;
       $this->authorId = $authorId;
       $this->image = $image;
   }

}

?>
