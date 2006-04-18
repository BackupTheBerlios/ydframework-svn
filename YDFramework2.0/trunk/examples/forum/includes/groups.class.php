<?php
/*
 * Fichier groups.class.php
 * Décrit les groups
 * 
 * */

require_once('sqlObject.class.php');


class GroupObject extends sqlObject {
  var $idGroup;
  var $name;
  var $rightsLevel;
  
  /*
   * Create a new group object from known datas
   * 
   * */
  
  function PostObject 	($idGroup, $name, $rightsLevel) {
  	
  		//initialize parent construtor
  		$this->sqlObject();

      	$this->idGroup = $idGroup;
      	$this->name = $name;
      	$this->rightsLevel = $rightsLevel;
  }
  
  /*
   * Insert current group in the database
   * 
   */
  function insert() {
	    $values = array();
	    
	    $values['idGroup'] = $this->idGroup;
	    $values['name'] = $this->name;
	    $values['rightsLevel'] = $this->rightsLevel;
	    
	    // Insert the values into the database
	    $this->id = $this->db->executeInsert( DB_PREXIX.'groups', $values );
  }
  
   /*
   * Update current group in the database
   * 
   */
  function update() {
	    $values = array();

	    $values['name'] = $this->name;
	    $values['rightsLevel'] = $this->rightsLevel;
	    	    
	    // Insert the values into the database
	    $this->db->executeUpdate ( DB_PREXIX.'groups', $values, 'idGroup = '.$this->idGroup);
  }
  
  /*
   * Delete current group from database
   * */
   function delete() {
   		$this->db->executeDelete ( DB_PREXIX.'groups', 'idGroup = '.$this->idGroup);
   } 
   
}

class GroupsLogic extends sqlObject {

	function GroupsLogic() {
		//initialize parent construtor
  		$this->sqlObject();
  		
	}
	
	/*
	 * Retrieve one group
	 * */
	function _retrieveGroup($where) {
  		$sql = 'SELECT * '.
  				' FROM '.DB_PREXIX.'groups ' .
				' WHERE '.
            	$where;
	    $dbPost = $this->db->getRecord ( $sql );
		
	    return new  GroupObject ($dbPost['idgroup'], $dbPost['name'], $dbPost['rightslevel']);
	
	}
	
	function retrieveGroupById($id) {
		return $this->_retrieveGroup(DB_PREXIX.'groups.idGroup = '.addslashes($id));
	}
	
	function listSelectGroups() {
  		$sql = 'SELECT * '.
		' FROM '.DB_PREXIX.'groups ';
    	
	    $dbGroups = $this->db->getRecords ( $sql );
		
		$groups = array();
		foreach($dbGroups as $i => $group) {
			$groups[''.$group['idgroup'].''] = $group['name'];
		}
	    return $groups;
	}
	function getGroups() {
  		$sql = 'SELECT * '.
		' FROM '.DB_PREXIX.'groups ';
    	
	    $dbGroups = $this->db->getRecords ( $sql );
		
		$groups = array();
		foreach($dbGroups as $i => $group) {
			$groups[''.$group['idgroup'].''] = new  GroupObject ($group['idgroup'], $group['name'], $group['rightslevel']);
		}
	    return $groups;
	}
}	
?>
