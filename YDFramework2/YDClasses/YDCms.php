<?php

	// Includes
	require_once( 'config.php' );
	require_once( 'YDRequest.php' );
	require_once( 'YDDatabase.php' );
	require_once( 'YDFileSystem.php' );
	require_once( 'YDBrowserInfo.php' );

	// Class definition
	class YDCmsBaseRequest extends YDRequest {

		// Class constructor
		function YDCmsBaseRequest() {

			// Initialize the parent
			$this->YDRequest();

			// Determine the base directory
			$this->basedir = dirname( YD_SELF_SCRIPT ) . '/';
			$this->setVar( 'basedir', $this->basedir );

			// Determine the base url
			$this->baseurl = 'http://' . strtolower( $_SERVER['SERVER_NAME'] );
			if ( $_SERVER['SERVER_PORT'] != '80' ) {
				$this->baseurl = $this->baseurl . ':' . $_SERVER['SERVER_PORT'];
			}
			$this->baseurl = $this->baseurl . $this->basedir;
			$this->setVar( 'baseurl', $this->baseurl );

			// Connect to the database
			$this->_connectToDatabase();

			// Load the configuration
			$this->_loadConfig();

			// Determine the language
			$this->_negotiateLanguage();

			// Load the language string
			$this->_loadStrings();

			// Add the version
			$this->setVar( 'YD_CMS_NAMEVERS', YD_FW_NAME . ' CMS 1.0' );
			$this->setVar( 'YD_CMS_HOMEPAGE', YD_FW_HOMEPAGE . 'wiki/YDCms' );

		}

		// Check the authentication
		function isAuthenticated() {
			if (
				! empty( $_COOKIE[ $this->config['site_id'] . '_user' ] )
				&&
				! empty( $_COOKIE[ $this->config['site_id'] . '_pass' ] )
			) {
				$fields = array(
					'loginName' => $_COOKIE[ $this->config['site_id'] . '_user' ],
					'loginPass' => $_COOKIE[ $this->config['site_id'] . '_pass' ],
				);
				if ( $this->checkLogin( $fields, true ) === true ) {
					$this->username = $_COOKIE[ $this->config['site_id'] . '_user' ];
					$this->authenticationSucceeded();
					return true;
				}
			}
			return false;
		}

		// Output the template
		function prepareOutput( $templateName='' ) {

			// Add the browser name
			if ( $this->browser->getBrowser() == 'ie' ) {
				$this->setVar( 'browser', 'ie' );
			} else {
				$this->setVar( 'browser', 'ns' );
			}

			// Add the version
			$this->setVar( 'YD_CMS_NAME_VERS', YD_FW_NAME . ' CMS 1.0' );
			$this->setVar( 'YD_CMS_HOMPAGE', YD_FW_HOMEPAGE . 'wiki/YDCms' );

		}

		// Output the template
		function output( $templateName='' ) {

			// Display the template output
			echo( $this->getOutput( $templateName ) );

		}

		// Function to get the output of the template
		function getOutput( $templateName='' ) {

			// Prepare the output
			$this->prepareOutput();

			// Add the number of SQL queries
			$this->setVar( 'elapsed', $GLOBALS['timer']->getElapsed() );
			$this->setVar( 'isAuthenticated', $this->isAuthenticated() );
			$this->setVar( 'sqlCount', $this->db->getSqlCount() );

			// Get the right template name
			if ( empty( $templateName ) ) {
				$templateName = basename( YD_SELF_FILE, YD_SCR_EXT );
			}

			// Return the template output
			return $this->getTemplateOutput( $templateName );

		}

		// Connect to the database
		function _connectToDatabase() {

			// Only if no database yet
			if ( ! isset( $this->db ) ) {

				// Get the config values
				$dbCfg = & $GLOBALS['db'];

				// Make the connection
				$this->db = new YDDatabase( $dbCfg['type'], $dbCfg['db'], $dbCfg['user'], $dbCfg['pass'], $dbCfg['host'] );
				$this->db->connect();

				// Set the prefix
				$this->dbPrefix = $dbCfg['prefix'] . '_';

			}

		}

		// Load the configuration
		function _loadConfig() {

			// Only if no config yet
			if ( ! isset( $this->config ) ) {

				// Get the configuration
				$this->config = $GLOBALS['cfg'];

				// Load the config from the database
				$config = $this->db->getAsAssocArray(
					'SELECT ConfigName, ConfigValue FROM ' . $this->dbPrefix . 'config', 'ConfigName', 'ConfigValue'
				);
				$this->config = array_merge( $this->config, $config );

				// Add the site variables
				$this->template->_vars = array_merge( $this->template->_vars, $this->config );

			}

		}

		// Determine the language for the request
		function _negotiateLanguage() {

			// Only if no database yet
			if ( ! isset( $this->languages ) ) {

				// Get the language codes
				$this->languages = array();
				foreach ( $this->config['site_languages'] as $key=>$lang ) {
					if ( $key < 4 ) {
						$this->languages[ strtolower( $lang[0] ) ] = array(
							'name'=>$lang[1], 'idx'=>$key+1, 'url'=>$this->basedir . 'language.php?lang=' . $lang[0]
						);
					}
				}

				// Get the browser language
				$this->browser = new YDBrowserInfo();
				$this->language = $this->browser->getLanguage( array_keys( $this->languages ) );

				// Check the cookies
				if ( isset( $_COOKIE[ $this->config['site_id'] . '_lang'] ) ) {
					if ( in_array(
						strtolower( $_COOKIE[ $this->config['site_id'] . '_lang' ] ), array_keys( $this->languages )
					) ) {
						$this->language = $_COOKIE[ $this->config['site_id'] . '_lang' ];
					}
				}

				// Check the url
				if ( isset( $_GET['lang'] ) ) {
					if ( in_array( strtolower( $_GET['lang'] ), array_keys( $this->languages ) ) ) {
						$this->language = $_GET['lang'];
					}
				}

				// Save the language in a cookie
				$this->_saveLanguage( $this->language );

				// Get the language index
				$this->languageIdx = $this->languages[ $this->language ]['idx'];

				// Add it to the template
				$this->setVar( 'site_lang', $this->language );
				$this->setVar( 'site_languages', $this->languages );

			}

		}

		// Save the language in a cookie
		function _saveLanguage( $language ) {
			setcookie( $this->config['site_id'] . '_lang', strtolower( $language ), time() + 31536000, '/' );
			setlocale( LC_ALL, strtolower( $language ) . '_' . strtoupper( $language ) );
		}

		// Load the strings
		function _loadStrings() {

			// Only if no database yet
			if ( ! isset( $this->strings ) ) {

				// Include the language file
				@include( 'skins/' . $this->config['site_skin'] . '/strings_' . $this->language  . '.php' );

				// Start with no strings
				$this->strings = array();
				if ( isset( $GLOBALS['translations'] ) ) {
					$this->strings = $GLOBALS['translations'];
					$this->template->_vars = array_merge( $this->template->_vars, $GLOBALS['translations'] );
				}

			}

		}

		// Convert from BBCode
		function convertFromBBCode( $string ) {
			require_once( 'YDBBCode.php' );
			$parser = new YDBBCode();
			$parser->addRule( "/\[img=([^<> \n]+?)\](.+?)\[\/img\]/i", '<a href="\\1"><img border="1" src="\\2"></a>' );
			$parser->addRule( "/\[img\]([^<> \n]+?)\[\/img\]/i", '<img border="1" src="\\1">' );
			return $parser->toHtml( $string, true, false );
		}

		// Function to check the login
		// TODO: needs to fetch the complete record and assign it to the username variable as an array. This should also
		//		 be added to the template.
		function checkLogin( $fields, $md5=false ) {
			if ( ! isset( $this->username ) ) {
				if ( $md5 == false ) { $fields['loginPass'] = md5( $fields['loginPass'] ); }
				$result = $this->db->getValue(
					'SELECT COUNT(*) FROM ' . $this->dbPrefix . 'users
					WHERE UserName = ' . $this->db->sqlString( $fields['loginName'] ) . '
					AND UserPass = ' . $this->db->sqlString( $fields['loginPass'] )
				);
				if ( $result == '1' ) {
					$this->username = $fields['loginName'];
					return true;
				} else {
					return array( '__ALL__' => $this->strings['t_error_login'] ); 
				}
			}
		}

		// Authentication was succesfull
		function authenticationSucceeded() {
			$this->setVar( 'username', $this->username );
		}

		// Function to make an url to an alias
		function makeAliasUrl( $alias ) {
			if ( $this->config['short_urls'] == '1' ) {
				return $this->basedir . $GLOBALS['cfg']['item_url'] . '/' . $alias;
			} else {
				return $this->basedir . $GLOBALS['cfg']['item_url'] . '?id=' . $alias;
			}
		}

	}

	// Class definition
	class YDCmsRequest extends YDCmsBaseRequest {

		// Class constructor
		function YDCmsRequest() {

			// Initialize the parent
			$this->YDCmsBaseRequest();

			// Start with no items
			$this->items1 = array();
			$this->items2 = array();
			$this->frontItems = array();

			// Get the list of sections
			$this->items1 = $this->db->getRecords(
				'SELECT i.ItemPK, i.SectionFK, LOWER( t.ItemTypeName ) AS ItemTypeName, i.ItemIdx, i.ItemAlias,
						i.ItemDate1, i.ItemDate2, i.ItemUrl, i.ListSubitemsInMenu, 
						i.ItemTitle_L' . $this->languageIdx . ' AS ItemTitle
				FROM ' . $this->dbPrefix . 'items i,
					 ' . $this->dbPrefix . 'itemtypes t
				WHERE	i.ItemTypeFK = t.ItemTypePK
						AND i.ItemVisible = \'1\' AND i.SectionFK IS NULL
				ORDER BY ItemIdx'
			);

			// Add the urls to the items
			foreach ( $this->items1 as $key=>$item ) { 
				if ( empty( $item['itemurl'] ) ) {
					$this->items1[ $key ]['itemurl'] = $this->makeAliasUrl( $item['itemalias'] );
				}
			}

			// Get the default item
			$this->item0 = $this->getByNameOrID( $this->config['site_default_item'] );

			// Set the default items 1 and 2
			$this->item1 = null;
			$this->item2 = null;

			// Initialize the breadcrumbs
			$this->bc = array(
				array(
					'itemurl' => $this->item0['itemurl'],
					'itemtitle' => $this->item0['itemtitle'],
					'itemalias' => $this->item0['itemalias']
				) 
			);

		}

		// Output the template
		function prepareOutput( $templateName='' ) {

			// Set the template dir for the skins
			$this->template->setTemplateDir( YD_SELF_DIR . '/skins/default' );
			if ( isset( $this->config['site_skin'] ) ) {
				$this->template->setTemplateDir( YD_SELF_DIR . '/skins/' . $this->config['site_skin'] );
			}

			// Set the images directory
			$this->setVar( 'site_images', $this->basedir . 'skins/' . $this->config['site_skin'] . '/images' );
			$this->setVar( 'site_skindir', $this->basedir . 'skins/' . $this->config['site_skin'] );

			// Add the breadcrumb
			$breadcrumb = $this->getBreadCrumb();

			// Add the breadcrumb as html
			$breadcrumb_html = array();
			foreach ( $breadcrumb as $item ) {
				if ( $item['itemurl'] ) {
					array_push( $breadcrumb_html, '<a href="' . $item['itemurl'] . '">' . $item['itemtitle'] . '</a>' );
				} else {
					array_push( $breadcrumb_html, '<a href="' . YD_SELF_URI . '">' . $item['itemtitle'] . '</a>' );
				}
			}
			$this->setVar(
				'breadcrumb_html', implode( ' ' . $this->config['site_breadcrumbsep'] . ' ', $breadcrumb_html )
			);

			// Remove the first element (which is home)
			array_shift( $breadcrumb );

			// Get the toplevel element
			$current1 = array_shift( $breadcrumb );
			$current2 = array_shift( $breadcrumb );

			// Get the ID of current1
			$current1ID = null;
			foreach ( $this->items1 as $key=>$item ) {
				if ( is_null( $current1 ) ) {
					if ( $this->config['site_default_item'] = $item['itemalias'] ) {
						$current1 = $item;
					}
				}
				if ( $item['itemalias'] == $current1['itemalias'] ) {
					$current1ID = $item['itempk'];
				}
			}

			// Set the paging defaults
			$this->items2Total = null;
			$this->items2Start = null;
			$this->items2Pagesize = $this->config['default_pagesize'];

			// Get the subitems
			if ( empty( $this->items2 ) && $current1ID ) {

				// Check if there is a start specified
				if ( isset( $_GET['start'] ) ) { $this->items2Start = $_GET['start']; }
				if ( isset( $_GET['pagesize'] ) ) { $this->items2Pagesize = $_GET['pagesize']; }

				// Check if we need to do paging
				if (
					! $this->item1 && $this->item2['itemalias'] != $this->config['site_default_item']
					&& ! is_null( $this->items2Start ) && $this->item2['listsubitemsinmenu'] == '0'
				) {

					// Select the total amount of items
					$this->items2Total = $this->db->getValue(
						'SELECT COUNT(*) FROM ' . $this->dbPrefix . 'items i,
							 ' . $this->dbPrefix . 'itemtypes t,
							 ' . $this->dbPrefix . 'users u
						WHERE	i.ItemTypeFK = t.ItemTypePK AND i.UserFK = u.UserPK AND i.ItemVisible = \'1\' 
								AND i.SectionFK = ' . $this->db->sqlString( $current1ID )
					);

					// Get the items2 (paged)
					$this->items2 = $this->db->getRecords(
						'SELECT i.ItemPK, i.SectionFK, LOWER( t.ItemTypeName ) AS ItemTypeName, i.ItemIdx, i.ItemAlias,
								i.ItemDate1, i.ItemDate2, i.ItemUrl, 
								i.ItemTitle_L' . $this->languageIdx . ' AS ItemTitle,
								i.ItemData_L' . $this->languageIdx . ' AS ItemData
						FROM ' . $this->dbPrefix . 'items i,
							 ' . $this->dbPrefix . 'itemtypes t
						WHERE	i.ItemTypeFK = t.ItemTypePK AND i.ItemVisible = \'1\'
								AND i.SectionFK = ' . $this->db->sqlString( $current1ID ) .'
						ORDER BY ItemIdx ASC, ItemDate1 DESC LIMIT ' . $this->items2Start . ', ' . $this->items2Pagesize
					);

				} else {

					// Get the items2
					$this->items2 = $this->db->getRecords(
						'SELECT i.ItemPK, i.SectionFK, LOWER( t.ItemTypeName ) AS ItemTypeName, i.ItemIdx, i.ItemAlias,
								i.ItemDate1, i.ItemDate2, i.ItemUrl, 
								i.ItemTitle_L' . $this->languageIdx . ' AS ItemTitle,
								i.ItemData_L' . $this->languageIdx . ' AS ItemData
						FROM ' . $this->dbPrefix . 'items i,
							 ' . $this->dbPrefix . 'itemtypes t
						WHERE	i.ItemTypeFK = t.ItemTypePK AND i.ItemVisible = \'1\'
								AND i.SectionFK = ' . $this->db->sqlString( $current1ID ) .'
						ORDER BY ItemIdx ASC, ItemDate1 DESC'
					);

				}

			}

			// Add the missing properties
			foreach ( $this->items2 as $key=>$item ) { 
				if ( empty( $item['itemurl'] ) ) {
					$this->items2[ $key ]['itemurl'] = $this->makeAliasUrl( $item['itemalias'] );
				}
			}

			// Add the browser name
			if ( $this->browser->getBrowser() == 'ie' ) {
				$this->setVar( 'browser', 'ie' );
			} else {
				$this->setVar( 'browser', 'ns' );
			}

			// Add the stuff to the template
			$this->setVar( 'items1', $this->items1 );
			$this->setVar( 'items2', $this->items2 );
			$this->setVar( 'item0', $this->item0 );
			$this->setVar( 'item1', $this->item1 );
			$this->setVar( 'item2', $this->item2 );
			$this->setVar( 'current1', $current1 );
			$this->setVar( 'current2', $current2 );
			$this->setVar( 'frontItems', $this->frontItems );

			// Add the paging details
			$this->setVar( 'items2Total', $this->items2Total );
			$this->setVar( 'items2Start', $this->items2Start );
			$this->setVar( 'items2Pagesize', $this->items2Pagesize );

		}

		// Function to get the item by name or ID
		function getByNameOrID( $id ) {
			foreach ( $this->items1 as $item ) {
				if ( $item['itempk'] == $id || $item['itemalias'] == strtolower( $id ) ) {
					return $item;
				}
			}
			return null;
		}

		// Function which will return the breadcrumb
		function getBreadCrumb() {
			return array(
				array( 'itemurl'=>$this->item0['itemurl'], 'itemtitle'=>$this->item0['itemtitle'] ),
			);
		}

	}

	// Class definition
	class YDCmsAdminRequest extends YDCmsBaseRequest {

		// Class constructor
		function YDCmsAdminRequest() {

			// Initialize the parent
			$this->YDCmsBaseRequest();

			// Indicate we require login
			$this->setRequiresAuthentication( true );

			// Start with no username
			$this->username = null;

		}

		// The login action
		function actionLogin() {

			// Redirect to default action if already logged in
			if ( $this->isAuthenticated() == true ) { $this->forward( 'default' ); return; }

			// Include the form library
			require_once( 'YDForm.php' );

			// Create the login form
			$form = new YDForm( 'loginForm' );

			// Set the error text
			$form->setHtmlError( '<span class="error"><b>' . $this->strings['t_error'] . '</b>: ', '</span>' );

			// Check if the login name exists
			$defaults = array( 'loginTo' => $this->getActionName() );
			if ( ! empty( $_COOKIE[ $this->config['site_id'] . '_user' ] ) ) {
				$defaults['loginName'] = $_COOKIE[ $this->config['site_id'] . '_user' ];
			}
			$form->setDefaults( $defaults );

			// Add the elements
			$form->addElement(
				'text', 'loginName', $this->strings['t_username'], array( 'class' => 'textFieldMedium' )
			);
			$form->addElement(
				'password', 'loginPass', $this->strings['t_password'], array( 'class' => 'textFieldMedium' )
			);
			$form->addElement(
				'submit', 'cmdSubmit', $this->strings['t_login'], array( 'class' => 'button' )
			);
			$form->addElement( 'hidden', 'loginTo', '' );

			// Add the element rules
			$form->addRule( 'loginName', 'required', $this->strings['t_req_loginname'] );
			$form->addRule( 'loginPass', 'required', $this->strings['t_req_loginpass'] );

			// Add the rules
			$form->addFormRule( array( & $this, 'checkLogin' ) );

			// Process the form
			if ( $form->validate() == true ) {

				// Get username and password
				$usrName = $form->getValue( 'loginName' );
				$usrPass = $form->getValue( 'loginPass' );
				$loginTo = $form->getValue( 'loginTo' );

				// Set the cookies
				setcookie( $this->config['site_id'] . '_user', $usrName, time() + 31536000, '/' );
				setcookie( $this->config['site_id'] . '_pass', md5( $usrPass ), time() + 31536000, '/' );

				// Set the username
				$this->username = $usrName;

				// Mark the form as valid
				$this->authenticationSucceeded();

				// TODO: forward to the original URL
				if ( ! empty( $loginTo ) && $loginTo != 'login' ) {
					$this->forward( $loginTo );
				} else {
					$this->forward( 'default' );
				}
				return;

			}

			// Add the form to the template
			$this->setVar( 'form_html', $form->toHtml() );

			// Output the template
			$this->output( 'login' );

		}

		// Logout action
		function actionLogout() {

			// Remove the cookie
			setcookie( $this->config['site_id'] . '_pass', null, time() - 31536000, '/' );

			// Redirect to the login form
			$this->redirectToAction( 'login' );
			return;

		}

		// Output the template
		function prepareOutput( $templateName='' ) {

			// Set the template dir for the skins
			$this->template->setTemplateDir( YD_SELF_DIR . '/skins/default/admin' );
			if ( isset( $this->config['site_skin'] ) ) {
				$this->template->setTemplateDir( YD_SELF_DIR . '/skins/' . $this->config['site_skin'] . '/admin' );
			}

			// Set the images directory
			$this->setVar( 'site_images', 'skins/' . $this->config['site_skin'] . '/images' );

			// Add the browser name
			if ( $this->browser->getBrowser() == 'ie' ) {
				$this->setVar( 'browser', 'ie' );
			} else {
				$this->setVar( 'browser', 'ns' );
			}

		}

		// Redirect to the login if the authentication failed
		function authenticationFailed() {
			$this->forward( 'login' );
			return;
		}

	}

?>