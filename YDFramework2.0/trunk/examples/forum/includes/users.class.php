<?php
/*
 * Fichier users.class.php
 * Décrit l'utilisateur du forum et interragit avec la base de données
 * 
 * */

require_once('sqlObject.class.php');

class UserObject extends sqlObject {
  var $id;
  var $login;
  var $password; //md5 password
  var $authentificated; // boolean
  var $description;
  var $ip;
  
  var $groupid;
  var $groupname;
  var $rightslevel; // int


  /*
   * Create a new user object from known datas
   * @param password in md5 format
   * */
  
  function UserObject($login, $password, $id=null, $description = '', $rightslevel = LEVEL_ANONYMOUS, $groupname ='', $ip=null, $groupid = GROUP_SIMPLEUSER_ID) {
  		//initialize parent construtor
  		$this->sqlObject();
  		
  		$this->id = $id;
		$this->login = $login;
		$this->password = $password;
		$this->authentificated = false;
		$this->description = $description;
		$this->rightslevel = $rightslevel;

		$this->groupid = $groupid;
		$this->groupname = $groupname;
		$this->ip = $ip;
  }
  
  /*
   * Insert current user in the database
   * 
   */
  function insert() {
	    $values = array();
	    $values['login'] = $this->login;
	    $values['password'] = $this->password;
	    $values['description'] = $this->description;
	    
	    // Insert the values into the database
	    $this->id = $this->db->executeInsert( DB_PREXIX.'users', $values );
	    
	    $values = array();
	    $values['idGroup'] = $this->groupid;
	    $values['idUser'] = $this->id;
	    $this->db->executeInsert ( DB_PREXIX.'groups_users', $values);
  }
  
   /*
   * Update current user in the database
   * 
   */
  function update() {
	    $values = array();
	    $values['description'] = $this->description;
	    $values['password'] = $this->password;

	    // Update the values into the database
	    $this->db->executeUpdate ( DB_PREXIX.'users', $values, 'iduser = '.$this->id);   	    
	    $this->db->executeUpdate ( DB_PREXIX.'groups_users', array('idGroup'=>$this->groupid), 'iduser = '.$this->id);
  }
  
  /*
   * Delete current user from database, in fact we set group to inactive users. Users are never deleted.
	 */
   function delete() {
	    $this->db->executeUpdate ( DB_PREXIX.'groups_users', array('idGroup'=>GROUP_INACTIVEUSER_ID), 'iduser = '.$this->id);;
   		$this->authentificated = false;
   }
  
  function auth() {
  	if(isset($this->login) && isset($this->password) && strlen($this->login)>0 && strlen($this->password)>0) {
  		// Get password in db 
  		$sql = 'SELECT password, rightsLevel, '.DB_PREXIX.'groups.name, '.DB_PREXIX.'groups.idGroup, '.DB_PREXIX.'users.idUser, '.DB_PREXIX.'users.description '
             .' FROM '.DB_PREXIX.'users '
             .' INNER JOIN '.DB_PREXIX.'groups_users ON '.DB_PREXIX.'groups_users.idUser = '.DB_PREXIX.'users.idUser '
             .' INNER JOIN '.DB_PREXIX.'groups ON '.DB_PREXIX.'groups_users.idGroup = '.DB_PREXIX.'groups.idGroup '
             .' WHERE '.DB_PREXIX.'users.login = \''.$this->login.'\'';
	
	    $result = $this->db->getRecord( $sql );

	    if(isset($result) && $result['password'] == $this->password && $result['rightslevel'] > 0) {
	      	$this->id = $result['iduser'];
	      	$this->password = $result['password'];
	      	$this->rightslevel = $result['rightslevel'];
	      	$this->authentificated = true;
		    	$this->description = $result['description'];
		    	$this->groupname = $result['name'];
		    	$this->groupid = $result['idgroup'];
	    	

	    	return true;
	    }
	    else {
	    	return array( 'login' => 'Utilisateur et/ou mot de passe incorrect' );
	    }
	    return true;
  	}
  	else {
		return array( 'login' => 'Veuillez renseigner les champs «login» et «mot de passe» avant de valider. ' );
  	}
  }
  
  
}


class UsersLogic extends sqlObject {

	function usersLogic() {
		//initialize parent construtor
  		$this->sqlObject();
  		
	}	
	
	/*
	 * Retrieve one user depending on id or name passed as parameter
	 * internal Use only
	 * */
	function _retrieveUser($where) {
  		$sql = 'SELECT '.DB_PREXIX.'groups.name AS groupe, '.DB_PREXIX.'groups.idGroup, '.DB_PREXIX.'groups.rightslevel, '.DB_PREXIX.'users.idUser, '.DB_PREXIX.'users.login, '.DB_PREXIX.'users.password, '.DB_PREXIX.'users.description, '.DB_PREXIX.'users.login '.
  				' FROM '.DB_PREXIX.'users ' .
                ' INNER JOIN '.DB_PREXIX.'groups_users ON '.DB_PREXIX.'groups_users.idUser = '.DB_PREXIX.'users.idUser '.
			    ' INNER JOIN '.DB_PREXIX.'groups ON '.DB_PREXIX.'groups_users.idGroup = '.DB_PREXIX.'groups.idGroup ' . 
				'WHERE '.
            	$where;

	    $dbUsers = $this->db->getRecords ( $sql );	

	    if(count($dbUsers)==1) {
			$dbUser = $dbUsers[0];
	    	return new UserObject($dbUser['login'], $dbUser['password'], $dbUser['iduser'], $dbUser['description'], $dbUser['rightslevel'], $dbUser['groupe'], '', $dbUser['idgroup']);
	    }
	    else {
	    	return null;
	    }
	}
	function doesLoginAlreadyExists($login) {
  		$sql = 'SELECT  '.DB_PREXIX.'users.idUser, '.DB_PREXIX.'users.login, '.DB_PREXIX.'users.password, '.DB_PREXIX.'users.description, '.DB_PREXIX.'users.login '.
  				' FROM '.DB_PREXIX.'users ' .
				'WHERE '.DB_PREXIX.'users.login = \''.addslashes($login).'\'';
	    $dbUser = $this->db->getRecords( $sql );	
	    if(count( $dbUser ) == 1 ) {
	    	return true;
	    }
	    else {
	    	return false;	
	    }
	}
	
	function retrieveUserById($id) {
		return $this->_retrieveUser(DB_PREXIX.'users.iduser = '.$id);
	}
	function retrieveUserByLogin($login) {
		return $this->_retrieveUser(DB_PREXIX.'users.login = \''.$login.'\'');	
	}
	

	
	/*
	 * Retrieve all users with a specified order
	 * internal Use only
	 * */
	
	function _retrieveAll($orderSQL, $direction=true) {
		$direction = $direction?' ASC':' DESC';
  		$sql = 'SELECT '.DB_PREXIX.'groups.name AS groupe, '.DB_PREXIX.'groups.idgroup, '.DB_PREXIX.'groups.rightslevel, '.DB_PREXIX.'users.idUser, '.DB_PREXIX.'users.login, '.DB_PREXIX.'users.password, '.DB_PREXIX.'users.description, '.DB_PREXIX.'users.login '
             .' FROM '.DB_PREXIX.'users '
             .' INNER JOIN '.DB_PREXIX.'groups_users ON '.DB_PREXIX.'groups_users.idUser = '.DB_PREXIX.'users.idUser '.
			' INNER JOIN '.DB_PREXIX.'groups ON '.DB_PREXIX.'groups_users.idGroup = '.DB_PREXIX.'groups.idGroup ' .
            $orderSQL.$direction;
		$dbUsers = $this->db->getRecords( $sql );

		$users = array();
		foreach($dbUsers as $i => $item) {
			$users[] = new UserObject($item['login'], $item['password'], $item['iduser'], $item['description'], $item['rightslevel'], $item['groupe'], '', $item['idgroup']);  
		}
	    return $users;
	}
	
	function retrieveAllById($direction=true) {
		return $this->_retrieveAll('ORDER BY '.DB_PREXIX.'users.idUser', $direction);	
	}
	
	function retrieveAllByName($direction=true) {
		return $this->_retrieveAll('ORDER BY '.DB_PREXIX.'users.login', $direction);	
	}
	
	function retrieveAllByGroup($direction=true) {
		return $this->_retrieveAll('ORDER BY '.DB_PREXIX.'groups.name', $direction);	
	}
	
}	
?>
