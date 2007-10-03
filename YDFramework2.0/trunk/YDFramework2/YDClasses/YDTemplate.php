<?php

    /*

        Yellow Duck Framework version 2.1
        (c) Copyright 2002-2007 Pieter Claerhout

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

    /**
     *  @addtogroup YDFramework Core
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Constants
    if ( ! defined( 'SMARTY_DIR' ) ) {
        define( 'SMARTY_DIR', dirname( __FILE__ ) . '/../3rdparty/smarty/libs/' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDRequest.php');

    // Configure the default for this class
    YDConfig::set( 'YD_TEMPLATE_ENGINE', 'smarty', false );
    YDConfig::set( 'YD_TEMPLATE_DIR', YD_SELF_DIR, false );

    // Use the right class implementation
    if ( strtolower( YDConfig::get( 'YD_TEMPLATE_ENGINE', 'smarty' ) == 'smarty' ) ) {

        // Include the smarty library
        include_once( SMARTY_DIR . '/Smarty.class.php' );

        /**
         *  This class is a wrapper around Smarty. Documentation can be found on: http://smarty.php.net/
         *
         *  @ingroup YDFramework
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
                $this->template_dir = YDConfig::get( 'YD_TEMPLATE_DIR', YD_SELF_DIR );

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
                $this->register_modifier( 'bbcode', 'YDTemplate_modifier_bbcode' );
                $this->register_modifier( 'absoluteurl', 'YDTemplate_modifier_absoluteurl' );
                $this->register_modifier( 'timesince', 'YDTemplate_modifier_timesince' );
                $this->register_modifier( 'url', 'YDTemplate_modifier_url' );
                $this->register_modifier( 'state', 'YDTemplate_modifier_state' );


                // Register the custom functions
                $this->register_function( 't', 'YDTemplate_function_t' );

                // custom javascript code
                $this->customJavascript       = '';
                $this->customJavascriptOnload = array();

                // custom Css code
                $this->customCss = '';
            }

            /**
             *	This function will parse the template and will display the parsed contents.
             *
             *	You can specify the name of the template which should be in the template directory. If no name is specified,
             *	the basename of the PHP script with the extension '.tpl' will be used.
             *
             *	This function automatically adds some variables to the template, which you can use as well in the template:
             *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI.
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
             *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI.
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
                $this->assign( 'YD_FW_POWERED_BY', YD_FW_POWERED_BY );
                $this->assign( 'YD_FW_COPYRIGHT', YD_FW_COPYRIGHT );
                $this->assign( 'YD_SELF_SCRIPT', YD_SELF_SCRIPT );
                $this->assign( 'YD_SELF_FILE', YD_SELF_FILE );
                $this->assign( 'YD_SELF_URI', YD_SELF_URI );
                $this->assign( 'YD_FW_REVISION', YD_FW_REVISION );
                $this->assign( 'YD_ACTION', YDRequest::getActionName() );

                // check if a custom resource is defined
                if ( strpos( $file, ':' ) !== false ){

                    // display or return the result
                    if ( $display == true ) {
                        echo( parent::fetch( $file ) );
                        return;
                    }
                    return parent::fetch( $file );
                }

                // Add custom javascript
                $this->register_outputfilter( array( &$this, "__assignHeadCode") );

                // check for multiple directories to choose the one that has the file
                if ( is_array( $this->template_dir ) ){

                    if ( strpos( $file, '.' ) === false ){
                        $file = $file . '.tpl';
                    }

                    foreach( $this->template_dir as $dir ){
                        if ( is_file ( $dir . '/' . $file ) ){
                            $this->template_dir = realpath( $dir );
                            break;
                        }
                    }

                    // check if a valid directory was found
                    if ( is_array( $this->template_dir ) ){
                        trigger_error(
                            'Was not possible to find "' . $file . '" in ' . count( $this->template_dir ) . ' template directories.',
                            YD_ERROR
                        );
                    }

                }

                // Get the template name
                $tplName = $this->_getTemplateName( $file );

                // Get the real path to the template file
                $tplPath = $tplName;
                if ( ! is_file( $tplPath ) ) {
                    $tplPath = realpath( $this->template_dir . '/' . $tplName );
                } else {
                    $tplPath = realpath( $tplName );
                }

                // Add pseudo compile id
                if ( is_null( $compile_id ) ) {

                    // Generate the compile ID
                    $compile_id = sprintf( '%u', crc32( $tplPath ) );

                }

                // Output the template
                $result = parent::fetch( $tplPath, $cache_id, $compile_id );

                // Display the template or return the result
                if ( $display == true ) {
                    echo( $result );
                } else {
                    return $result;
                };

            }

            /**
             *	This function will parse the template and will assign the parsed contents to the template.
             *
             *	You can specify the name of the template which should be in the template directory. If no name is specified,
             *	the basename of the PHP script with the extension '.tpl' will be used.
             *
             *	This function automatically adds some variables to the template, which you can use as well in the template:
             *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI.
             *
             *	@param $name		Name you want to use for this fetched template for referencing it in the template.
             *	@param $file		The name of the template you want to parse and output.
             *	@param $cache_id	(optional) ID for the cache of the template (must be unique).
             *	@param $compile_id	(optional) ID for the compilation of the template (must be unique).
             *
             *	@returns	This function returns the output of the parsed template.
             */
            function assignFetchedTemplate( $name, $file, $cache_id=null, $compile_id=null ) {
                $fetched = $this->fetch( $file, $cache_id, $compile_id, false );
                $this->assign( $name, $fetched );
            }


            /**
             *	This function will assign multiple values from an array
             */
            function assignArray( $values ) {
                foreach( $values as $k => $v ){
                    $this->assign( $k, $v );
                }
            }


            /**
             *	This function will parse the template and will assign custom code to head.
             *
             *	@param $tpl_source		Template source
             *	@param $smarty		    Smarty pointer
             *
             *	@returns	Template code parsed
             */
            function __assignHeadCode( $tpl_source, &$smarty ){

                // code to add
                $code = '';

                // test if customCss is set
                if ( $this->customCss != '' )
                    $code = "\n<style type=\"text/css\">\n" . $this->customCss . "</style>";

                // test if customJavascriptUrls is set
                if ( is_array( $this->customJavascriptUrls ) ) {
                    foreach ( $this->customJavascriptUrls as $jsUrl ) {
                        $code .= "\n<script type=\"text/javascript\" src=\"" . $jsUrl .  "\"></script>\n";
                    }
                }

                // add onload code to custom javascript code
                if ( ! empty( $this->customJavascriptOnload ) ){ 
					$onLoad = "window.onload = function() {" . implode( '', $this->customJavascriptOnload ) . "}\n"; 
				}else{
					$onLoad = '';
				}
				
                // test if customJavascript is set
                if ( $this->customJavascript != '' ) 
                    $code .= "\n<script type=\"text/javascript\">\n" . $this->customJavascript . $onLoad . "</script>\n";

                if ( $code == '' ) return $tpl_source;

                // replace head end tag
                return eregi_replace( "</head>", $code . "</head>", $tpl_source );

            }

            /**
             *	This function will add custom javascript to the template head
             *
             *	@param $jsCode	   Javascript code to add
             *	@param $prepend    (Optional) Boolean that defines if code should be prepended
             *	@param $onload     (Optional) Boolean that defines if code should be added after page load
             */
            function addJavascript( $jsCode, $prepend = false, $onload = false ) {

				// check if we really have js code
				if ( empty( $jsCode ) ) return;

                if ( $onload ){
                    if ( $prepend ) return array_unshift( $this->customJavascriptOnload, $jsCode );
                    else            return array_push(    $this->customJavascriptOnload, $jsCode );
                }

                if ( $prepend ) return $this->customJavascript = $jsCode . $this->customJavascript;
				else            return $this->customJavascript = $this->customJavascript . $jsCode;
            }


            /**
             *	This function will add custom javascript url to the template head
             *
             *	@param $jsUrl	   Javascript url to add
             */
            function addJavascriptUrl( $jsUrl ) {
                $this->customJavascriptUrls[] = $jsUrl;
            }

            /**
             *	This function will add custom css to the template head
             *
             *	@param $cssCode	   Css code to add
             *	@param $prepend    (Optional) Boolean that defines if code should be prepended
             */
            function addCss( $cssCode, $prepend = false ) {

                if (!$prepend) $this->customCss = $this->customCss . $cssCode . "\n";
                else           $this->customCss = $cssCode . $this->customCss . "\n";
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
                $this->assign( $name, $form->toArray() );
            }

            /**
             *	This function will get the name of the template.
             *
             *	@internal
             */
            function _getTemplateName( $file='' ) {
                
                $ext = '';
                if ( strrchr( $file, '.' ) ) {
                    $ext  = substr( strrchr( $file, '.' ), 1 );
                }
                if ( file_exists( $file ) ) {
                    if ( '.' . $ext != YD_TPL_EXT ) {
                        trigger_error(
                            'The specified file ' . $file . ' is not a valid template file (wrong file extension)',
                            YD_ERROR
                        );
                    }
                    return realpath( $file );
                }
                $this->template_dir = realpath( $this->template_dir );
                if ( empty( $file ) ) {
                    $file = basename( YD_SELF_FILE, '.' . substr( strrchr( YD_SELF_FILE, '.' ), 1 ) );
                }
                if ( is_file( rtrim( $this->template_dir, '/\\' ) . '/' . $file . YD_TPL_EXT ) ) {
                    $tplName = $file . YD_TPL_EXT;
                } else {
                    $tplName = $file;
                }
                if ( ! is_file( rtrim( $this->template_dir, '/\\' ) . '/' . $tplName ) ) {
                    trigger_error( 'Template not found: ' . $tplName, YD_ERROR );
                }
                return $tplName;
            }

        }

    // Use PHP templates
    } else {

        /**
         *  This class is template engine based on PHP.
         *
         *  @ingroup YDFramework
         */
        class YDTemplate extends YDBase {

            /**
             *	This is the class constructor for the YDTemplate class. By default, it looks in the same directory as
             *	the current script to find the templates.
             */
            function YDTemplate() {

                // Initialize the parent
                $this->YDBase();

                // Set the default template directory
                $this->template_dir = YDConfig::get( 'YD_TEMPLATE_DIR', YD_SELF_DIR );

                // The variables to use
                $this->vars = array();

            }

            /**
             *  Assign a a new variable to the template.
             *
             *  @param  $name   The name of the variable.
             *  @param  $value  The value of the variable.
             */
            function assign( $name, $value ) {
                $this->vars[ $name ] = $value;
            }

            /**
             *	This function will parse the template and will display the parsed contents.
             *
             *	You can specify the name of the template which should be in the template directory. If no name is specified,
             *	the basename of the PHP script with the extension '.tpl' will be used.
             *
             *	This function automatically adds some variables to the template, which you can use as well in the template:
             *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI.
             *
             *	@param $file		(optional) The name of the template you want to parse and output.
             */
            function display( $file='' ) {
                $this->fetch( $file, true );
            }

            /**
             *	This function will parse the template and will return the parsed contents.
             *
             *	You can specify the name of the template which should be in the template directory. If no name is specified,
             *	the basename of the PHP script with the extension '.tpl' will be used.
             *
             *	This function automatically adds some variables to the template, which you can use as well in the template:
             *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI.
             *
             *	@param $file		(optional) The name of the template you want to parse and output.
             *	@param $display		(optional) Whether the output should be displayed or returned.
             *
             *	@returns	This function returns the output of the parsed template.
             */
            function fetch( $file='', $display=false ) {

                // Add some default variables
                $this->assign( 'YD_ACTION', YDRequest::getActionName() );

                // Get the template name
                $tplName = $this->_getTemplateName( $file );

                // Extract the variables to the local namespace
                extract( $this->vars );

                // Start buffering the output
                ob_start();

                // Include the template
                include( $tplName );

                // Get the buffer contents
                $result = ob_get_contents();

                // Clean up
                ob_end_clean();

                // Display the template or return the result
                if ( $display == true ) {
                    echo( $result );
                } else {
                    return $result;
                };

            }

            /**
             *	This function will parse the template and will assign the parsed contents to the template.
             *
             *	You can specify the name of the template which should be in the template directory. If no name is specified,
             *	the basename of the PHP script with the extension '.tpl' will be used.
             *
             *	This function automatically adds some variables to the template, which you can use as well in the template:
             *	YD_FW_NAME, YD_FW_VERSION, YD_FW_NAMEVERS, YD_FW_HOMEPAGE, YD_SELF_SCRIPT, YD_SELF_URI.
             *
             *	@param $name		Name you want to use for this fetched template for referencing it in the template.
             *	@param $file		The name of the template you want to parse and output.
             *	@param $cache_id	(optional) ID for the cache of the template (must be unique).
             *	@param $compile_id	(optional) ID for the compilation of the template (must be unique).
             *
             *	@returns	This function returns the output of the parsed template.
             */
            function assignFetchedTemplate( $name, $file, $cache_id=null, $compile_id=null ) {
                $fetched = $this->fetch( $file, $cache_id, $compile_id, false );
                $this->assign( $name, $fetched );
            }


            /**
             *	This function will assign multiple values from an array
             */
            function assignArray( $values ) {
                foreach( $values as $k => $v ){
                    $this->assign( $k, $v );
                }
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
                $this->assign( $name, $form->toArray() );
            }

            /**
             *	This function will get the name of the template.
             *
             *	@internal
             */
            function _getTemplateName( $file='' ) {
                
                $ext = '';
                if ( strrchr( $file, '.' ) ) {
                    $ext  = substr( strrchr( $file, '.' ), 1 );
                }
                if ( file_exists( $file ) ) {
                    if ( '.' . $ext != YD_TPL_EXT ) {
                        trigger_error(
                            'The specified file ' . $file . ' is not a valid template file (wrong file extension)',
                            YD_ERROR
                        );
                    }
                    return realpath( $file );
                }
                $this->template_dir = realpath( $this->template_dir );
                if ( empty( $file ) ) {
                    $file = basename( YD_SELF_FILE, '.' . substr( strrchr( YD_SELF_FILE, '.' ), 1 ) );
                }
                if ( is_file( rtrim( $this->template_dir, '/\\' ) . '/' . $file . YD_TPL_EXT ) ) {
                    $tplName = rtrim( $this->template_dir, '/\\' ) . '/' . $file . YD_TPL_EXT;
                } else {
                    $tplName = rtrim( $this->template_dir, '/\\' ) . '/' . $file;
                }
                if ( ! is_file( $tplName ) ) {
                    trigger_error( 'Template not found: ' . $file, YD_ERROR );
                }
                return $tplName;
            }

        }

    }

    /**
     *	This template function loads a translation from a global variables $_GLOBALS['t'] with specified name.
     *
     *  @param  $params   The parameters for the modifier. The parameter "w" gives the name of the translation. The
     *                    parameter lower indicates if the translation needs to be returned as lowercase or not.
     *
     *  @returns    The named translation.
     */
    function YDTemplate_function_t( $params ) {

        // Check if the word is specified
        if ( isset( $params['w'] ) ) {

            // Get the word and lowercase setting
            $word = $params['w'];
            $lower = ( @ $params['lower'] == 'yes' ) ? true : false;

            // Remove unneeded parameters
            unset( $params['lower'] );
            unset( $params['w'] );

            // Get the translated word
            $word = t( $word, $params );

            // Return it
            return ( $lower === true ) ? strtolower( $word ) : $word;

        // No word specified
        } else {

            // Return an empty string
            return '';

        }

    }

    /**
     *	This template modifier converts a piece of text to BBCode.
     *
     *  @param  $text   The text to convert
     *
     *  @returns    The text formatted as HTML.
     */
    function YDTemplate_modifier_bbcode( $text ) {
        YDInclude( 'YDBBCode.php' );
        $cls = new YDBBCode();
        return $cls->toHtml( $text, true, false, true, YDRequest::getCurrentUrl() );
    }

    /**
     *	This template modifier converts an url to an absolute one
     *
     *  @param  $url   The url to convert
     *
     *  @returns    The url formatted as an absolute url.
     */
    function YDTemplate_modifier_absoluteurl( $url ) {
        return YDUrl::makeLinkAbsolute( $url );
    }

    /**
     *	This template modifier formats a numeric value to a human readable file size.
     *
     *  @param  $size   The numeric value to format.
     *
     *  @returns    The filesize in a human readable format.
     */
    function YDTemplate_modifier_fmtfileize( $size ) {
        return YDStringUtil::formatFileSize( $size );
    }

    /**
     *	This template modifier converst an array to a string using the specified separator.
     *
     *  @param  $array      The array to convert.
     *  @param  $separator  (optional) The separator to use. Default is ", ".
     *
     *  @returns    The array as a string.
     */
    function YDTemplate_modifier_implode( $array, $separator=', ' ) {
        return implode( $separator, $array );
    }

    /**
     *	This template modifier generates a timestamp.
     *
     *  @param  $string     The string to convert to a timestamp.
     *
     *  @returns    The timestamp as a numeric value.
     */
    function YDTemplate_make_timestamp( $string ) {
        if ( empty( $string ) ) {
            $string = "now";
        }
        if ( is_numeric( $string ) && $string != -1 ) {
            return intval( $string );
        }
        if ( preg_match( '/^\d{14}$/', $string ) ) {
            $time = mktime(
                substr( $string, 8, 2 ), substr( $string, 10, 2 ), substr( $string, 12, 2 ),
                substr( $string, 4, 2 ), substr( $string, 6, 2 ), substr( $string,0 ,4 )
            );
            return $time;
        }
        $time = ( int ) strtotime( $string );
        if ( $time > 0 ) {
            return $time;
        } else {
            return time();
        }
    }

    /**
     *	This template modifier formats a timestamp as a string using the strftime function.
     *
     *  @param  $string     The timestamp to format.
     *  @param  $format     (optional) The format to use. Default is '%b %d, %Y'.
     *  @param  $default_date   (optional) If null and no string is given, the current time will be used.
     *
     *  @returns    The formatted date.
     */
    function YDTemplate_modifier_date_format( $string, $format='%b %d, %Y', $default_date=null ) {
        if( $string != '' ) {
            return strftime( $format, YDTemplate_make_timestamp( $string ) );
        } elseif ( isset( $default_date ) && $default_date != '' ) {
            return strftime( $format, YDTemplate_make_timestamp( $default_date ) );
        } else {
            return;
        }
    }

    /*
     *	This template modifier will dump the contents of the variable using var_dump.
     *
     *  @param  $obj    The object to dump.
     */
    function YDTemplate_modifier_dump( $obj ) {
        var_dump( $obj );
        return;
    }

    /*
     *	This template modifier will dump the contents of the variable using var_dump.
     *
     *  @param  $obj    The object to dump.
     */
    function YDTemplate_modifier_timesince( $obj ) {
        return YDStringUtil::timesince( $obj );
    }

    /**
     *	This template modifier formats an url with an optional title.
     *
     *  @param  $url        The url to format as a HTML hyperlink
     *  @param  $title      (optional) If null and no string is given, the url will be used.
     *
     *  @returns    The formatted url.
     */
    function YDTemplate_modifier_url( $url, $title=null ) {
        if ( empty( $title ) ) {
            $title = $url;
        }
        return sprintf( '<a href="%s">%s</a>', $url, $title );
    }

    /**
     *	This template modifier replaces a state code with a name
     *
     *  @param  $statecode       The state code
     *
     *  @returns    The state name.
     */
    function YDTemplate_modifier_state( $statecode ) {

        require_once( YD_DIR_HOME_CLS . '/YDList.php');

        foreach( YDList::states() as $country => $stateList ){
            if( isset( $stateList[ $statecode ] ) ){
               return $stateList[ $statecode ];
            }
        }

        return $statecode;
    }

?>
