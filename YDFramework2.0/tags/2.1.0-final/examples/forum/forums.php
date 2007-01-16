<?php

    // Includes main class of the site
    require_once('fffRequest.php');
    
    // Include the classes needed on this page
    require_once('includes/forums.class.php');
    require_once('includes/posts.class.php');



    // Define the request class
    class forums extends fffRequest {

        // Class constructor
        function forums() {
            // Initialize the parent
            $this->fffRequest();
        }

        // The default action
        function actionDefault() {
            $this->actionListForums();
            return;
        }       

        function checkSearch( $fields ) {
            if(strlen($fields['searchKeys'])>2) {
                return true;
            }
            else {
                return array( 'search' => t('forums.search3chars') );
            }
        }


        function actionSearch() {
       
            // retrieve list of forums
            $forumLogic = new ForumsLogic();
            $forums = $forumLogic->retrieveAllByOrderSimple();  
            $selectForums = array();
            foreach($forums as $i=> $item) {
                $selectForums["".$item->id] = $item->name;
            }
                        
            // Create the search form
            $form = new YDForm( 'searchForm' );

            $form->addElement( 'text', 'searchKeys', t('forums.searchstring') , array("size" => 40));
            $form->addElement( 'select', 'searchForum', t('forums.searchforum'), array("width" => 60), $selectForums ); 

            $form->addElement( 'submit', 'cmdSubmit', t('forums.search') );

            // Add rules
            $form->addFormRule( array( & $this, 'checkSearch' ) );


            // Process the form

            if ( $form->validate() ) {
                $this->actionTpl->assign( 'searched',true);
             // get and show results
             $forum = $forumLogic->retrieveForumById($form->getValue( 'searchForum' )); 
                   $forum->loadSubjectsMatching($form->getValue( 'searchKeys' ));
                                    
                   // number of results
                   $this->actionTpl->assign( 'nbposts' , count($forum->subjects) );
                   $this->actionTpl->assign( 'posts' , $forum->subjects );

            }
            
            // Assign variables to the template
            $this->actionTpl->assign( 'form',$form->toArray());
            $content = new page($this->actionTpl->fetch('templates/forums.search.tpl'), t('forums.searching') );
    
    				$this->display($content);        	
        }

        function actionListForums() {

						
            // Retrieve all forums in Database
            $forumLogic = new ForumsLogic();
            $forums = $forumLogic->retrieveAllByOrderSimple();     

            // Assign variables to the template
            $this->actionTpl->assign('forums', $forums);
            $content = $this->actionTpl->fetch('templates/forums.list.tpl');

            // Display the action template into the master template
            $this->display(new Page($content, t('global.forumlist') ) );

        }
      

        function actionViewForum() {

            // Retrieve one forum with all subjects
            $forumLogic = new ForumsLogic();
            $forum = $forumLogic->retrieveForumById($_GET['id']);

            if(isset($_GET['from']) && is_numeric($_GET['from']) ) {
                  $forum->loadSubjects($_GET['from']);
            }
            else {
                  $forum->loadSubjects();
            }
  
            // Assign variables to the template
            $this->actionTpl->assign('user', $this->user);
            $this->actionTpl->assign('forum', $forum);
            $this->actionTpl->assign('posts', $forum->subjects);            

            $content = $this->actionTpl->fetch('templates/forums.list.subjects.tpl');

			     $menus = ' » ' .''.$forum->name.'';

            // Display the action template into the master template
            $this->display(new Page($content, $forum->name ,$menus));

        }

    } 



    YDInclude( 'YDF2_process.php' ); 


?>
