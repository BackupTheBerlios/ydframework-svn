<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

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

			// The template directory
			$this->_templateDir = YD_SELF_DIR;

			// Keep a list of the variables
			$this->_vars = array();

			// Keep track of custom modifiers
			$this->_modifiers = array();
			$this->_functions = array();

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
		 *	Set te template directory.
		 *
		 *	@param $dir	The directory to look in for finding template files.
		 */
		function setTemplateDir( $dir ) {
			$this->_templateDir = realpath( $dir );
		}

		/**
		 *	This function registers a new modifier with the specified name pointing to the specified function.
		 *
		 *	@param $name		Name of the modifier.
		 *	@param $function	Function implementing the modifier.
		 */
		function registerModifier( $name, $function ) {
			$this->_modifiers[ $name ] = $function;
		}

		/**
		 *	This function registers a new function with the specified name pointing to the specified function.
		 *
		 *	@param $name		Name of the function.
		 *	@param $function	Function implementing the function.
		 */
		function registerFunction( $name, $function ) {
			$this->_functions[ $name ] = $function;
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
		 *
		 *	@todo
		 *		We should add options here to cache the output.
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

			// Get the path to the template
			if ( is_file( $this->_templateDir . '/' . $name . YD_TPL_EXT ) ) {
				$tplPath = realpath( $this->_templateDir . '/' . $name . YD_TPL_EXT );
			} elseif ( is_file( $this->_templateDir . '/' . $name ) ) {
				$tplPath = realpath( $this->_templateDir . '/' . $name );
			} else {
				$tplPath = realpath( $name );
			}

			// Check if the file exists
			if ( ! is_file( $tplPath ) ) {
				trigger_error( 'Template not found: ' . $tplPath, YD_ERROR );
			}

			// Include smarty
			require_once( YD_DIR_3RDP . '/smarty/Smarty.class.php' );

			// Instantiate smarty
			$tpl = new Smarty();
	
			// Configure smarty
			$tpl->template_dir = dirname( $tplPath );
			$tpl->compile_dir = YD_DIR_TEMP;
			$tpl->left_delimiter = '[';
			$tpl->right_delimiter = ']';

			// Trim whitespace
			$tpl->register_outputfilter( 'YDTemplate_outputfilter_trimwhitespace' );

			// Register the custom modifiers
			$tpl->register_modifier( 'addslashes', 'addslashes' );
			$tpl->register_modifier( 'lower', 'strtolower' );
			$tpl->register_modifier( 'implode', 'YDTemplate_modifier_implode' );
			$tpl->register_modifier( 'fmtfilesize', 'YDTemplate_modifier_fmtfileize' );
			$tpl->register_modifier( 'date_format', 'YDTemplate_modifier_date_format' );
			$tpl->register_modifier( 'dump', 'YDTemplate_modifier_dump' );

			// Add the other modifiers
			foreach ( $this->_modifiers as $name=>$function ) {
				$tpl->register_modifier( $name, $function );
			}

			// Add the functions
			foreach ( $this->_functions as $name=>$function ) {
				$tpl->register_function( $name, $function );
			}

			// Assign the variables
			$tpl->assign( $this->_vars );

			// Output the template
			$contents = $tpl->fetch( basename( $tplPath ), null, md5( dirname( $tplPath ) ) );

			// Returns the contents
			return $contents; 

		}

	}

	/**
	 *	@internal
	 */
	function YDTemplate_modifier_fmtfileize( $size ) {
		return YDStringUtil::formatFileSize( $size );
	}

	/**
	 *	@internal
	 */
	function YDTemplate_modifier_implode( $array, $separator=', ' ) {
		return implode( $separator, $array );
	}

	/**
	 *	@internal
	 */
	function YDTemplate_make_timestamp( $string ) {
		if ( empty( $string ) ) {
			$string = "now";
		}
		$time = strtotime( $string );
		if ( is_numeric( $time ) && $time != -1 ) {
			return $time;
		}
		if ( preg_match( '/^\d{14}$/', $string ) ) {
			$time = mktime(
				substr( $string, 8, 2 ), substr( $string, 10, 2 ), substr( $string, 12, 2 ),
				substr( $string, 4, 2 ), substr( $string, 6, 2 ), substr( $string,0 ,4 )
			);
			return $time;
		}
		$time = ( int ) $string;
		if ( $time > 0 ) {
			return $time;
		} else {
			return time();
		}
	}

	/**
	 *	@internal
	 */
	function YDTemplate_modifier_date_format( $string, $format='%b %e, %Y', $default_date=null ) {
		if( $string != '' ) {
			return strftime( $format, YDTemplate_make_timestamp( $string ) );
		} elseif ( isset( $default_date ) && $default_date != '' ) {
			return strftime( $format, YDTemplate_make_timestamp( $default_date ) );
		} else {
			return;
		}
	}

	/**
	 *	@internal
	 */
	function YDTemplate_modifier_dump( $obj ) {
		var_dump( $obj );
		return;
	}

	/**
	 *	@internal
	 */
	function YDTemplate_outputfilter_trimwhitespace( $source, &$smarty ) {

		// Pull out the script blocks
		preg_match_all( "!<script[^>]+>.*?</script>!is", $source, $match );
		$_script_blocks = $match[0];
		$source = preg_replace( "!<script[^>]+>.*?</script>!is", '@@@SMARTY:TRIM:SCRIPT@@@', $source );

		// Pull out the pre blocks
		preg_match_all( "!<pre>.*?</pre>!is", $source, $match );
		$_pre_blocks = $match[0];
		$source = preg_replace( "!<pre>.*?</pre>!is", '@@@SMARTY:TRIM:PRE@@@', $source );

		// Pull out the textarea blocks
		preg_match_all( "!<textarea[^>]+>.*?</textarea>!is", $source, $match );
		$_textarea_blocks = $match[0];
		$source = preg_replace( "!<textarea[^>]+>.*?</textarea>!is", '@@@SMARTY:TRIM:TEXTAREA@@@', $source );

		// remove all leading spaces, tabs and carriage returns NOT preceeded by a php close tag.
		$source = preg_replace( '/((?<!\?>)\n)[\s]+/m', '\1', $source );

		// replace script blocks
		foreach ( $_script_blocks as $curr_block ) {
			$source = preg_replace( "!@@@SMARTY:TRIM:SCRIPT@@@!", $curr_block, $source, 1 );
		}

		// replace pre blocks
		foreach ( $_pre_blocks as $curr_block ) {
			$source = preg_replace( "!@@@SMARTY:TRIM:PRE@@@!", $curr_block, $source, 1 );
		}

		// replace textarea blocks
		foreach ( $_textarea_blocks as $curr_block ) {
			$source = preg_replace( "!@@@SMARTY:TRIM:TEXTAREA@@@!", $curr_block, $source, 1 );
		}

		return $source;

	}

?>