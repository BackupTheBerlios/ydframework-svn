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

			// Include the template class
			require_once( YD_DIR_3RDP . '/class.template/src/class.template.php' );

			// Instantiate our template class
			$this->_tpl = new template();

			// Set the default template directory
			$this->_tpl->template_dir = YD_SELF_DIR;

		}

		/**
		 *	Set te template directory.
		 *
		 *	@param $dir	The directory to look in for finding template files.
		 */
		function setTemplateDir( $dir ) {
			$this->_tpl->template_dir = realpath( $dir );
		}

		/**
		 *	This is used to assign values to the templates. You can pass a key/value pair, or an associative array of
		 *	key/value pairs.
		 *
		 *	@param $name	Name you want to use for this variable for referencing it in the template.
		 *	@param $value	(optional) The value of the variable you want to add.
		 */
		function setVar( $name, $value=null ) {
			$this->_tpl->assign( $name, $value );
		}

		/**
		 *	This is used to assign a constant or "config" value. These constant values are referenced in the same way as
		 *	values loaded from config files. Usage is the same as with setVar.
		 *
		 *	@param $name	Name you want to use for this variable for referencing it in the template.
		 *	@param $value	(optional) The value of the variable you want to add.
		 */
		function setConfigVar( $name, $value = null ) {
			$this->_tpl->assign_config( $value, $value );
		}

		/**
		 *	This will effectively erase an assigned variable.
		 *
		 *	@param $name	(optional) Name of the variable you want to clear.
		 */
		function clear( $name = null ) {
			$this->_tpl->clear( $name );
		}

		/**
		 *	This will effectively erase a config value, whether assigned manually or loaded from a config file.
		 *
		 *	@param $name	(optional) Name of the variable you want to clear.
		 */
		function clear_config( $name = null ) {
			$this->_tpl->clear_config( $name );
		}

		/**
		 *	Will return all variables in an associative array, or a single variable named $name. This will not return
		 *	variables assigned inside the template, unless the template has already been processed.
		 *
		 *	@param $name	(optional) Name of the variable you want to clear.
		 *
		 *	@returns	All variables in an associative array, or a single variable named $name.
		 */
		function & get_vars( $name = null ) {
			return $this->_tpl_get_vars( $name );
		}

		/**
		 *	Will return all config values in an associative array, or a single config value named $name. This will not
		 *	return values loaded by config_load calls embedded in a template, unless the template has already been 
		 *	processed. 
		 *
		 *	@param $name	(optional) Name of the variable you want to clear.
		 *
		 *	@returns	All config variables in an associative array, or a single variable named $name.
		 */
		function & get_config_vars( $name = null ) {
			return $this->get_config_vars( $name );
		}

		/**
		 *	This will clear out the compiled template folder, or if a file and/or a compile id is supplied, it will
		 *	clear that specific template. If you have utilized compile groups, then it is possible to delete a specific
		 *	group by specifying a compile id. If no file or compile id is specified, all compiled files will be deleted.
		 *
		 *	@param $file		(optional) File for which you want to clear the cache.
		 *	@param $compile_id	(optional) Compile ID you want to clear the cache for.
		 */
		function clear_compiled( $file = null, $compile_id = null ) {
			$this->_tpl->clear_compiled( $file, $compile_id );
		}

		/**
		 *	This will clear out the cache, or if a file and/or a cache id is supplied, it will clear that specific
		 *	template. If you have utilized cache groups, then it is possible to delete a specific group by specifying a
		 *	cache id. If no file or cache id is specified, all cached files will be deleted.
		 *
		 *	@param $file		(optional) File for which you want to clear the cache.
		 *	@param $cache_id	(optional) Cache ID you want to clear the cache for.
		 */
		function clear_cached( $file = null, $cache_id = null ) {
			$this->clear_cached( $file, $cache_id );
		}

		/**
		 *	Returns true if there is a valid cache for this template. This only works if caching is to true.
		 *
		 *	@param $file		(optional) File for which you want to clear the cache.
		 *	@param $cache_id	(optional) Cache ID you want to clear the cache for.
		 *
		 *	@returns	Boolean indicating if the cache for the file and cache ID is still valid.
		 */
		function is_cached( $file, $cache_id = null ) {
			return $this->is_cached( $file, $cache_id );
		}

		/**
		 *	Use this to dynamically register a modifiery plugin. Pass the template modifier name, followed by the PHP
		 *	function that implements it.
		 *
		 *	The php-function callback $implementation can either be a string containing the function name, or an array
		 *	of the form array(&$object, $method) with &$object being a reference to an object and $method being a string
		 *	that contains the method name, or an array of the form array(&$class, $method) with &$class being a
		 *	reference to a class and $method being a method of that class.
		 *
		 *	@param $name		Name of the modifier.
		 *	@param $function	Function implementing the modifier.
		 */
		function registerModifier( $name, $function ) {
			$this->_tpl->register_modifier( $name, $function );
		}

		/**
		 *	Use this dynamically register a template function. Pass in the template function name, followed by the PHP
		 *	function name that implements it.
		 *
		 *	The php-function callback $implementation can either be a string containing the function name, or an array 
		 *	of the form array(&$object, $method) with &$object being a reference to an object and $method being a string
		 *	that contains the method name, or an array of the form array(&$class, $method) with &$class being a
		 *	reference to a class and $method being a method of that class.
		 *
		 *	@param $name		Name of the function.
		 *	@param $function	Function implementing the function.
		 */
		function registerFunction( $name, $function ) {
			$this->_tpl->register_function( $name, $function );
		}

		/**
		 *	Use this to dynamically register a block function. Pass in the block function name, followed by the PHP
		 *	function that implements it. The php-function callback $implementation can either be a string containing the
		 *	function name, or an array of the form array(&$object, $method) with &$object being a reference to an object 
		 *	and $method being a string that contains the method name, or an array of the form array(&$class, $method) 
		 *	with &$class being a reference to a class and $method being a method of that class.
		 *
		 *	@param $function		Name of the block.
		 *	@param $implementation	Function implementing the block.
		 */
		function registerBlock( $function, $implementation ) {
			$this->_tpl->registerBlock( $function, $implementation );
		}

		/**
		 *	This function will parse the template and will return the parsed contents. The name of the template you need
		 *	to specify is the basename of the template without the file extension. This function automatically adds some
		 *	variables to the template, which you can use as well in the template: YD_FW_NAME, YD_FW_VERSION, 
		 *	YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI, YD_ACTION_PARAM.
		 *
		 *	@param $name	The name of the template you want to parse and output.
			@param $cacheID	ID for the cache of the template (must be unique).
		 *
		 *	@returns	This function returns the output of the parsed template.
		 */
		function getOutput( $name, $cacheID=null ) {

			// Add some default variables
			$this->setVar( 'YD_FW_NAME', YD_FW_NAME );
			$this->setVar( 'YD_FW_VERSION', YD_FW_VERSION );
			$this->setVar( 'YD_FW_NAMEVERS', YD_FW_NAMEVERS );
			$this->setVar( 'YD_FW_HOMEPAGE', YD_FW_HOMEPAGE );
			$this->setVar( 'YD_SELF_SCRIPT', YD_SELF_SCRIPT );
			$this->setVar( 'YD_SELF_URI', YD_SELF_URI );
			$this->setVar( 'YD_ACTION_PARAM', YD_ACTION_PARAM );

			// Get the path to the template
			if ( is_file( $this->_tpl->template_dir . '/' . $name . YD_TPL_EXT ) ) {
				$tplPath = realpath( $this->_tpl->template_dir . '/' . $name . YD_TPL_EXT );
			} elseif ( is_file( $this->_tpl->template_dir . '/' . $name ) ) {
				$tplPath = realpath( $this->_tpl->template_dir . '/' . $name );
			} else {
				$tplPath = realpath( $name );
			}

			// Check if the file exists
			if ( ! is_file( $tplPath ) ) {
				trigger_error( 'Template not found: ' . $tplPath, YD_ERROR );
			}

			// Configure the template object
			$this->_tpl->template_dir = dirname( $tplPath );
			$this->_tpl->compile_dir = YD_DIR_TEMP;
			$this->_tpl->left_tag = '{';
			$this->_tpl->right_tag = '}';

			// Register the custom modifiers
			$this->_tpl->register_modifier( 'sizeof', 'sizeof' );
			$this->_tpl->register_modifier( 'addslashes', 'addslashes' );
			$this->_tpl->register_modifier( 'implode', 'YDTemplate_modifier_implode' );
			$this->_tpl->register_modifier( 'fmtfilesize', 'YDTemplate_modifier_fmtfileize' );
			$this->_tpl->register_modifier( 'date_format', 'YDTemplate_modifier_date_format' );
			$this->_tpl->register_modifier( 'dump', 'YDTemplate_modifier_dump' );

			// Get the cache ID
			if ( ! empty( $cacheID ) ) {
				$cacheID = YD_TMP_PRE . 'C_' . md5( $cacheID );
			}

			// Output the template
			$contents = $this->_tpl->fetch(
				 basename( $tplPath ), YD_TMP_PRE . 'T_' . md5( dirname( $tplPath ) ), $cacheID
			);

			// Returns the contents
			return $contents;

		}

		/**
		 *	This function will load the specified config file into the template. You can specify a specific section or
		 *	even a specific key to load, but otherwise, all config vars from the specified template will be loaded. Will
		 *	return true if the config file was successfully loaded and false otherwise.
		 *
		 *	@param $file			Name of the config file.
		 *	@param $section_name	(optional) Section to load.
		 *	@param $var_name		(optional) Variable to load.
		 *
		 *	@returns	True if the config file was successfully loaded and false otherwise.
		 */
		function config_load( $file, $section_name = null, $var_name = null ) {
			$this->_tpl->config_load( $file, $section_name, $var_name );
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

?>