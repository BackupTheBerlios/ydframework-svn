<?php
    
    // Includes main class of the site
    require_once('fffRequest.php');
    
    // Include the classes needed on this page
    // .... YDInclude( 'YDRequest.php' ); 
    require_once('includes/posts.class.php');
 	require_once('includes/forums.class.php');
 	
    // Define the request class
    class posts extends fffRequest {

        // Class constructor
        function posts() {
            // Initialize the parent
            $this->fffRequest();

        }

        // The default action
        function actionDefault() {
            header('location: forums.php');
            return;
        }
        
        
        function actionViewPost() {
           
            // Assign variables to the template
			
						$postlogic = new PostsLogic();
						$post = $postlogic->retrievePostById($_GET['id']);
						$post->nbViews++;
						$post->update();
						$post->loadAnswers();
						
						$forumLogic = new ForumsLogic();
            $forum = $forumLogic->retrieveForumById($post->idForum);
            
            $menus = ' » ' .
            		'<a class="fff_link1" href="forums.php?do=viewForum&amp;id='.$forum->id.'">'.$forum->name.'</a>' .
            		' » '.$post->title;
                        
            $this->actionTpl->assign( 'post', $post);
            $this->actionTpl->assign('user', $this->user);
            $this->actionTpl->assign('forum', $forum);

            $content = new Page($this->actionTpl->fetch('templates/posts.view.tpl'), $post->title, $menus);

            // Display the action template into the master template
            $this->display($content);
        }
        
        function checkPost( $fields ) {
        	// Controle de l'utilisateur, on ne peut pas poster deux messages trop rapidement
        	if(isset($_SESSION['s_user_post_time'])) {
        		if(($_SESSION['s_user_post_time']+SECONDS_BETWEEN_POSTS) > mktime()){
        			return array( 'timing' => t('posts.toorapid') );
        		}
        	}
        	// les deux champs sont-ils replis
        	if(strlen($fields['postTitle'])>2 && strlen($fields['postContent'])>2) {
				return true;
        	}
	        else {
	        	return array( 'profile' => t('posts.tooshort') );
	        }
       	}
        function simpleCheckPost( $fields ) {
        	// les deux champs sont-ils replis
        	if(strlen($fields['postTitle'])>2 && strlen($fields['postContent'])>2) {
				return true;
        	}
	        else {
	        	return array( 'profile' => t('posts.tooshort') );
	        }	
        }
        
        
        
        function actionRepondre() {
        	$this->forward('AddPost');
        }
        
        
        function actionAddPost() {
        	if(!isset($_GET['idForum'])) {
        		$this->errorRequete();
        		return;
        	}
        	
        	if($this->user->authentificated) {
						$forumLogic = new ForumsLogic();
						$forum = $forumLogic->retrieveForumById($_GET['idForum']);
						
				
	        	// Create the add post form
	          $form = new YDForm( 'postAdd' );
	            
						$form->addElement( 'hidden' , 'idForum', $_GET['idForum'] );
		
						$form->addElement( 'text', 'postTitle', t('posts.messagetitle') , array("size" => 60));
	          $form->addElement( 'textarea', 'postContent', t('posts.message') , array("cols" => 50, "rows" => 8));
	          $form->addElement( 'submit', 'cmdSubmit', t('posts.addmessage') );

						if(isset($_GET['idPostParent'])) {
							$postLogic = new PostsLogic();
							$postParent = $postLogic->retrievePostById($_GET['idPostParent']);
							$form->addElement( 'hidden' , 'idPostParent', $postParent->id);
						}
						else {
							$postParent = null;
						}

						// nouveau post ou réponse ?
						if($postParent==null) {
							$titre =  t('posts.newsubject');
							
							$menus = ' » ' .
			            		'<a class="fff_link1" href="forums.php?do=viewForum&amp;id='.$forum->id.'">'.$forum->name.'</a>' .
			            		' » '.$titre;
						}
						else {
							$titre =   t('posts.newanswer');
							
							$menus = ' » ' .
			            		'<a class="fff_link1" href="forums.php?do=viewForum&amp;id='.$forum->id.'">'.$forum->name.'</a>' .
			            		' » '.$titre;
						}
				
	          // Add rules
	          $form->addFormRule( array( & $this, 'checkPost' ) );
	
	          // Process the form
					  if ( $form->validate() ) {
									$idPP = isset($_GET['idPostParent'])?$_GET['idPostParent']:null;
									
									// Insère le poste			
									$post = new PostObject 	($form->getValue( 'postTitle' ), $form->getValue( 'postContent' ), $this->user->id , $this->user->login , date('Y-m-d H:i:s', mktime()),date('Y-m-d H:i:s', mktime()), $this->user->ip,
					  					$idPP, $_GET['idForum'], 0, 0);
									$post->insert();
														
									// Verrouillage de l'utilisateur avant de pouvoir poster un nouveau post
									$_SESSION['s_user_post_time'] = mktime();
									
									// affiche la page de confirmation
					        $this->actionTpl->assign( 'insert', true);
	                
	                // on redirige sur le post parent si il existe
	                if($postParent!=null) {
			            		// on incrémente le nombre de réponses
			            		$postParent->nbAnswers++;
			            		$postParent->dateAnswer = date('Y-m-d H:i:s', mktime());
			            		$postParent->update();
			                $this->actionTpl->assign( 'post', $postParent);
	                }
	                else {
	                	$this->actionTpl->assign( 'post', $post);
	                }
					
									$content = new page($this->actionTpl->fetch('templates/posts.success.tpl'), t('posts.success'), $menus);
									$this->display($content);
	               	return;
	            }
	            
							$form->setDefaults( array( 'postIdForum' => $_GET['idForum'] ) ); 
							if($postParent!=null) {
								$form->setDefaults( array( 'postTitle' => t('posts.replyprefix').$postParent->title ) ); 
							}
		
	            // Assign variables to the template
	            $this->actionTpl->assign( 'form',$form->toArray()); // forms
			 				$this->actionTpl->assign( 'forum',$forum); // forum for displaying name
			 				$this->actionTpl->assign( 'user',$this->user); // user for rights

	            $content = new page($this->actionTpl->fetch('templates/posts.new.tpl'),$titre, $menus);

        	}

        	else {

        		$content = new page($this->actionTpl->fetch('templates/user.notAuth.tpl'), t('global.notconnected') );

        	}



            // Display the action template into the master template

            $this->display($content);
    
       }
        
       function actionEditPost() {
        	if(!isset($_GET['idPost'])) {
        		$this->errorRequete();
        		return;
        	}
        	
			if($this->user->authentificated) {
				$postlogic = new PostsLogic();
				$post = $postlogic->retrievePostById($_GET['idPost']);
				if($post!=null) {
					if($this->user->rightslevel>=LEVEL_MODERATOR || $this->user->id == $post->idAuthor) {
									
			        	// Create the add post form
			            $form = new YDForm( 'postEdit' );
			            
									$form->addElement( 'hidden' , 'idPost', $post->id);
				
									$form->addElement( 'text', 'postTitle', t('posts.messagetitle') , array("size" => 60));
			            $form->addElement( 'textarea', 'postContent', t('posts.message'), array("cols" => 50, "rows" => 8));
			            $form->addElement( 'submit', 'cmdSubmit', t('posts.editmessage') );
				
									// Add rules
			            $form->addFormRule( array( & $this, 'simpleCheckPost' ) );
			
			            // Process the form
			            if ( $form->validate() ) {
			            	$post->title = $form->getValue( 'postTitle' );
			            	$post->content = $form->getValue( 'postContent' );
			            	$post->idEditor = $this->user->id;
			            	$post->ipEditor = $this->user->ip;
			            	$post->dateEdited = date('Y-m-d H:i:s', mktime());
			            	
			            	$post->update();
			            	 // on redirige sur le post parent si il existe
			                if($post->idPostParent!=null) {
			                	$postParent = $postlogic->retrievePostById($post->idPostParent);
			            					                	
			            			$postParent->dateAnswer = date('Y-m-d H:i:s', mktime());
			            			$postParent->update();
			                	$this->actionTpl->assign( 'post', $postParent);
			                }
			                else {
			                	$this->actionTpl->assign( 'post', $post);
			                }
											$this->actionTpl->assign( 'editMessage',true); // we are editing the message
											$content = new page($this->actionTpl->fetch('templates/posts.success.tpl'), t('posts.updsuccess') ) ;
											$this->display($content);
			               	return;
			            }
				
									$form->setDefaults( array( 'postTitle' => $post->title ) ); 
									$form->setDefaults( array( 'postContent' => $post->content ) ); 
								     
								  // Assign variables to the template
						      $this->actionTpl->assign( 'form',$form->toArray()); // forms
					 				$this->actionTpl->assign( 'user',$this->user); // user for rights
									$this->actionTpl->assign( 'editMessage',true); // we are editing the message
		
			            $content = new page($this->actionTpl->fetch('templates/posts.new.tpl'),t('posts.messageupdate') );
		
									$this->display($content);
		              return;
					}
					else {
						$this->errorRequete(t('posts.notrightupdate'));
        		return;
					}
				}
				else {
					$this->errorRequete(t('posts.donotexists'));
        	return;
				}
			}
			else {
				$content = new page($this->actionTpl->fetch('templates/user.notAuth.tpl'), t('global.notconnected') );
			}
      } 
        
        
        
      function actionDelPost() {
      		if(isset($_GET['idPost'])) {
      			
			
					$postlogic = new PostsLogic();
					$post = $postlogic->retrievePostById($_GET['idPost']);
					if($post!=null) {
	
							$forumLogic = new ForumsLogic();
		          $forum = $forumLogic->retrieveForumById($post->idForum);
					

		        	if($this->user->rightslevel >= LEVEL_MODERATOR) {
		        		
		        		if($post->idPostParent!=null) {
									$postParent = $postlogic->retrievePostById($post->idPostParent);
									$postParent->nbAnswers--;
								}
								$forum->nbPosts -= 1;
														
								$post->delete();
								
								$menus = ' » ' .
			            		'<a class="fff_link1" href="forums.php?do=viewForum&amp;id='.$forum->id.'">'.$forum->name.'</a>' .
			            		' » '.t('posts.delmessage');
						
								// affiche l'opération a été effectuée
								$this->actionTpl->assign( 'post', $post);
								
								$content = new page($this->actionTpl->fetch('templates/posts.del.tpl'), t('posts.messagedeleted') , $menus);
								$this->display($content);
				        return;
			    
		        	}
		        	else {
		        		$this->errorActionNotAllowed();
		        		return;
		        	}
				}
				else {
					$this->errorRequete();
      		return;	
				}
      		}
      		else {
      			$this->errorRequete();
      			return;	
      		}
        }
    } 

    YDInclude( 'YDF2_process.php' ); 

?>
