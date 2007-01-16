<?php
		require_once( 'includes/config.php' );
    // Include the Yellow Duck Framework

		require_once( YD_INIT_FILE );

    // Include the classes needed on all pages
    YDInclude( 'YDForm.php' ); 
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDTemplate.php' );
    require_once( 'includes/page.class.php' );
		require_once( 'includes/users.class.php' );

		
  // Define the constants we want to override for the Framework
  //YDConfig::set( 'YD_DEBUG', 1 );
	YDLocale::set( LOCALE );

	// Show all errors including notification of unset variables
	error_reporting(E_ALL);

    // Include the language file
    include_once( dirname( __FILE__ ) . '/languages/language_' . strtolower( YDLocale::get() ) . '.php' );

//	    /**
//     * The t function used for translations
//     * @param t Code of the word to translate
//     * @return t corresponding string for the current locale
//     * 
//     * For the moment the code must be in form module.word.or.more
//     * Every thing is currently in one file, could evolve if required
//     */
//    function t( $t ) {
//
//        // Return empty string when param is missing
//        if ( empty( $t ) ) {
//            return '';
//        }
//
//        // Load the language file
//        include_once( dirname( __FILE__ ) . '/languages/language_' . strtolower( YDLocale::get() ) . '.php' );
//        // Initialize the language array
//        if ( ! isset( $GLOBALS['t'] ) ) {
//            $GLOBALS['t'] = array();
//        }
//        
//        // Return the right translation
//        //$t = strtolower( $t ); // strtolower removed to improve perfs
//        return ( array_key_exists( $t, $GLOBALS['t'] ) ? $GLOBALS['t'][$t] : "%$t%" );
//    }
    
    /** 
		 * Template t modifier
     * @param params Parameters supplied by Smarty
       @return $params['w'] corresponding string for the current locale.
     */
    function YDTplModT( $params ) {
    		// strtolower removed to improve perfs.
        /*if ( @ $params['lower'] == 'yes' ) {
            return isset( $params['w'] ) ? strtolower( t( $params['w'] ) ) : '';
        } else {*/
            return isset( $params['w'] ) ? t( $params['w'] ) : '';
        //}
    }
	

    // Here, we define our custom request class
    class fffRequest extends YDRequest {
        var $template; // main template
        var $actionTpl; // action template
        var $user;
        
        // Class constructor
        function fffRequest() {

            // Initialize the parent request
            $this->YDRequest();
						
            // Make the template object available for all requests
            $this->wrapperTpl = new YDTemplate();  
						$this->actionTpl = new YDTemplate();        	 
						$this->wrapperTpl->register_function( 't', 'YDTplModT' );
        	  $this->actionTpl->register_function( 't', 'YDTplModT' );
												
						// create a new user object for the request
						if(isset($_SESSION['s_user'])) {
							$this->user = YDObjectUtil::unserialize($_SESSION['s_user']);
						}
						else {
							$this->user = new UserObject('Anonymous', '');
						}
						if($this->user->ip !=  $_SERVER['REMOTE_ADDR']) {
							$this->user->ip =  $_SERVER['REMOTE_ADDR'];
							$_SESSION['s_user'] = YDObjectUtil::serialize($this->user);
						}

        }
        
        function errorMissingAction() {
        		// TODO : return 404 in HTTP headers
        	 $this->actionTpl->assign( 'message',t('global.pagedoesnotexists') );
           $content = new Page($this->actionTpl->fetch('templates/errorApplication.tpl'), t('global.pagedoesnotexists') );

            // Display the action template into the master template
            $this->display($content);	
        }
        
        function errorActionNotAllowed() {
            $this->actionTpl->assign( 'message','Action non autorisée.');
            $content = new Page($this->actionTpl->fetch('templates/errorApplication.tpl'), t('global.erroractionnotallowed') );

            // Display the action template into the master template
            $this->display($content);	
        }
        
        function errorRequete($message=null) {
            
            $message = $message==null?t('global.unrecognizederror'):$message;            
            $actionTpl->assign( 'message',$message);
            $content = new Page($this->actionTpl->fetch('templates/errorApplication.tpl'), t('global.error').$message);

            // Display the action template into the master template
            $this->display($content);	
        }
        
        
        // function display, will display $content in master template
        function display($page) {
            $this->wrapperTpl->assign('user', $this->user);   
            $this->wrapperTpl->assign( 'menus', $page->menus);
            $this->wrapperTpl->assign( 'content' , $page->content!=null?$page->content:'');
            
            $this->wrapperTpl->assign( 'title' , $page->title!=null?$page->title:'FFF default title');

            $this->wrapperTpl->display( 'templates/fffRequest.tpl' );
        }
        
        
        // Check the authentication
        function isAuthenticated() {
        	return $this->user->authentificated;
        }


       // Function to check the login and log
        function checkLogin( $fields ) {
	        	$user = new userObject($fields['loginName'], md5($fields['loginPass']));	
				    $temp = $user->auth();
	
		  			// errors during authentification ?
		  			if(is_array($temp)) {
		  				return $temp;
		  			}
		  			else {
		  				// else log the user
		  				$this->user = $user;
		  				$_SESSION['s_user'] = YDObjectUtil::serialize($user);
		  				return true;
		  			}
        }
        

        // Login function
        function actionLogin() {

            // Redirect to default action if already logged in
            if ( $this->isAuthenticated() === true ) {
                $this->forward( 'default' );
                return;
            }

            // Create the login form
            $form = new YDForm( 'loginForm' );

            $form->addElement( 'text', 'loginName', t('user.login') );
            $form->addElement( 'password', 'loginPass', t('user.pwd') );
            $form->addElement( 'submit', 'cmdSubmit', 'Login' );

						$form->setDefaults( array( 'loginPass' => '' ) ); 
			
            // Add the rules
            $form->addFormRule( array( & $this, 'checkLogin' ) );

            // Process the form
            if ( $form->validate() ) {

                // Mark the form as valid
                $this->authenticationSucceeded();
                header('location: forums.php');
                return;
            }
                       
            // Assign variables to the action template
            $this->actionTpl->assign( 'form', $form->toArray());

            $content = new Page($this->actionTpl->fetch('templates/user.login.tpl'), '');

            // Display the action template into the master template
            $this->display($content);
  
        }


        // Function to logout
        function actionLogout() {
            session_destroy();
                      
            header('location: forums.php');
            return;
        }

        // Redirect to the login if the authentication failed
        function authenticationFailed() {
            $this->forward( 'login' );
            return;
        }

    }

?>
