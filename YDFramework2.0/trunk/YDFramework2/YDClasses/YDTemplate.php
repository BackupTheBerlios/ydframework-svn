<?php

	/*
	
		Yellow Duck Framework version 2.0
		Copyright (C) (c) copyright 2004 Pieter Claerhout
	
		This library is free software; you can redistribute it and/or
		modify it under the terms of the GNU Lesser General Public
		License as published by the Free Software Foundation; either
		version 2.1 of the License, or (at your option) any later version.
	
		This library is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
		Lesser General Public License for more details.
	
		You should have received a copy of the GNU Lesser General Public
		License along with this library; if not, write to the Free Software
		Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
	
	*/

	if ( ! defined( 'YD_FW_NAME' ) ) {
		die( 'Yellow Duck Framework is not loaded.' );
	}
	
	define( 'SMARTY_DIR', dirname( __FILE__ ) . '/../3rdparty/smarty/libs/' );

	YDInclude( 'YDFileSystem.php' );
	YDInclude( SMARTY_DIR . '/Smarty.class.php' );

	/**
	 *	This class is a wrapper around Smarty. Documentation can be found on: http://smarty.php.net/
	 */
	class YDTemplate extends Smarty {

		/**
		 *	This is the class constructor for the YDTemplate class. By default, it looks in the same directory as 
		 *	the current script to find the templates.
		 */
		function YDTemplate() {

			// Initialize the parent
			$this->Smarty();
		
			// Set the default template directory
			$this->template_dir = YD_SELF_DIR;

			$this->compile_dir = YD_DIR_TEMP;
			$this->use_sub_dirs = false;
			$this->caching = false;
			$this->cache_lifetime = 3600;
			$this->cache_dir = YD_DIR_TEMP . '/cache';

			// Create the cache dir if it doesn't exist
			if ( $this->caching && ! is_dir( $this->cache_dir ) ) {
				@mkdir( $this->cache_dir );
			}

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
		 *	@param $compile_id	(optional) ID for the compilation of the template (must be unique).
		 */
		function display( $file='', $cache_id=null, $compile_id=null ) {
			$this->fetch( $file, $cache_id, $compile_id, true );
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
		 *	@param $compile_id	(optional) ID for the compilation of the template (must be unique).
		 *	@param $display		(optional) Whether the output should be displayed or returned.
		 *
		 *	@returns	This function returns the output of the parsed template.
		 */
		function fetch( $file='', $cache_id=null, $compile_id=null, $display=false ) {

			// Create the cache dir if it doesn't exist
			if ( $this->caching && ! is_dir( $this->cache_dir ) ) {
				@mkdir( $this->cache_dir );
			}

			// Add some default variables
			$this->assign( 'YD_FW_NAME', YD_FW_NAME );
			$this->assign( 'YD_FW_VERSION', YD_FW_VERSION );
			$this->assign( 'YD_FW_NAMEVERS', YD_FW_NAMEVERS );
			$this->assign( 'YD_FW_HOMEPAGE', YD_FW_HOMEPAGE );
			$this->assign( 'YD_SELF_SCRIPT', YD_SELF_SCRIPT );
			$this->assign( 'YD_SELF_FILE', YD_SELF_FILE );
			$this->assign( 'YD_SELF_URI', YD_SELF_URI );
			$this->assign( 'YD_ACTION_PARAM', YD_ACTION_PARAM );
			$this->assign( 'YD_ACTION', YDRequest::getActionName() );
			
			// Get the template name
			$tplName = $this->_getTemplateName( $file );

			// Add pseudo compile id
			if ( is_null( $compile_id ) ) {
				$compile_id = sprintf( '%u', crc32( realpath( $this->template_dir ) . '/' . $tplName ) );
			}

			// Output the template
			$result = parent::fetch( $tplName, $cache_id, $compile_id );

			// Display the template or return the result
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
					'The form you have tried to add to the template is not a subclass of the YDForm class.', YD_ERROR
				);
			}
			$this->assignObject( $name, $form->toArray() );
		}

		/**
		 *	This function will add an object to the template.
		 *
		 *	@param $name	Name you want to use for this object for referencing it in the template.
		 *	@param $obj		The object you want to add.
		 */
		function assignObject( $name, $obj ) {
			$this->assign( $name, $obj );
		}

		/**
		 *	This function will get the name of the template.
		 *
		 *	@internal
		 */
		function _getTemplateName( $file='' ) {
			$this->template_dir = YDPath::getFullPath( $this->template_dir );
			if ( empty( $file ) ) {
				$file = YDPath::getFileNameWithoutExtension( YD_SELF_FILE );
			}
			if ( is_file( YDPath::join( $this->template_dir, $file . YD_TPL_EXT ) ) ) {
				$tplName = $file . YD_TPL_EXT;
			} else {
				$tplName = $file;
			}
			if ( ! is_file( YDPath::join( $this->template_dir, $tplName ) ) ) {
				trigger_error( 'Template not found: ' . $tplName, YD_ERROR );
			}
			return $tplName;
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