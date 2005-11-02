<?php

    /*

        Yellow Duck Framework version 2.0
        (c) Copyright 2002-2005 Pieter Claerhout

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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

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

    /**
     *	This function will include a file from the filesystem. It works similar to the require_once function but it
     *	knows about the include path for the Yellow Duck Framework.
     *
     *	@param $file	File to be included.
     */
    function YDInclude( $file ) {
        if ( is_file( $file ) ) {
            include_once( $file );
            return;
        }
        foreach ( $GLOBALS['YD_INCLUDE_PATH'] as $include ) {
            if ( $include != false && is_file( $include . '/' . $file ) ) {
                include_once( $include . '/' . $file );
                return;
            }
        }
        trigger_error(
            'Failed to include the file: ' . $file . ' The file was not found in the include path.', YD_ERROR
        );
    }

    /**
     *	This function will add a marker to the global timer.
     *
     *	@param $name	The name of the marker.
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
     *  This is the base class for all other YD classes.
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
     */
    class YDLocale extends YDBase {

        /**
         *	@param	$locale	Sets the current locale
         */
        function set( $locale ) {

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

        }

        /**
         *	@returns	The current locale.
         */
        function get() {
            YDConfig::set( YD_LOCALE_KEY, 'en', false );
            return strtolower( YDConfig::get( YD_LOCALE_KEY ) );
        }

    }

    /**
     *	This is the base class for all other YD classes.
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

?>
