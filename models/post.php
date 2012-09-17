<?php

namespace box;

class post{

   public $id = 0;

   public $from = '';

   public $postId=0;

   public $title='';
 
   public $text='';

   public $attachment = array();

   public $date = '';

   public $likes = 0;

   public $links = array();

   public $authorId=0;

   public function __construct( $id = 0, $from = '', $postid = 0, $title = '', $text = '', $attachment = array(), $date = '', $likes = 0, $links = array(), $authorId = 0 ){
   
       $this->id = $id;
       $this->from = $from;
       $this->postid = $postid;
       $this->title = $title;
       $this->text = $text;
       $this->attachment = $attachment;
       $this->date = $date;
       $this->likes = $likes;
       $this->links = $likes;
       $this->authorId = $authorId;
   }

}

?>
