<?php


    // Includes main class of the site
    require_once('fffRequest.php');


    // Include the classes needed on this page
	require_once('includes/forums.class.php');
	require_once('includes/groups.class.php');
	

    // Define the request class
    class admin extends fffRequest {
		var $menusAdmin;
		
        // Class constructor
        function admin() {

            // Initialize the parent
            $this->fffRequest();
            
            // Indicate we require login
            $this->setRequiresAuthentication( true );
            
            $this->wrapperTpl->assign("viewadmin", true);

						$this->menusAdmin = ' Administration » ' .
	            		'<a class="fff_link1" href="admin.php?do=manageUsers">'.t('admin.users').' </a>' .
	            		' | '.
	            		'<a class="fff_link1" href="admin.php?do=manageForums">'.t('admin.forums').' </a>' .
	            		'';//' | ';
        }

		/*Overloading of isAutheticated, users, even registred need to be administrator to access this file*/
        function isAuthenticated() {
        	if($this->user->rightslevel >= LEVEL_ADMIN) {
        		return true;
        	}
        	else {
        		return false;
        	}
        }

        // The default action
        function actionDefault() {
						$this->forward('manageUsers');
						return;
        }

		function actionEditUser() {
			if(isset($_GET['userLogin'])) {
				
	
	  			// get the asked profile
	  			$userLogic = new UsersLogic();
	  			$user = $userLogic->retrieveUserByLogin($_GET['userLogin']);
	  			
	  			$groupsLogic = new GroupsLogic();
	  			$groups = $groupsLogic->listSelectGroups();
	  			
	  			if($user!=null) {
	  				
	  				// Create the addForum form
		            $form = new YDForm( 'editForm' );
		            
		            $form->addElement( 'textarea', 'userDescription', t('admin.desc'),array("cols" => 50, "rows" => 8) );
  							$form->addElement( 'select', 'userGroup', t('admin.usergroups') , array("width" => 60), $groups ); 
		  					$form->addElement( 'hidden' , 'iduser', $user->id );
	
		            $form->addElement( 'submit', 'cmdSubmit', t('admin.saveupdates') );
	
		            // Process the form
		            if ( $form->validate() ) {
		               // update
		               $user->description = $form->getValue( 'userDescription' );
		               $user->groupid = $form->getValue( 'userGroup' );

								   $user->update();
								   $this->forward('manageUsers');
								   return;		
		     				}
		            
								$form->setDefaults( array( 'userGroup' => $user->groupid ) );
								$form->setDefaults( array( 'userDescription' => $user->description ) );
					
			  				// Assign variables to the template
			  				$this->actionTpl->assign( 'form',$form->toArray());
						    $this->actionTpl->assign( 'user' ,$user);
						    $this->actionTpl->assign( 'userfound',true);
						    $title = 'Profil de '.$user->login;
					    
		  			}
		  			else {
		  				$this->actionTpl->assign( 'userfound',false);
		  				$title = 'Utilisateur inconnu';
		  			}
			    	$content = new page($this->actionTpl->fetch('templates/admin.user.edit.tpl'), $title, $this->menusAdmin);
						// Display the action template into the master template
						$this->display($content);
			}
			else {
				$this->errorRequete();
				return;
			}			
		}
		
		
        function actionManageUsers() {
            $usersLogic = new UsersLogic();
            
            // Assign variables to the template
            $temp = $usersLogic->retrieveAllByName();
            $this->actionTpl->assign( 'users', $temp);

            $content = new Page($this->actionTpl->fetch('templates/admin.users.list.tpl'), t('admin.manageusers'), $this->menusAdmin);


            // Display the action template into the master template
            $this->display($content);

        }

        
        function checkNewForum( $fields ) {
			if(strlen($fields['forumTitle'])>=5) {
				if($fields['forumPoids']>=0 && $fields['forumPoids']<=500) {
					return true;
	        	}
		        else {
		        	return array( 'search' => t('admin.forumweight') );
		        }
        	}
	        else {
	        	return array( 'search' => t('admin.forumtitle5char') );
	        }
        }
        
        function actionEditForum() {
        	if(isset($_GET['idforum'])) {

							// retrieve forum to edit
	            $forumLogic = new ForumsLogic();
	            $forum = $forumLogic->retrieveForumById($_GET['idforum']);

							// Create the addForum form
	            $form = new YDForm( 'editForumForm' );

	            $form->addElement( 'text', 'forumTitle', t('admin.newforumtitle') , array("size" => 40));
			  			$form->addElement( 'text', 'forumPoids', t('admin.forumweightorder') , array());
			  			$form->addElement( 'hidden' , 'idforum', $_GET['idforum'] );

	            $form->addElement( 'submit', 'cmdSubmit', t('admin.forumsave') );

	            // Add rules
	            $form->addFormRule( array( & $this, 'checkNewForum' ) );
	 						$form->addRule( 'forumPoids', 'numeric', t('admin.forumweightinteger') ); 

	 	         // defaults values
	            $form->setDefaults( array( 'forumTitle' => $forum->name ) ); 
	            $form->setDefaults( array( 'forumPoids' => $forum->order) ); 
       
	            // Process the form
	            if ( $form->validate() ) {
	               // get and show results
	               $forum->name = $form->getValue( 'forumTitle' );
	               $forum->order = $form->getValue( 'forumPoids' ); 
							   $forum->update();
							   $this->forward('manageForums');
							   return;		
	            }


	            // Assign variables to the template
	            $this->actionTpl->assign( 'form',$form->toArray());

	
	            $content = new Page($this->actionTpl->fetch('templates/admin.forum.edit.tpl'), t('admin.manageforums'), $this->menusAdmin);
	
	            // Display the action template into the master template
	            $this->display($content);
        	}
        	else {
							$this->errorRequete();        		
        	}
        }
        
        function actionManageForums() {

						// Create the addForum form
            $form = new YDForm( 'forumForm' );

            $form->addElement( 'text', 'forumTitle', t('admin.newforumtitle') , array("size" => 40));
  					$form->addElement( 'text', 'forumPoids', t('admin.forumweightorder') , array());
  			
            $form->addElement( 'submit', 'cmdSubmit', t('admin.forumcreate') );

            // Add rules
            $form->addFormRule( array( & $this, 'checkNewForum' ) );
 						$form->addRule( 'forumPoids', 'numeric', t('admin.forumweightinteger') ); 
 
            // Process the form
            if ( $form->validate() ) {
               // get and show results
               $forum = new ForumObject($form->getValue( 'forumTitle' ),$form->getValue( 'forumPoids' ));
			   				$forum->insert();

            }
            // Future defaults values
            $form->setDefaults( array( 'forumTitle' => '' ) ); 
            $form->setDefaults( array( 'forumPoids' => '' ) ); 
            
						// retrieve existing forums
            $forumLogic = new ForumsLogic();
            $forums = $forumLogic->retrieveAllByOrderSimple();     

            // Assign variables to the template
            $this->actionTpl->assign( 'form',$form->toArray());
            $this->actionTpl->assign('forums', $forums);

            $content = new Page($this->actionTpl->fetch('templates/admin.forums.list.tpl'), t('admin.manageforums') , $this->menusAdmin);

            // Display the action template into the master template
            $this->display($content);
        	
        }
        
        
        function actionDelForum() {
        	$forumLogic = new ForumsLogic();
            $forum = $forumLogic->retrieveForumById($_GET['idforum']);
            $forum->delete();
            $this->forward('manageForums');  
            return;
        	
        }
        

          function actionDelUser() {
            $usersLogic = new UsersLogic();
            $user = $usersLogic->retrieveUserById($_GET['iduser']);
            $user->delete();
      			$this->forward('manageUsers');
      			return;
          }
    } 

    YDInclude( 'YDF2_process.php' ); 

?>
