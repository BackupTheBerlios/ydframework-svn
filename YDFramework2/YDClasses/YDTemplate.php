<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die(  'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDBase.php' );

	/**
	 *	This class implements an template object. Unless changed manually, the template engine will look in the same
	 *	directory as your script to find the template. The templates all share the same file extension, which is '.tpl'.
	 *	By using the setTemplateDir function, you can change this and indicate which directory needs to be looked in for
	 *	finding the template files.
	 */
	class YDTemplate extends YDBase {

		/**
		 *	This is the class constructor for the YDTemplate class. It does not take any arguments and is smart enough
		 *	to configure itself. By default, it looks in the same directory as the current script to find the templates.
		 */
		function YDTemplate() {

			// Initialize YDBase
			$this->YDBase();

			// Keep a list of the variables
			$this->_vars = array();

		}

		/**
		 *	This function adds a variable to the template and gives it the specified name.
		 *
		 *	@param $name	Name you want to use for this variable for referencing it in the template.
		 *	@param $value	The value of the variable you want to add.
		 */
		function setVar( $name, $value ) {
			$this->_vars[ $name ] = $value;
		}

		/**
		 *	This function will parse the template and will return the parsed contents. The name of the template you need
		 *	to specify is the basename of the template without the file extension. This function automatically adds some
		 *	variables to the template, which you can use as well in the template: YD_FW_NAME, YD_FW_VERSION, 
		 *	YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI, YD_ACTION_PARAM, YD_ENV, YD_COOKIE, YD_GET,
		 *	YD_POST, YD_FILES, YD_REQUEST, YD_SESSION, YD_GLOBALS.
		 *
		 *	@param $name	The name of the template you want to parse and output.
		 *
		 *	@returns	This function returns the output of the parsed template.
		 */
		function getOutput( $name ) {

			// Add some default variables
			$this->setVar( 'YD_FW_NAME', YD_FW_NAME );
			$this->setVar( 'YD_FW_VERSION', YD_FW_VERSION );
			$this->setVar( 'YD_FW_NAMEVERS', YD_FW_NAMEVERS );
			$this->setVar( 'YD_FW_HOMEPAGE', YD_FW_HOMEPAGE );
			$this->setVar( 'YD_SELF_SCRIPT', YD_SELF_SCRIPT );
			$this->setVar( 'YD_SELF_URI', YD_SELF_URI );
			$this->setVar( 'YD_ACTION_PARAM', YD_ACTION_PARAM );

			// Add PHP variables
			$this->setVar( 'YD_ENV', $_ENV );
			$this->setVar( 'YD_COOKIE', $_COOKIE );
			$this->setVar( 'YD_GET', $_GET );
			$this->setVar( 'YD_POST', $_POST );
			$this->setVar( 'YD_FILES', $_FILES );
			$this->setVar( 'YD_REQUEST', $_REQUEST );
			$this->setVar( 'YD_SESSION', $_SESSION );
			$this->setVar( 'YD_GLOBALS', $GLOBALS );
			$this->setVar( 'YD_SERVER', $_SERVER );

			// Get the path to the template
			if ( is_file( $name ) ) {
				$tplPath = realpath( $name );
			} elseif ( is_file( $name . YD_TPL_EXT ) ) {
				$tplPath = realpath( $name . YD_TPL_EXT );
			} else {
				$tplPath = realpath( YD_SELF_DIR ) . '/' . $name . YD_TPL_EXT;
			}

			// Check if the file exists
			if ( ! is_file( $tplPath ) ) {
				YDFatalError( 'Template not found: ' . $tplPath );
			}

			// Extract the variables
			extract( $this->_vars );

			// Process the template
			ob_start();
			include( $tplPath );
			$contents = ob_get_contents();
			ob_end_clean();

			// Returns the contents
			return $contents; 

		}

	}

?>