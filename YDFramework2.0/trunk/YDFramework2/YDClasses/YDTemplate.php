<?php

	// Yellow Duck Framework version 2.0
	// (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}

	require_once( 'YDRequest.php' );
	require_once( YD_DIR_3RDP . '/class.template/src/class.template.php' );

	define( 'YD_TPL_CACHEEXT', '.phc' );
	define( 'YD_TPL_CACHEPRE', YD_TMP_PRE . 'T_' );

	/**
	 *	This class is a wrapper around Smarty Light. Documentation can be found on:
	 *	http://www.paullockaby.com/projects/smarty-light/docs/
	 */
	class YDTemplate extends template {

		/**
		 *	This is the class constructor for the YDTemplate class. By default, it looks in the same directory as the
		 *	current script to find the templates.
		 */
		function YDTemplate() {

			// Set the default template directory
			$this->template_dir = YD_SELF_DIR;

			$this->compile_dir = YD_DIR_TEMP;
			$this->left_tag = '{';
			$this->right_tag = '}';
			$this->caching = false;
			$this->cache_lifetime = 3600;
			$this->cache_dir = YD_DIR_TEMP;

			// Register the custom modifiers
			$this->register_modifier( 'sizeof', 'sizeof' );
			$this->register_modifier( 'addslashes', 'addslashes' );
			$this->register_modifier( 'implode', 'YDTemplate_modifier_implode' );
			$this->register_modifier( 'fmtfilesize', 'YDTemplate_modifier_fmtfileize' );
			$this->register_modifier( 'date_format', 'YDTemplate_modifier_date_format' );
			$this->register_modifier( 'dump', 'YDTemplate_modifier_dump' );

		}

		/**
		 *	This function will parse the template and will display the parsed contents.
		 *
		 *	You can specify the name of the template which should be in the template directory. If no name is specified,
		 *	the basename of the PHP script with the extension '.tpl' will be used.
		 *
		 *	This function automatically adds some variables to the template, which you can use as well in the template:
		 *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI, YD_ACTION_PARAM.
		 *
		 *	@param $file		(optional) The name of the template you want to parse and output.
		 *	@param $cache_id	(optional) ID for the cache of the template (must be unique).
		 */
		function display( $file='', $cache_id=null ) {
			$this->fetch( $file, $cache_id, true );
		}

		/**
		 *	This function will parse the template and will return the parsed contents.
		 *
		 *	You can specify the name of the template which should be in the template directory. If no name is specified,
		 *	the basename of the PHP script with the extension '.tpl' will be used.
		 *
		 *	This function automatically adds some variables to the template, which you can use as well in the template:
		 *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI, YD_ACTION_PARAM.
		 *
		 *	@param $file		(optional) The name of the template you want to parse and output.
		 *	@param $cache_id	(optional) ID for the cache of the template (must be unique).
		 *	@param $display		(optional) Whether the output should be displayed or returned.
		 *
		 *	@returns	This function returns the output of the parsed template.
		 */
		function fetch( $file='', $cache_id=null, $display=false ) {

			// Get the cache filename
			$cacheFile = $this->_getCachePath( $cache_id );

			// If the template is cached, return the cache
			if ( $this->is_cached( $cache_id ) ) {
				$fp = fopen( $cacheFile, 'rb' );
				$result = fread( $fp, filesize( $cacheFile ) );
				fclose( $fp );
				if ( $display == true ) {
					echo( $result );
					return;
				} else {
					return $result;
				};
			}

			// Add some default variables
			$this->assign( 'YD_FW_NAME', YD_FW_NAME );
			$this->assign( 'YD_FW_VERSION', YD_FW_VERSION );
			$this->assign( 'YD_FW_NAMEVERS', YD_FW_NAMEVERS );
			$this->assign( 'YD_FW_HOMEPAGE', YD_FW_HOMEPAGE );
			$this->assign( 'YD_SELF_SCRIPT', YD_SELF_SCRIPT );
			$this->assign( 'YD_SELF_URI', YD_SELF_URI );
			$this->assign( 'YD_ACTION_PARAM', YD_ACTION_PARAM );
			$this->assign( 'YD_ACTION', YDRequest::getActionName() );
			
			// Get the template name
			$tplName = $this->_getTemplateName( $file );

			// Output the template
			$result = parent::fetch( $tplName, $cache_id, false );

			// Save the cache if needed
			if ( ! empty( $cache_id ) && ! $this->force_compile && $this->caching ) {
				$fp = fopen( $cacheFile, 'wb' );
				fwrite( $fp, $result );
				fclose( $fp );
			}

			if ( $display == true ) {
				echo( $result );
			} else {
				return $result;
			};

		}

		/**
		 *	This function will add a YDForm object to the template. It will automatically convert the form to an array
		 *	using the template object so that you don't have to do it manually.
		 *
		 *	If the form specified when calling this function is not a class derived from the YDForm class, a fatal error
		 *	will be thrown.
		 *
		 *	@param $name	Name you want to use for this form for referencing it in the template.
		 *	@param $form	The form you want to add.
		 */
		function assignForm( $name, $form ) {
			if ( ! YDObjectUtil::isSubclass( $form, 'YDForm' ) ) {
				trigger_error(
					'The form you have tried to add to the form is not a subclass of the YDForm class.', YD_ERROR
				);
			}
			$this->assign( $name, $form->toArray( $this ) );
		}

		/**
		 *	This function will get the name of the template.
		 *
		 *	@internal
		 */
		function _getTemplateName( $file='' ) {
			if ( empty( $file ) ) {
				$file = basename( YD_SELF_FILE, YD_SCR_EXT );
			}
			if ( is_file( realpath( $this->template_dir ) . '/' . $file . YD_TPL_EXT ) ) {
				$tplName = $file . YD_TPL_EXT;
			} else {
				$tplName = $file;
			}
			if ( ! is_file( realpath( $this->template_dir ) . '/' . $tplName ) ) {
				trigger_error( 'Template not found: ' . $tplName, YD_ERROR );
			}
			return $tplName;
		}

		/**
		 *	This will clear out the compiled template folder, or if a file is supplied, it will clear that specific
		 *	template. If no file is specified, all compiled files will be deleted. 
		 *
		 *	@internal
		 */
		function clear_compiled( $file=null, $compile_id=null ) {
			if ( $file === '' ) {
				$file = $this->_getTemplateName();
			}
			parent::clear_compiled( $file, $compile_id );
		}

		/**
		 *	This will clear out the cache, or if a file and/or a cache id is supplied, it will clear that specific
		 *	template. If you have utilized cache groups, then it is possible to delete a specific group by specifying a
		 *	cache id. If no file or cache id is specified, all cached files will be deleted.
		 *
		 *	@internal
		 */
		function clear_cached( $cache_id=null ) {
			@unlink( $this->_getCachePath( $cache_id ) );
		}

		/**
		 *	Returns true if there is a valid cache for this template. This only works if caching is to true.
		 *
		 *	@param $cache_id	ID for the cache of the template (must be unique).
		 *
		 *	@returns	Boolean indicating if the item is cached or not.
		 */
		function is_cached( $cache_id ) {
			if ( empty( $cache_id ) || $this->force_compile || ! $this->caching ) {
				return false;
			}
			$cacheFile = $this->_getCachePath( $cache_id );
			if (
				file_exists( $cacheFile ) && ( ( time() - filemtime( $cacheFile ) ) < $this->cache_lifetime )
			) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 *	Function to convert a cache ID to a file name.
		 *
		 *	@param $cache_id	ID for the cache of the template (must be unique).
		 *
		 *	@returns	The file path of the cache file
		 *
		 *	@internal
		 */
		function _getCachePath( $cache_id ) {
			if ( empty( $cache_id ) ) {
				return '';
			}
			return realpath( $this->cache_dir ) . '/' . YD_TPL_CACHEPRE . md5( $cache_id ) . YD_TPL_CACHEEXT;
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