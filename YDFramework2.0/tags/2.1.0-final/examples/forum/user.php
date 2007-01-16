<?php

    

    // Includes main class of the site
    require_once('fffRequest.php');

  
    // Include the classes needed on this page
        

    // Define the request class
    class user extends fffRequest {

        // Class constructor
        function user() {
            // Initialize the parent
            $this->fffRequest();

            // Indicate we require login
            //$this->setRequiresAuthentication( true );
        }

        function isActionAllowed() {
						$act = $this->getActionName();
						switch($act) {
							case "suscribe" :
							case "ViewProfil" :
								return true;
								break;
								
							case "MonProfil":
							case "DeleteProfil":
								if($this->user->authentificated) {
									return true;
								}
								else {
									return false;
								}
								break;
							default :
								return true;
						}
        	//echo $this->getActionName();
        }
     
        // The default action
        function actionDefault() {
      		$this->forward('monProfil');
      		return;			
        }


        function checkPass( $fields ) {
        	// L'utilisateur souhaite modifier son mot de passe ?
        	if(strlen($fields['profilePassUn'])>0|| strlen($fields['profilePassDeux'])>0) {
	        	if($fields['profilePassUn']==$fields['profilePassDeux']) {
	        		if(strlen($fields['profilePassUn'])>=4) {
		        		if(md5($fields['profilePass'])==$this->user->password) {
		        			return true;
		        		}
		        		else {
		        		   return array( 'profile' => t('user.oldpwdincorrect') );
		        		}	
	        		}
	        		else {
	        			return array( 'profile' => t('user.newpwd4chars'));
	        		}

	        	}
	        	else {
	        		return array( 'profile' => t('user.pwdsnotmatch') );
	        	}		
        	}
        	else {
        		return true;	
        	}
        }
        
        function checkSuscribe( $fields ) {
  			// check passwords
        	if(strlen($fields['suscribePassUn'])>0 || strlen($fields['suscribePassDeux'])>0) {
	        	if($fields['suscribePassUn']==$fields['suscribePassDeux']) {
	        		if(strlen($fields['suscribePassUn'])>=4) {
	        			if(strlen($fields['suscribeLogin'])>=4) {
	        				// check user don't already exist
	        				$userLogic = new UsersLogic();
	        				if($userLogic->doesLoginAlreadyExists($fields['suscribeLogin'])== false) {
  								return true;
  							}
  							else {
  								return array( 'suscribeLogin' => t('user.loginalready') );
  						    }
	        			}
	        			else {
	        				return array( 'suscribe' => t('user.pwd4chars') );
	        			}
	        		}
	        		else {
	        			return array( 'suscribe' => t('user.login4chars') );
	        		}
	        	}
	        	else {

	        		return array( 'suscribe' => t('user.pwdsnotmatch') );
	        	}		
        	}
        	else {
        		return array( 'suscribe' => t('user.2pwd') );
        	}
        }

		function actionViewProfil() {
			if(isset($_GET['userLogin'])) {
		        
	  			// load user's profile
	  			$userLogic = new UsersLogic();
	  			$user = $userLogic->retrieveUserByLogin($_GET['userLogin']);
	  			
	  			if($user!=null) {
	  				// Assign variables to the template
				    $this->actionTpl->assign( 'user' ,$user);
				    $this->actionTpl->assign( 'userfound',true);
				    $title = 'Profil de '.$user->login;
	  			}
	  			else {
	  				$this->actionTpl->assign( 'userfound',false);
	  				$title = 'Utilisateur inconnu';
	  			}
			    $content = new page($this->actionTpl->fetch('templates/user.viewprofile.tpl'), $title);
				// Display the action template into the master template
				$this->display($content);
			}
			else {
				$this->errorRequete();
				return;
			}
		}

   		function actionMonProfil() {

        	if($this->user->authentificated) {

	        	// Create the profile form
	            $form = new YDForm( 'profileForm' );
	            $form->addElement( 'textarea', 'profileDescription', t('user.desc'),array("cols" => 50, "rows" => 8) );
	            $form->addElement( 'password', 'profilePass', t('user.oldpwd') );
	            $form->addElement( 'password', 'profilePassUn', t('user.newpwd') );
	            $form->addElement( 'password', 'profilePassDeux', t('user.newpwd') );
	            $form->addElement( 'submit', 'cmdSubmit', t('user.saveupdate') );
			
							// default values
				      $form->setDefaults( array( 'profileDescription' => $this->user->description ) ); 
							$form->setDefaults( array( 'profilePass' => '' ) ); 
							$form->setDefaults( array( 'profilePassUn' => '' ) ); 
							$form->setDefaults( array( 'profilePassDeux' => '' ) ); 
								
	            // Add rules
	            $form->addFormRule( array( & $this, 'checkPass' ) );

	            // Process the form
	            if ( $form->validate() ) {

	                // Update profile
	                $newPass = $form->getValue( 'profilePassUn' );
									if(strlen($newPass)>0) {
										// Update description and password
										$this->user->description = $form->getValue( 'profileDescription' );
										$this->user->password = md5($form->getValue( 'profilePassUn' ));
									}
									else {
										// Update only description
										$this->user->description = $form->getValue( 'profileDescription' );
									}
				
									$this->user->update();
									$_SESSION['s_user'] = YDObjectUtil::serialize($this->user);
										
	                // Mark the form as valid
	                $this->actionTpl->assign( 'updated', true);            	
				
									$form->setDefaults( array( 'profilePass' => '' ) ); 
									$form->setDefaults( array( 'profilePassUn' => '' ) ); 
									$form->setDefaults( array( 'profilePassDeux' => '' ) ); 

	            }
	            // Assign variables to the template
	            $this->actionTpl->assign( 'form',$form->toArray());
	            $content = new page($this->actionTpl->fetch('templates/user.monprofile.tpl'), 'Mon profil');

        	}

        	else {
        		$content = new page($tpl->fetch('templates/user.notAuth.tpl'), t('global.notconnected') );
        	}

            // Display the action template into the master template
            $this->display($content);
        }


      function actionDeleteProfil() {
        	// Ask for a validation
        	if(isset($_GET['confirm']) && $_GET['confirm'] == 'true') {
        		// Suppression effective
        		$this->user->delete();
        		$_SESSION['s_user'] = YDObjectUtil::serialize($this->user);
        		$this->actionTpl->assign( 'confirmation',true);

        	}

        	else {
        		$this->actionTpl->assign( 'confirmation',false);
        	}

        	$content = new page($this->actionTpl->fetch('templates/user.delprofile.tpl'), t('user.delyraccount') );

          // Display the action template into the master template
          $this->display($content);

        }

        

        function actionSuscribe() {
			
        	if(!$this->user->authentificated) {
	        	// Create the profile form
	            $form = new YDForm( 'suscribeForm' );
							$form->addElement( 'text', 'suscribeLogin', t('user.login') );
	            $form->addElement( 'textarea', 'suscribeDescription', t('user.desc') );
	            $form->addElement( 'password', 'suscribePassUn', t('user.pwd') );
	            $form->addElement( 'password', 'suscribePassDeux', t('user.confirmpwd') );
	            $form->addElement( 'submit', 'cmdSubmit', t('user.valid') );

	            // Add rules
	            $form->addFormRule( array( & $this, 'checkSuscribe' ) );
	
	            // Process the form
	            if ( $form->validate() ) {

	                // Insert profile
	                $this->user->id = '';
        					$this->user->login = $form->getValue( 'suscribeLogin' );
        					$this->user->description = $form->getValue( 'suscribeDescription' );
        					$this->user->password = md5($form->getValue( 'suscribePassUn' ));
        					$this->user->insert();
        					$this->user->auth();
        					$_GET['idUser']	= $this->user->id;				
									$_SESSION['s_user'] = YDObjectUtil::serialize($this->user);
							
	                // Redirect sur les forums
	                header('location: forums.php');
        			return;
	
	            }
	            // Assign variables to the template
	            $this->actionTpl->assign( 'form', $form->toArray());
	            $content = new page($this->actionTpl->fetch('templates/user.suscribe.tpl'), 'Inscription');
	            // Display the action template into the master template
    	        $this->display($content);
				
        	}
        	else {
        		$this->actionMonProfil();
        		return;
        	}
        } 

    }


    YDInclude( 'YDF2_process.php' ); 

?>
