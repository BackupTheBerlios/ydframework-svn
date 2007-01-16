<?php
/*
 * Fichier posts.class.php
 * Décrit les posts
 * 
 * */

require_once('sqlObject.class.php');


class PostObject extends sqlObject {
  var $id;
  var $title;
  var $content;
  
  var $nbViews;
  var $nbAnswers;
  var $idAuthor;
  var $ipAuthor;
  var $loginAuthor;
  var $datePost;
  var $dateAnswer; // dernière réponse au sujet
  var $idForum;
  var $idPostParent;
  var $nbViews;
  var $nbAnswers;
  
  var $idEditor;
  var $ipEditor;
  var $loginEditor;
  var $dateEdited;
  
  var $answers;
  
  var $parity;
  /*
   * Create a new post object from known datas
   * 
   * */
  
  function PostObject 	($title, $content, $idAuthor, $loginAuthor , $datePost, $dateAnswer, $ipAuthor,
	  	$idPostParent, $idForum, $nbViews, $nbAnswers ,$id=null, 
	  	$idEditor=null, $loginEditor=null, $dateEdited=null, $ipEditor=null) {

  		//initialize parent construtor
  		$this->sqlObject();

      	$this->title = $title;
      	$this->content = $content;
      	$this->idAuthor = $idAuthor;
      	$this->ipAuthor = $ipAuthor;
      	$this->loginAuthor = $loginAuthor;
      	    	
      	$this->datePost = $datePost;
      	$this->dateAnswer = $dateAnswer;
      	$this->idForum = $idForum;
      	$this->idPostParent = $idPostParent;
      	$this->nbViews = $nbViews;
      	$this->nbAnswers = $nbAnswers;
      	$this->idEditor = $idEditor;
      	$this->dateEdited = $dateEdited;
      	$this->id = $id;
      	$this->loginEditor = $loginEditor;
      	
  }
  
  /*
   * Insert current post in the database
   * 
   */
  function insert() {
	    $values = array();
	    
	    $values['title'] = $this->title;
	    $values['content'] = $this->content;
      	$values['idAuthor'] = $this->idAuthor;
      	$values['datePost'] = $this->datePost;
      	$values['dateAnswer'] = $this->dateAnswer;
      	$values['ipAuthor'] = $this->ipAuthor;
      	$values['idForum'] = $this->idForum;
      	$values['idPostParent'] = $this->idPostParent;
      	$values['nbViews'] = $this->nbViews;
      	$values['nbAnswers'] = $this->nbAnswers;
      	$values['idEditor'] = $this->idEditor;
      	$values['dateEdited'] = $this->dateEdited;
      	$values['ipEditor'] = $this->ipEditor;
      	$values['idPost'] = $this->id; 
	    
	    // Insert the values into the database
	    $this->id = $this->db->executeInsert( DB_PREXIX.'posts', $values );
  }
  
   /*
   * Update current post in the database
   * 
   */
  function update() {
	    $values = array();
	    $values['title'] = $this->title;
	    $values['content'] = $this->content;
      	$values['idAuthor'] = $this->idAuthor;
      	$values['datePost'] = $this->datePost;
      	$values['dateAnswer'] = $this->dateAnswer;
      	$values['ipAuthor'] = $this->ipAuthor;
      	$values['idForum'] = $this->idForum;
      	$values['idPostParent'] = $this->idPostParent;
      	$values['nbViews'] = $this->nbViews;
      	$values['nbAnswers'] = $this->nbAnswers;
      	$values['idEditor'] = $this->idEditor;
      	$values['ipEditor'] = $this->ipEditor;
      	$values['dateEdited'] = $this->dateEdited;
      	$values['idPost'] = $this->id; 
	    

	    // Insert the values into the database
	    $this->db->executeUpdate ( DB_PREXIX.'posts', $values, 'idPost = '.$this->id);
  }
  
  /*
   * Delete current post from database
   * */
   function delete() {
   		$this->db->executeDelete ( DB_PREXIX.'posts', 'idPost = '.$this->id);
   		$this->db->executeDelete ( DB_PREXIX.'posts', 'idPostParent = '.$this->id);
   		$this->authentificated = false;
   } 
   
   		
         	
   /*
    * Load answers of the post
    */
   function loadAnswers() {
   		$sql = 'SELECT '.DB_PREXIX.'posts.title,'.DB_PREXIX.'posts.content,'.DB_PREXIX.'posts.idauthor,'.DB_PREXIX.'posts.datepost,'.DB_PREXIX.'posts.dateanswer,'.DB_PREXIX.'posts.ipauthor,'.DB_PREXIX.'posts.idpostparent,'.DB_PREXIX.'posts.idforum,'.DB_PREXIX.'posts.nbviews,'.DB_PREXIX.'posts.nbanswers,'.DB_PREXIX.'posts.idpost,'.DB_PREXIX.'posts.ideditor,'.DB_PREXIX.'posts.dateedited,'.DB_PREXIX.'posts.ipeditor, author.login AS authorlogin, editor.login AS editorlogin  '.
  				' FROM '.DB_PREXIX.'posts ' .
                ' INNER JOIN '.DB_PREXIX.'users AS author ON '.DB_PREXIX.'posts.idAuthor = author.idUser '.
                ' LEFT JOIN '.DB_PREXIX.'users AS editor ON '.DB_PREXIX.'posts.idEditor = editor.idUser '.
				'WHERE '.DB_PREXIX.'posts.idPostParent = '.$this->id .' ORDER BY '.DB_PREXIX.'posts.idpost ASC ';
				
	    $dbAnswers = $this->db->getRecords ( $sql );
		foreach($dbAnswers as $i => $item) {
		$this->answers[] = 	new PostObject 	($item['title'], $item['content'], $item['idauthor'], $item['authorlogin'] , $item['datepost'],  $item['dateanswer'], $item['ipauthor'],
		  	$item['idpostparent'], $item['idforum'], $item['nbviews'], $item['nbanswers'] ,$item['idpost'], 
		  	$item['ideditor'], $item['editorlogin'], $item['dateedited'], $item['ipeditor']);		
		}   	
   }
}


class PostsLogic extends sqlObject {

	function PostsLogic() {
		//initialize parent construtor
  		$this->sqlObject();
  		
	}	
		
	
	/*
	 * Retrieve one user depending on id or name passed as parameter
	 * internal Use only
	 * */
	function _retrievePost($where) {
   		$sql = 'SELECT '.DB_PREXIX.'posts.title,'.DB_PREXIX.'posts.content,'.DB_PREXIX.'posts.idauthor,'.DB_PREXIX.'posts.datepost,'.DB_PREXIX.'posts.dateanswer,'.DB_PREXIX.'posts.ipauthor,'.DB_PREXIX.'posts.idpostparent,'.DB_PREXIX.'posts.idforum,'.DB_PREXIX.'posts.nbviews,'.DB_PREXIX.'posts.nbanswers,'.DB_PREXIX.'posts.idpost,'.DB_PREXIX.'posts.ideditor,'.DB_PREXIX.'posts.dateedited,'.DB_PREXIX.'posts.ipeditor,editor.login AS logineditor,author.login AS loginauthor '.
  				' FROM '.DB_PREXIX.'posts ' .
                ' INNER JOIN '.DB_PREXIX.'users AS author ON '.DB_PREXIX.'posts.idAuthor = author.idUser '.
                ' LEFT JOIN '.DB_PREXIX.'users AS editor ON '.DB_PREXIX.'posts.idEditor = editor.idUser '.
				' WHERE '.
            	$where;
	    $dbPosts = $this->db->getRecords ( $sql );

		if(count($dbPosts) == 1) {
			$dbPost = $dbPosts[0];
			return new  PostObject 	($dbPost['title'], $dbPost['content'], $dbPost['idauthor'], $dbPost['loginauthor'] , $dbPost['datepost'],$dbPost['dateanswer'], $dbPost['ipauthor'],
	  	$dbPost['idpostparent'], $dbPost['idforum'], $dbPost['nbviews'], $dbPost['nbanswers'] ,$dbPost['idpost'], 
	  	$dbPost['ideditor'], $dbPost['logineditor'], $dbPost['dateedited'], $dbPost['ipeditor']);
		}
		else {
			return null;	
		}
	}
	
	function retrievePostById($id) {
		return $this->_retrievePost(DB_PREXIX.'posts.idpost = '.addslashes($id));
	}
	
}	
?>
