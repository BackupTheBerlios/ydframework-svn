<?php
/*
 * Fichier forums.class.php
 * Décrit un «sous»-forum et interragit avec la base de données
 * 
 * */

require_once('sqlObject.class.php');
require_once('posts.class.php');

class ForumObject extends sqlObject {
  var $id;
  var $name;
  var $order;
  
  var $nbPosts;
  var $nbSubjects;
  
  var $subjects;

  var $parity; //odd or even, used to alternate template
   		
  var $limiteBasse;
  var $limiteHaute;
  var $pas;
  
  /*
   * Create a new forum object from known datas
   * */
  
  function ForumObject($name, $order, $id=null) {
  		//initialize parent construtor
  		$this->sqlObject();
  		
  		$this->id = $id;
  		$this->name = $name;
  		$this->order = $order;
  		
  		$this->nbPosts=0;
 		$this->nbSubjects=0;
  		$this->subjects = array(); // empty at start, need to be loaded
  }
  
  /*
   * Insert current forum in the database
   * 
   */
  function insert() {
	    $values = array();
	    $values['nameForum'] = $this->name;
	    $values['`order`'] = $this->order;
	    
	    // Insert the values into the database
	    $this->id = $this->db->executeInsert( DB_PREXIX.'forums', $values );
  }
  
   /*
   * Update current user in the database
   * 
   */
  function update() {
	    $values = array();
	    $values['nameForum'] = $this->name;
		$values['`order`'] = $this->order;

	    // Insert the values into the database
	    $this->db->executeUpdate ( DB_PREXIX.'forums', $values, 'idforum = '.$this->id);
  }
  
  /*
   * Delete current forum from database
   * */
   function delete() {
   		// delete forum object
   		$this->db->executeDelete ( DB_PREXIX.'forums' , 'idforum = '.$this->id);
   		
   		// delete forum posts
   		$this->db->executeDelete ( DB_PREXIX.'posts' , 'idforum = '.$this->id);
   		
   		// delete forums authorisation
   		//$this->db->executeDelete ( DB_PREXIX.'users_forums_rights' , 'idforum = '.$this->id);
   }
  
  // Load number of subjects and of postst
  function loadNumbers() {
  		// count answers and posts
	  	$sql = ' SELECT COUNT(  idforum  )  AS nbposts ' .
	  			' FROM  `'.DB_PREXIX.'posts` ' .
	  			' WHERE idForum = '.$this->id.' ';  
	    $dbPosts = $this->db->getRecord ( $sql );
  	 	$this->nbPosts = $dbPosts['nbposts'];
			
		$sql = ' SELECT COUNT(  idpost  )  AS nbsubjects ' .
				' FROM  `'.DB_PREXIX.'posts` ' .
				' WHERE idForum = '.$this->id.' AND idPostParent IS NULL;';	
	    $dbSubjects = $this->db->getRecord ( $sql );
  	 	$this->nbSubjects = $dbSubjects['nbsubjects'];
	   	
  }
  
  // load subjects corresponding to a research
  function loadSubjectsMatching($keys) {
  	// So far, we research the plain keys, without decomposing words with spaces
  	
	$sql = 'SELECT idPostParent, idPost' .
			' FROM  `'.DB_PREXIX.'posts` ' .
			' WHERE  MATCH ( title, content ) ' .
			' AGAINST (  \''.addslashes($keys).'\' )';

 	$dbPostsId = $this->db->getRecords( $sql );
 	
 	// We retrieve parent subjects id in order to present subjects instead of all posts
 	$idSubjects = array();
 	foreach($dbPostsId as $i => $item) {
 		// post subject or post answers ?
 		if($item['idpostparent']==null) {
 			$idSubjects[''.$item['idpost'].''] = $item['idpost'];
 		}
 		else {
 			$idSubjects[''.$item['idpostparent'].''] = $item['idpostparent'];
 		}
 	}
    
    $nb = count($idSubjects);
    
    if($nb>0) {
	    // construct id clause query
	    $where = ' WHERE idpost IN (';
	    $j=0;
	
	    foreach($idSubjects as $i => $item) {
	    	$j++;
	    	if($j==$nb) {
	    	   $where .= ' '.$i.' ';
	    	}
	    	else {
	    	   $where .= ' '.$i.', ';
	    	}
	    }
	    $where .= ' )';
	    
	    
	    // retrieve subjects of matching posts
		$sql = 'SELECT '.DB_PREXIX.'posts.idpost, '.DB_PREXIX.'posts.title, '.DB_PREXIX.'users.login, '.DB_PREXIX.'users.idUser, '.DB_PREXIX.'posts.datePost, '.DB_PREXIX.'posts.dateAnswer, '.DB_PREXIX.'posts.nbViews, '.DB_PREXIX.'posts.nbAnswers '
	       .' FROM '.DB_PREXIX.'posts '
	       .' INNER JOIN '.DB_PREXIX.'users ON '.DB_PREXIX.'users.idUser = '.DB_PREXIX.'posts.idAuthor'
	       .$where;
    
	    $dbSubjects = $this->db->getRecords( $sql );
	 
	    foreach($dbSubjects as $i => $item) {
		  	// on charge un minimum d'informations sur les sujets
	  		$subject = new PostObject($item['title'], '', $item['iduser'], $item['login'],
	  			$item['datepost'],$item['dateanswer'], '', null, $this->id, $item['nbviews'],$item['nbanswers'], $item['idpost']);
	  		$subject->parity = $i%2?'odd':'even';
	        $this->subjects[] = $subject;
	      
	        $this->nbPosts += (1 + $item['nbanswers']);
	  	}
	   	$this->nbSubjects = count($this->subjects);
    }
  }
    
  
  /*
   *  Load specified subjects
   * 
   * @param $limitMin si the low limit
   * @param $number is the number of post to show
   */
  function loadSubjects($limitMin = 0, $number = NUMBER_SUBJECT_PER_PAGES) {
  	
  		// Get all posts informations
  		$sql = 'SELECT '.DB_PREXIX.'posts.idpost, '.DB_PREXIX.'posts.title, '.DB_PREXIX.'users.login, '.DB_PREXIX.'users.idUser, '.DB_PREXIX.'posts.datePost, '.DB_PREXIX.'posts.dateAnswer, '.DB_PREXIX.'posts.nbViews, '.DB_PREXIX.'posts.nbAnswers '
           .' FROM '.DB_PREXIX.'posts '
           .' INNER JOIN '.DB_PREXIX.'users ON '.DB_PREXIX.'users.idUser = '.DB_PREXIX.'posts.idAuthor'
           .' WHERE '.DB_PREXIX.'posts.idPostParent IS NULL AND '.DB_PREXIX.'posts.idForum = \''.$this->id.'\' ORDER BY '.DB_PREXIX.'posts.dateAnswer DESC';
     	$sql .= ' LIMIT '.$limitMin.','.$number.';';
      	      
     	$dbSubjects = $this->db->getRecords( $sql );
 
      	foreach($dbSubjects as $i => $item) {
	  		// on charge un minimum d'informations sur les sujets
	  		$subject = new PostObject($item['title'], '', $item['iduser'], $item['login'],
	  			$item['datepost'],$item['dateanswer'], '', null, $this->id, $item['nbviews'],$item['nbanswers'], $item['idpost']);
	  		$subject->parity = $i%2?'odd':'even';
	        $this->subjects[] = $subject;
	      
	        $this->nbPosts += (1 + $item['nbanswers']);
	  	}
	   	$this->nbSubjects = count($this->subjects);
	   	
	   	
	   	/*Check if it exists any posts after those getted*/
   		  	$sql = 'SELECT '.DB_PREXIX.'posts.idpost '
           .' FROM '.DB_PREXIX.'posts '
           .' WHERE '.DB_PREXIX.'posts.idPostParent IS NULL AND '.DB_PREXIX.'posts.idForum = \''.$this->id.'\' '
           .' LIMIT '.($limitMin+$number).',1;';

	     $dbSubjects = $this->db->getRecords( $sql );
	     if(count($dbSubjects)==1) {
	     	$this->limiteHaute = $limitMin+NUMBER_SUBJECT_PER_PAGES;
	     }
	     else {
	     	$this->limiteHaute = -1;
	     }
	     if($this->limiteHaute <= NUMBER_SUBJECT_PER_PAGES && $this->limiteHaute!=-1) {
	     	 $this->limiteBasse = -1;
	     }
	     else {
	     	$this->limiteBasse = $limitMin-NUMBER_SUBJECT_PER_PAGES>=0?$limitMin-NUMBER_SUBJECT_PER_PAGES:-1;	
	     }
	
	     $this->pas = NUMBER_SUBJECT_PER_PAGES;
    }
}


class ForumsLogic extends sqlObject {

	function ForumsLogic() {
		//initialize parent construtor
  		$this->sqlObject();	
	}
	
	
	/*
	 * Retrieve one forum depending on id
	 * internal Use only
	 * */
	function _retrieveForum($where) {
  		$sql = 'SELECT '.DB_PREXIX.'forums.idforum, '.DB_PREXIX.'forums.nameforum, '.DB_PREXIX.'forums.order ' .
  				' FROM '.DB_PREXIX.'forums WHERE '.
            	$where;
         
        $dbForum = $this->db->getRecord( $sql );

	    return new ForumObject($dbForum['nameforum'], $dbForum['order'], $dbForum['idforum']);
	}
	function retrieveForumById($id) {
		return $this->_retrieveForum(DB_PREXIX.'forums.idforum = '.$id);
	}

	
	/*
	 * Retrieve all forums with a specified order
	 * internal Use only
	 * */
	
	function _retrieveAll($orderSQL, $direction=true, $full=false) {
		$direction = $direction?' ASC':' DESC';
  		$sql = 'SELECT '.DB_PREXIX.'forums.idforum, '.DB_PREXIX.'forums.nameforum, '.DB_PREXIX.'forums.order' 
  		      .' FROM '.DB_PREXIX.'forums ' .
            $orderSQL.$direction;
        $dbForums = $this->db->getRecords( $sql );
	    $forums = array();
	    foreach($dbForums as $i => $item) {
  			$forum = new ForumObject($item['nameforum'], $item['order'], $item['idforum']);  
  			if($full) { $forum->loadSubjects(); } else { $forum->loadNumbers(); }
  			$forum->parity = $i%2?'odd':'even';
  			$forums["".$forum->id] = $forum;
		  }
		return $forums;
	}
	
	function retrieveAllByOrder($direction=true) {
		return $this->_retrieveAll('ORDER BY '.DB_PREXIX.'forums.order', $direction);	
	}
	
	function retrieveAllByOrderFull($direction=true) {
		return $this->_retrieveAll('ORDER BY '.DB_PREXIX.'forums.order', $direction, true);
	}
	function retrieveAllByOrderSimple($direction=true) {
		return $this->_retrieveAll('ORDER BY '.DB_PREXIX.'forums.order', $direction, false);
	}
}	
?>
