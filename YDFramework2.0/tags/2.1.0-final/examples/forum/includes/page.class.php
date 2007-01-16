<?php
  class Page {
    var $content;
    var $title;
    var $menus;
    
    function Page($content, $title, $menus=null) {
      $this->content = $content;
      $this->title = $title;
      $this->menus = $menus;
    } 
  }
?>
