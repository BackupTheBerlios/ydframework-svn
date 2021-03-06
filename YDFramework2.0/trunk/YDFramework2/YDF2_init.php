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

    /*! \mainpage Yellow Duck Framework
     *
     *	The Yellow Duck Framework is web application framework created by Pieter Claerhout. More information can be
     *	found on http://ydframework.berlios.de/.
     *
     *	(c) Copyright 2002-2007 Pieter Claerhout
     *
     *	Webpage: http://ydframework.berlios.de/
     *
     *	Author: Pieter Claerhout, pieter@yellowduck.be
     */

    /**
     *  @addtogroup YDFramework Core
     */

    // Set the error reporting correctly.
    error_reporting( E_ALL );

    // Disable magic_quotes_runtime
    set_magic_quotes_runtime( 0 );

    // Get the path delimiter and newline
    if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' ) {
        @define( 'YD_CRLF', "\r\n" );
        @define( 'YD_PATHDELIM', ';' );
    } elseif ( strtoupper( PHP_OS ) == 'DARWIN' ) {
        @define( 'YD_CRLF', "\r" );
        @define( 'YD_PATHDELIM', ':' );
    } else {
        @define( 'YD_CRLF', "\n" );
        @define( 'YD_PATHDELIM', ':' );
    }

    // Global framework constants
    @define( 'YD_FW_REVISION', 'unknown' );
    @define( 'YD_FW_NAME', 'Yellow Duck Framework' );
    @define( 'YD_FW_VERSION', '2.1 (build ' . YD_FW_REVISION . ')' );
    @define( 'YD_FW_NAMEVERS', YD_FW_NAME . ' ' . YD_FW_VERSION );
    @define( 'YD_FW_HOMEPAGE', 'http://ydframework.berlios.de/' );
    @define( 'YD_FW_POWERED_BY', sprintf( 'Powered by <a href="%s">%s</a>', YD_FW_HOMEPAGE, YD_FW_NAMEVERS ) );
    @define( 'YD_FW_COPYRIGHT', '(c) 2002-2007 Pieter Claerhout, pieter@yellowduck.be' );

    // Directory paths
    @define( 'YD_DIR_HOME', dirname( __FILE__ ) );
    @define( 'YD_DIR_HOME_CLS', YD_DIR_HOME . '/YDClasses' );
    @define( 'YD_DIR_HOME_ADD', YD_DIR_HOME . '/addons' );
    @define( 'YD_DIR_TEMP', YD_DIR_HOME . '/temp' );

    // File and URL constants
    @define( 'YD_SELF_SCRIPT', $_SERVER['PHP_SELF'] );
    if ( isset( $_SERVER['PATH_TRANSLATED'] ) && is_file( $_SERVER['PATH_TRANSLATED'] ) ) {
        @define( 'YD_SELF_FILE', $_SERVER['PATH_TRANSLATED'] );
    } else {
        @define( 'YD_SELF_FILE', $_SERVER['SCRIPT_FILENAME'] );
    }
    @define( 'YD_SELF_DIR', dirname( YD_SELF_FILE ) );
    @define( 'YD_SELF_URI', $_SERVER['REQUEST_URI'] );

    // Extensions and prefixes
    @define( 'YD_TPL_EXT', '.tpl' );
    @define( 'YD_SCR_EXT', '.php' );
    @define( 'YD_TMP_PRE', 'YDF_' );

    // Error constants
    @define( 'YD_ERROR', E_USER_ERROR );
    @define( 'YD_WARNING', E_USER_WARNING );
    @define( 'YD_NOTICE', E_USER_NOTICE );

    // For servers that do not send the request uri
    if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        if ( isset ( $_SERVER['QUERY_STRING'] ) ) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }

    // Keep some global variables
    $GLOBALS['YD_SQL_QUERY'] = array();

    // Update the include path
    $includePath = YD_SELF_DIR;
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS;
    $includePath .= YD_PATHDELIM . YD_DIR_HOME;
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS . '/YDFormElements';
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS . '/YDFormRenderers';
    $includePath .= YD_PATHDELIM . YD_DIR_HOME_CLS . '/YDDatabaseDrivers';
    if ( is_dir( YD_SELF_DIR . '/includes' ) ) {
        $includePath .= YD_PATHDELIM . YD_SELF_DIR . '/includes';
    }
    $includePath .= YD_PATHDELIM . YD_DIR_HOME . '/3rdparty';
    $includePath .= YD_PATHDELIM . YD_DIR_HOME . '/addons';
    if ( $handle = opendir( YD_DIR_HOME . '/addons' ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
           if ( substr( $file, 0, 1 ) != '.' && is_dir( YD_DIR_HOME . '/addons/' . $file ) ) {
                $includePath .= YD_PATHDELIM . YD_DIR_HOME . '/addons/' . $file;
           }
        }
        closedir( $handle );
    }
    if ( ini_get( 'include_path' ) != '' ) {
        $includePath .= YD_PATHDELIM . ini_get( 'include_path' );
    }
    $GLOBALS['YD_INCLUDE_PATH'] = explode( YD_PATHDELIM, $includePath );
    @ini_set( 'include_path', $includePath );

    // Constants
    define( 'YD_CONFIG_VAR', 'YDF2_GLOBAL_CONFIG' );
    define( 'YD_LOCALE_KEY', 'YD_LOCALE' );

    // This is the list of support languages
    $GLOBALS[ 'YD_LANGUAGES' ] = array(
        'en'  => array( 'eng', 'english', 'en_US' ),
        'eng' => array( 'en', 'english', 'en_US' ),
        'cs'  => array( 'csy', 'czech' ),
        'csy' => array( 'czech', 'cs' ),
        'da'  => array( 'dan', 'danish' ),
        'dan' => array( 'danish', 'da' ),
        'nl'  => array( 'nld', 'dutch' ),
        'nld' => array( 'dutch', 'nl' ),
        'nlb' => array( 'belgian', 'dutch-belgian', 'nl_BE', 'nl' ),
        'ena' => array( 'australian', 'english-aus', 'en_AU', 'en' ),
        'enc' => array( 'canadian', 'english-can', 'en_CA', 'en' ),
        'enz' => array( 'english-nz', 'en_NZ', 'en' ),
        'uk'  => array( 'english-uk', 'en_UK', 'en', 'enu', 'eng' ),
        'enu' => array( 'uk', 'english-uk', 'en_UK', 'en', 'eng' ),
        'us'  => array( 'american', 'american english', 'american-english', 'english-american', 'english-us', 'english-usa', 'enu', 'usa', 'en_US', 'en' ),
        'fi'  => array( 'fin', 'finnish' ),
        'fin' => array( 'finnish', 'fi' ),
        'fr'  => array( 'fra', 'french' ),
        'fra' => array( 'french', 'fr_FR', 'fr_FR', 'fr' ),
        'frb' => array( 'french-belgian', 'fr_BE', 'fr' ),
        'frc' => array( 'french-canadian', 'fr_CA', 'fr' ),
        'frs' => array( 'french-swiss', 'fr_SW', 'fr' ),
        'de'  => array( 'deu', 'german' ),
        'deu' => array( 'german', 'de' ),
        'dea' => array( 'german-austrian', 'de_AU', 'de' ),
        'des' => array( 'german-swiss', 'swiss', 'de_SW', 'de' ),
        'el'  => array( 'ell', 'greek' ),
        'ell' => array( 'greek', 'el' ),
        'hu'  => array( 'hun', 'hungarian' ),
        'hun' => array( 'hungarian', 'hu' ),
        'is'  => array( 'isl', 'icelandic' ),
        'isl' => array( 'icelandic', 'is' ),
        'it'  => array( 'ita', 'italian' ),
        'ita' => array( 'italian', 'it' ),
        'its' => array( 'italian-swiss', 'it', 'it_SW' ),
        'no'  => array( 'nor', 'norwegian' ),
        'nor' => array( 'norwegian-bokmal', 'no', 'no_BO' ),
        'non' => array( 'norwegian-nynorsk', 'no', 'no_NY' ),
        'pl'  => array( 'plk', 'polish' ),
        'plk' => array( 'polish', 'pl' ),
        'pt'  => array( 'ptg', 'portuguese' ),
        'ptg' => array( 'portuguese', 'pt' ),
        'ptb' => array( 'portuguese-brazil', 'pt_BR', 'pt' ),
        'ru'  => array( 'rus', 'russian' ),
        'rus' => array( 'russian', 'ru' ),
        'sk'  => array( 'sky', 'slovak' ),
        'sky' => array( 'slovak', 'sk' ),
        'es'  => array( 'spanish' ),
        'esp' => array( 'spanish', 'es' ),
        'esm' => array( 'spanish-mexican', 'es_ME', 'es' ),
        'esn' => array( 'spanish-modern', 'es_MO', 'es' ),
        'sv'  => array( 'sve', 'swedish' ),
        'sve' => array( 'swedish', 'sv' ),
        'tr'  => array( 'trk', 'turkish' ),
        'trk' => array( 'turkish', 'tr' ),
    );

    // Default translations
    $GLOBALS['t'] = array(
        'years' => 'years',
        'months' => 'months',
        'weeks' => 'weeks',
        'days' => 'days',
        'hours' => 'hours',
        'minutes' => 'minutes',
        'ago' => 'ago',
    );

    /**
     *	This function will include a file from the filesystem. It works similar to the require_once function but it
     *	knows about the include path for the Yellow Duck Framework.
     *
     *	@param $file	File to be included.
     *
     *  @ingroup YDFramework
     */
    function YDInclude( $file ) {

        // Check if the file is an absolute path
        if ( is_file( $file ) ) {
            include_once( $file );
            return;
        }

        // Check for known classes in known paths

        // For YDTemplate, check which one needs to be included (optimize the file includes)
        // If Smarty: include YDTemplate_smarty.php
        // If PHP: include YDTemplate_php.php
        // If not defined: include YDTemplate.php

        // Search the include path
        foreach ( $GLOBALS['YD_INCLUDE_PATH'] as $include ) {
            if ( $include != false && is_file( $include . '/' . $file ) ) {
                include_once( $include . '/' . $file );
                return;
            }
        }

        // Raise an error
        trigger_error(
            'Failed to include the file: ' . $file . ' The file was not found in the include path.', YD_ERROR
        );

    }

    /**
     *	This function will include a upgrade file that simulates php functions not present in this php system
     *
     *  @ingroup YDFramework
     */
    function YDIncludeCompatibility( $core = true, $gettext = true, $array = true ) {
        if ( $core )    include_once( dirname( __FILE__ ) . '/3rdparty/upgrade/upgrade.php' );
        if ( $gettext ) include_once( dirname( __FILE__ ) . '/3rdparty/upgrade/ext/gettext.php' );
        if ( $array )   include_once( dirname( __FILE__ ) . '/3rdparty/upgrade/ext/array.php' );
    }

    /**
     *	This function will add a marker to the global timer.
     *
     *	@param $name	The name of the marker.
     *
     *  @ingroup YDFramework
     */
    function YDGlobalTimerMarker( $name ) {
        if ( ! isset( $GLOBALS['timer'] ) ) {
            include_once( dirname( __FILE__ ) . '/YDClasses/YDUtil.php' );
            $GLOBALS['timer'] = new YDTimer();
        }
        $GLOBALS['timer']->addMarker( $name );
    }

    /**
     *	This function will fix magic quotes.
     *
     *	@param $value	The value to fix.
     *
     *	@returns	The fixed value.
     *
     *  @ingroup YDFramework
     */
    function YDRemoveMagicQuotes( & $value ) {
        if ( get_magic_quotes_gpc() == 1 ) {
            if ( is_array( $value ) ) {
                $result = array();
                foreach ( $value as $key=>$val) {
                    $result[ $key ] = is_array( $val ) ? YDRemoveMagicQuotes( $val ) : stripslashes( $val );
                }
                return $result;
            } else {
                return stripslashes( $value );
            }
        }
        return $value;
    }

    /**
     *  This function is used to load translations from a $GLOBALS['t'] array.
     *
     *  @param  $t      The name of the translated string.
     *  @param  $params (optional) An array of parameters that are passed to the sprintf function.
     *
     *  @returns    The translated string.
     *
     *  @ingroup YDFramework
     */
    function t( $t, $params=array() ) {
        if ( empty( $t ) ) {
            return '';
        }

		// check gettext patern
		if ( preg_match ( "/^\_\((\"|.{2})(.+)(\"|.{2})\)$/", $t, $res ) ){
			$out = gettext( $res[2] );
		}else{
			$t = strtolower( $t );
			$out = ( array_key_exists( $t, $GLOBALS['t'] ) ? $GLOBALS['t'][$t] : "#$t#" );
		}
        array_unshift( $params, $out );
        return call_user_func_array( 'sprintf', $params );
    }

    /**
     *  This is the base class for all other YD classes.
     *
     *  @ingroup YDFramework
     */
    class YDBase {

        /**
         *  Class constructor for the YDBase class.
         */
        function YDBase() {
        }

        /**
         *  This function returns the name of the current class.
         *
         *  @returns    The name of the current class in lowercase.
         */
        function getClassName() {
            return strtolower( get_class( $this ) );
        }

        /**
         *  This function returns true if the specified method is existing in the current class.
         *
         *  @param      $name   The name of the class method to look for.
         *
         *  @returns    Boolean indicating if the class method exists or not.
         */
        function hasMethod( $name ) {
            return method_exists( $this, $name );
        }

        /**
         *  This function checks if this object instance is of a specific class or is based on a derived class of the
         *  given class. The class name is case insensitive.
         *
         *  @param $class   The object type you want to check against.
         *
         *  @returns    Boolean indicating if this object is of the specified class.
         */
        function isSubClass( $class ) {
            return YDObjectUtil::isSubClass( $this, $class );
        }

        /**
         *  Function to get all the ancestors of this class. The list will contain
         *  the parent class first, and then it's parent class, etc. You can pass both
         *  the name of the class or an object instance to this function.
         *
         *  @returns    Array with all the ancestors.
         */
        function getAncestors() {
            return YDObjectUtil::getAncestors( $this->getClassName() );
        }

        /**
         *  This function will serialize the object.
         */
        function serialize() {
            return YDObjectUtil::serialize( $this );
        }

        /**
         *  This function sets a variable in the object.
         *
         *  @param  $name   The variable name.
         *  @param  $value  The variable value.
         *
         *  @returns        Returns a reference to the variable
         */
        function & set( $name, $value ) { 
            $this->$name = $value; 
            return $this->$name; 
        } 

        /**
         *  This function deletes a variable in the object.
         *
         *  @param  $name   The variable name.
         */
        function unsetVar( $name ) { 
            if ( isset( $this->$name ) ) unset( $this->$name ); 
        } 

        /**
         *  This function indicates if a variable in the object exists or not.
         *
         *  @param  $name   The variable name.
         *  @param  $null   (Optional) If false, indicates variables that are null
         *                  as they don't exist. Default: false.
         *
         *  @returns        Returns a boolean indicating if a variable exists or not.
         */
        function exists( $name, $null=false ) { 
            return $null ? array_key_exists( $name, $this ) : isset( $this->$name ); 
        } 

        /**
         *  This function returns a variable value in the object.
         *
         *  @param  $name  The variable name.
         *
         *  @returns       Returns the variable value.
         */
        function get( $name ) { 
            return $this->exists( $name, true ) ? $this->$name : null; 
        }

        /** 
         *  This function returns an array with all the values of the object. 
         * 
         *  @returns  An array with all the values. 
         */ 
        function toArray() { 
            $array = get_object_vars( $this ); 
            foreach ( $array as $key => $val ) { 
                if ( substr( $key, 0, 1 ) == '_' ) { 
                    unset( $array[ $key ] ); 
                } 
            } 
            return $array; 
        } 

    }

    /**
     *	This is the class that hold the global configuration.
     *
     *  @ingroup YDFramework
     */
    class YDConfig extends YDBase {

        /**
         *	This function initializes the global configuration.
         *
         *	@internal
         */
        function _init() {
            if ( ! isset( $GLOBALS[ YD_CONFIG_VAR ] ) ) {
                $GLOBALS[ YD_CONFIG_VAR ] = array();
            }
        }

        /**
         *	This funtion add a new configuration variable to the configuration.
         *
         *	@param	$name		The name of the configuration variable.
         *	@param	$value		The value of the configuration variable.
         *	@param	$override	(optional) Override the current value or not. Default is true.
         */
        function set( $name, $value, $override=true ) {
            if ( YDConfig::exists( $name ) ) {
                if ( $override ) {
                    $GLOBALS[ YD_CONFIG_VAR ][ $name ] = $value;
                }
            } else {
                $GLOBALS[ YD_CONFIG_VAR ][ $name ] = $value;
            }
        }

        /**
         *	This function returns a variable from the configuration. If the configuration variable doesn't exist, it
         *	returns a fatal error.
         *
         *	@param	$name       The name of the configuration variable to retrieve.
         *  @param  $default    (optional) If not null, this value will be returned if the configuration setting doesn't
         *                      exist in the configuration.
         *
         *	@returns	The value of the configuration variable.
         */
        function get( $name, $default=null ) {
            return YDConfig::exists( $name ) ? $GLOBALS[ YD_CONFIG_VAR ][ $name ] : $default;
        }

        /**
         *	This function checks if the configuration value is set or not.
         *
         *	@returns	Boolean indicating if the configuration value is set or not.
         */
        function exists( $name ) {
            YDConfig::_init();
            return isset( $GLOBALS[ YD_CONFIG_VAR ][ $name ] );
        }

        /**
         *	This function dumps the contents of the configuration.
         */
        function dump() {
            YDConfig::_init();
            YDDebugUtil::dump( $GLOBALS[ YD_CONFIG_VAR ], 'YDConfig contents' );
        }

    }

    /**
     *	This is the class that hold the locale information.
     *
     *  @ingroup YDFramework
     */
    class YDLocale extends YDBase {

        /**
         *	@param	$locale		Sets the current locale
         *	@param	$domain		(Optional) Gettext: Filename to search in locale directory
         *	@param	$directory	(Optional) Gettext: Directory name where language directories are stored
         *	@param	$charset	(Optional) Gettext: Custom charset, eg 'UTF-8'. By default not set.
         */
        function set( $locale, $domain = null, $directory = null, $charset = null ) {

            // Check if the locale exists or not
            if ( ! array_key_exists( strtolower( $locale ), $GLOBALS[ 'YD_LANGUAGES' ] ) ) {
                trigger_error( 'You specified an invalid locale: "' . strtolower( $locale ) . '"', YD_ERROR );
            }

            // Get the locales
            $locales = $GLOBALS[ 'YD_LANGUAGES' ][ $locale ];

            // Add the current locale
            array_unshift( $locales, $locale );
            array_unshift( $locales, LC_ALL );

            // Add another one
            array_push( $locales, substr( $locale, 0, 2 ) . '_' . strtoupper( substr( $locale, 0, 2 ) ) );

            // Set the locale
            @call_user_func_array( 'setlocale', array_unique( $locales ) );

            // Set the locale
            YDConfig::set( YD_LOCALE_KEY, $locale );

            // check if we want to use gettext
            if ( is_string( $domain ) ){

                // set environment
                $_ENV["LANG" ]      = $locale;
                $_ENV["LANGUAGE"]   = $locale;
                $_ENV["LC_MESSAGE"] = $locale;
                $_ENV["LC_ALL"]     = $locale;

                // added php5 environment flag
                @putenv( "LANGUAGE=" . $locale );

                // set gettext domain
                bindtextdomain( $domain, $directory );
                textdomain( $domain );

                // apply charset
                if ( is_string( $charset ) ){
                    bind_textdomain_codeset( $domain, $charset );
                }

                return;
            }

            // initialize t array
            if ( ! isset( $GLOBALS['t'] ) ) {
                $GLOBALS['t'] = array();
            }

            // if directories array exist we must include all files
            if ( isset( $GLOBALS[ 'YD_LANGUAGES_DIRECTORIES' ] ) )
                foreach ( $GLOBALS[ 'YD_LANGUAGES_DIRECTORIES' ] as $dir )
                    @include( $dir . '/' . YDLocale::get() . '.php' );
        }

        /**
         *	@returns	The current locale.
         */
        function get() {
            YDConfig::set( YD_LOCALE_KEY, 'en', false );
            return strtolower( YDConfig::get( YD_LOCALE_KEY ) );
        }

        /**
         *	This function adds a new directory
         *
         *	@param	$dir       Directory to add
         */
        function addDirectory( $dir ){
            if ( !isset( $GLOBALS[ 'YD_LANGUAGES_DIRECTORIES' ] ) ) {
                $GLOBALS[ 'YD_LANGUAGES_DIRECTORIES' ] = array();
            }
            $GLOBALS[ 'YD_LANGUAGES_DIRECTORIES' ][] = $dir;
            if ( !isset( $GLOBALS['t'] ) ) {
                $GLOBALS['t'] = array();
            }

            // just try to include language file
            @ include_once( $dir . '/' . YDLocale::get() . '.php' );
        }

    }

    /**
     *	This is the base class for all other YD classes.
     *
     *  @ingroup YDFramework
     */
    class YDAddOnModule extends YDBase {

        /**
         *	Class constructor for the YDBase class.
         */
        function YDAddOnModule() {
            $this->_author = 'unknown author';
            $this->_version = '1.0';
            $this->_copyright = 'no copyright';
            $this->_description = 'no description';
        }

    }

    /**
     *	This is the executor class that contains all the logic for executing requests. It will instantiate the request
     *	class and execute the right functions to get the request processed correctly.
     *
     *  @ingroup YDFramework
     */
    class YDExecutor extends YDBase {

        /**
         *	The class constructor for the YDExecutor class.
         *
         *	@param $file	Full path to the file that contains the request.
         */
        function YDExecutor( $file ) {
            $this->_file = $file;
        }

        /**
         *	The execute function will start the YDExecutor class and process the request.
         */
        function execute() {

            // Do nothing if we already processed a request
            if ( defined( 'YD_REQ_PROCESSED' ) ) {
                return;
            }

            // Construct the name of the request class
            $clsName = basename( $this->_file, YD_SCR_EXT );
            $this->clsInst = new $clsName();

            // Check if the object a YDRequest object
            if ( ! YDObjectUtil::isSubClass( $this->clsInst, 'YDRequest' ) ) {
                $ancestors = YDObjectUtil::getAncestors( $this->clsInst );
                trigger_error(
                    'Class "' . $clsName . '" should be derived from the YDRequest class. Currently, this class has the '
                    . 'following ancestors: ' . implode( ' -&gt; ', $ancestors ), YD_ERROR
                );
            }

            // Check if the class is properly initialized
            if ( $this->clsInst->isInitialized() != true ) {
                trigger_error(
                    'Class "' . $clsName . '" is not initialized properly. Make  sure loaded the base class YDRequest '
                    . 'and initialized it.', YD_ERROR
                );
            }

            // Get the action name
            $action = 'action' . $this->clsInst->getActionName();

            // Only if authentication is required
            if ( $this->clsInst->getRequiresAuthentication( $action ) ) {
                $result = $this->clsInst->isAuthenticated();
                if ( $result ) {
                    $this->clsInst->authenticationSucceeded();
                } else {
                    $this->clsInst->authenticationFailed();
                    $this->finish();
                }
            }

            // Check if the action exists
            if ( ! $this->clsInst->hasMethod( $action ) ) {
                $this->clsInst->errorMissingAction( $action );
                $this->finish();
            }

            // Check if the current action is allowed or not and execute errorActionNotAllowed if failed
            if ( ! $this->clsInst->isActionAllowed() ) {
                $this->clsInst->errorActionNotAllowed();
                $this->finish();
            }

            // Process the request
            $this->clsInst->process();
            $this->finish();

        }

        /**
         *	This is the function we use for finishing the request.
         *
         *	@internal
         */
        function finish() {

            // Mark that the request is processed
            define( 'YD_REQ_PROCESSED', 1 );

            // Stop the timer
            $GLOBALS['timer']->finish();

            // Output visible debug info as a comment
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 ) {

                // Total size of include files
                $includeFiles = get_included_files();

                // Calculate the total size
                $includeFilesSize = 0;
                $includeFilesWithSize = array();
                foreach ( $includeFiles as $key=>$includeFile ) {
                    if ( is_file( $includeFile ) ) {
                        $includeFilesSize += filesize( $includeFile );
                        $includeFilesWithSize[ filesize( $includeFile ) ] = realpath( $includeFile );
                    }
                }
                $includeFilesSize = YDStringUtil::formatFileSize( $includeFilesSize );

                // Sort the list of include files by file size
                krsort( $includeFilesWithSize );

                // Convert to a string
                $includeFiles = array();
                foreach ( $includeFilesWithSize as $size=>$file ) {
                    array_push( $includeFiles, YDStringUtil::formatFileSize( $size ) . "\t  " . realpath( $file ) );
                }

                // Create the debug messages
                $debug = YD_CRLF . YD_CRLF . YD_FW_NAMEVERS . ' DEBUG INFORMATION ' . YD_CRLF . YD_CRLF;

                // Create the timings report
                $debug .= 'PROCESSING TIME:' . YD_CRLF . YD_CRLF;
                $debug .= "    Elapsed\t  Diff\t  Marker" . YD_CRLF;
                $timings = $GLOBALS['timer']->getReport();
                foreach ( $timings as $timing ) {
                    $debug .= "    " . $timing[0] . ' ms' . "\t  " . $timing[1] . ' ms' . "\t  " .$timing[2] . YD_CRLF;
                }
                $debug .= YD_CRLF;

                // Create the include file details
                $debug .= 'INCLUDED FILES (' . sizeof( $includeFiles ) . ' files - ' . $includeFilesSize . ')' . YD_CRLF . YD_CRLF;
                $debug .= "    " . implode( "\n    ", $includeFiles ) . YD_CRLF . YD_CRLF;

                // Add the queries if any
                if ( sizeof( $GLOBALS['YD_SQL_QUERY'] ) > 0 ) {
                    $debug .= 'EXECUTED SQL QUERIES (' . sizeof( $GLOBALS['YD_SQL_QUERY'] ) . ' queries)' . YD_CRLF . YD_CRLF;
                    foreach ( $GLOBALS['YD_SQL_QUERY'] as $key=>$query ) {
                        $debug .= "    " . '---- SQL QUERY ' . ($key+1) . ' ----' . YD_CRLF;
                        foreach ( explode( YD_CRLF, trim( $query['trace'] ) ) as $line ) {
                            $debug .= "    " . rtrim( $line ) . YD_CRLF;
                        }
                        $debug .= YD_CRLF . "    SQL Query:" . YD_CRLF;
                        foreach ( explode( YD_CRLF, trim( $query['sql'] ) ) as $line ) {
                            $debug .= "        " . rtrim( $line ) . YD_CRLF;
                        }
                        $debug .= YD_CRLF . "    Query Time: " . $query['time'] . ' ms' . YD_CRLF;
                        $debug .= YD_CRLF;
                    }
                }

                // Output the debug message
                YDDebugUtil::debug( $debug );

            }

            // Output visible debug info
            if ( YDConfig::get( 'YD_DEBUG' ) == 2 ) {

                // Total size of include files
                $includeFiles = get_included_files();

                // Calculate the total size
                $includeFilesSize = 0;
                $includeFilesWithSize = array();
                foreach ( $includeFiles as $key=>$includeFile ) {
                    if ( is_file( $includeFile ) ) {
                        $includeFilesSize += filesize( $includeFile );
                        $includeFilesWithSize[ filesize( $includeFile ) ] = realpath( $includeFile );
                    }
                }
                $includeFilesSize = YDStringUtil::formatFileSize( $includeFilesSize );

                // Sort the list of include files by file size
                krsort( $includeFilesWithSize );

                // Get the timing report
                $timings = $GLOBALS['timer']->getReport();

                // The font to use for the debug output
                $font = ' style="font-family: Arial, Helvetica, sans-serif; text-align: left; vertical-align: top;"';

                // Include the needed libraries
                $debug  = '';

                // Create the output
                $debug .= '<br clear="all"/><hr size="1"/>';
                $debug .= '<p ' . $font . '><b>' . YD_FW_NAMEVERS . ' Debug Information</b></p>';

                // Add the processing time
                $debug .= '<p ' . $font . '><i>Processing time</i></p>';
                $debug .= '<dd><p><table width="90%" border="1" cellspacing="0" cellpadding="2">';
                $debug .= sprintf(
                    '<tr><th %s>Elapsed</th><th %s>Diff</th><th %s>Marker</th></tr>',
                    $font, $font, $font
                );
                foreach ( $timings as $timing ) {
                    $debug .= sprintf(
                        '<tr><td %s>%s ms</td><td %s>%s ms</td><td %s>%s</td></tr>',
                        $font, $timing[0], $font, $timing[1], $font, $timing[2]
                    );
                }
                $debug .= '</table></p></dd>';

                // Add the included files
                $debug .= '<p ' . $font . '><i>Included files (' . $includeFilesSize . ')</i></p>';
                $debug .= '<dd><p><table width="90%" border="1" cellspacing="0" cellpadding="2">';
                $debug .= sprintf( '<tr><th %s>File</th><th %s>Size</th></tr>', $font, $font );
                foreach ( $includeFilesWithSize as $size=>$file ) {
                    $debug .= sprintf(
                        '<tr><td %s>%s</td><td %s>%s</td></tr>',
                        $font, realpath( $file ), $font, YDStringUtil::formatFileSize( $size )
                    );
                }
                $debug .= '</table></p></dd>';

                // Add the query log
                if ( sizeof( $GLOBALS['YD_SQL_QUERY'] ) > 0 ) {
                    $debug .= '<script src="' . YD_SELF_SCRIPT . '?do=JsShMain"></script>';
                    $debug .= '<script src="' . YD_SELF_SCRIPT . '?do=JsShSql"></script>';
                    $debug .= '<link rel="stylesheet" href="' . YD_SELF_SCRIPT . '?do=CssShStyle" type="text/css" />';
                    $debug .= sprintf(
                        '<p ' . $font . '><i>SQL Query Log (%s queries)</i></p>',
                        sizeof( $GLOBALS['YD_SQL_QUERY'] )
                    );
                    $debug .= '<dd><p><table width="90%" border="1" cellspacing="0" cellpadding="2">';
                    $debug .= sprintf(
                        '<tr><th %s>Query #</th><th %s>Trace</th><th %s>SQL Statement</th><th %s>Elapsed</th></tr>',
                        $font, $font, $font, $font );
                    foreach ( $GLOBALS['YD_SQL_QUERY'] as $key=>$query ) {
                        $debug .= sprintf(
                            '<tr><td width="5%%" %s>%s</td><td width="15%%" %s>%s ms</td><td width="40%%" %s><pre>%s</pre></td><td width="40%%" %s><pre class="sh_sql">%s</pre></td></tr>',
                            $font, $key, $font, $query['time'], $font, $query['trace'], $font, $query['sql']
                        );
                    }
                    $debug .= '</table></p></dd>';
                    $debug .= '<script>sh_highlightDocument();</script>';
                    $debug .= '<script>sh_highlightDocument();</script>';
                }

                // Add the elapsed time
                $debug .= '<p ' . $font . '><i>Elapsed Time</i></p>';
                $debug .= '<dd><p>' . $GLOBALS['timer']->getElapsed() . ' ms</p></dd>';

                // Output the debug message
                echo( $debug );

            }

            // Add the elapsed time
            die( YD_CRLF . '<!-- ' . $GLOBALS['timer']->getElapsed() . ' ms -->' );

        }

    }

    // Add if not exists yet
    if ( ! function_exists( 'str_ireplace' ) ) {

        /**
         *  Function to do a case insensitve str_replace
         *
         *  @param  $search     The string to search for.
         *  @param  $replace    The replacement string.
         *  @param  $subject    The string to replace in
         *  @param  $count      (optional) The maximum number of replacements. Default us all.
         *
         *  @returns    The updated string.
         */
        function str_ireplace( $search, $replace, $subject, $count = null ) {
            if ( is_string( $search ) && is_array( $replace ) ) {
                user_error( 'Array to string conversion', E_USER_NOTICE );
                $replace = ( string ) $replace;
            }
            if ( ! is_array( $search ) ) {
                $search = array( $search );
            }
            $search = array_values( $search );
            if ( ! is_array( $replace ) ) {
                $replace_string = $replace;
                $replace = array();
                for ( $i = 0, $c = count( $search ); $i < $c; $i++ ) {
                    $replace[$i] = $replace_string;
                }
            }
            $replace = array_values( $replace );
            $length_replace = count( $replace );
            $length_search = count( $search );
            if ( $length_replace < $length_search ) {
                for ( $i = $length_replace; $i < $length_search; $i++ ) {
                    $replace[$i] = '';
                }
            }
            $was_array = false;
            if ( ! is_array( $subject ) ) {
                $was_array = true;
                $subject = array( $subject );
            }
            $count = 0;
            foreach ( $subject as $subject_key => $subject_value ) {
                foreach ( $search as $search_key => $search_value ) {
                    $segments = explode( strtolower( $search_value ), strtolower( $subject_value ) );
                    $count += count( $segments ) - 1;
                    $pos = 0;
                    foreach ( $segments as $segment_key => $segment_value ) {
                        $segments[$segment_key] = substr( $subject_value, $pos, strlen( $segment_value ) );
                        $pos += strlen( $segment_value ) + strlen( $search_value );
                    }
                    $subject_value = implode( $replace[$search_key], $segments );
                }
                $result[$subject_key] = $subject_value;
            }
            if ( $was_array === true ) {
                return $result[0];
            }
            return $result;
        }

    }

    // Default the locale to English
    YDConfig::set( 'YD_LOCALE', 'en', false );

    // Fix the PHP variables affected by magic_quotes_gpc (which is evil if you ask me ;-)
    if ( ! defined( 'YD_FIXED_MAGIC_QUOTES' ) ) {
        if ( get_magic_quotes_gpc() == 1 ) {
            $_GET = @YDRemoveMagicQuotes( $_GET );
            $_POST = @YDRemoveMagicQuotes( $_POST );
            $_COOKIE = @YDRemoveMagicQuotes( $_COOKIE );
            $_REQUEST = @YDRemoveMagicQuotes( $_REQUEST );
        }
        define( 'YD_FIXED_MAGIC_QUOTES', true );
    }

    // Check if we have the right PHP version
    if ( version_compare( phpversion(), '4.2.0' ) == -1 ) {
        trigger_error( 'PHP version 4.2.0 or greater is required.', YD_ERROR );
    }

    // Set the debugging level
    switch ( @ $_GET['YD_DEBUG'] ) {
        case 2:
            YDConfig::set( 'YD_DEBUG', 2, false );
            break;
        case 1:
            YDConfig::set( 'YD_DEBUG', 1, false );
            break;
        default:
            YDConfig::set( 'YD_DEBUG', 0, false );
            break;
    }

    // Include the base classes
    include_once( YD_DIR_HOME . '/YDClasses/YDUtil.php' );

    // Start the global timer
    $timer = new YDTimer();

?>