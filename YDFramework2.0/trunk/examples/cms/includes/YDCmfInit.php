<?php

    // Initialize the Yellow Duck Framework
    require_once( dirname( __FILE__ ) . '/../../../YDFramework2/YDF2_init.php' );

    // Configure the template class
    YDConfig::set( 'YD_TEMPLATE_ENGINE', 'php' );

    // General constants
    @define( 'YDCMS_CONFIG_FILENAME', 'config' );
    @define( 'YDCMS_DIR_MOD',         rtrim( realpath( dirname( __FILE__ ) . '/../modules' ), '/' ) );
    @define( 'YDCMS_DIR_SKIN',        rtrim( realpath( dirname( __FILE__ ) . '/../skins/default' ), '/' ) );
    @define( 'YDCMS_REQ_EXT',         '.html' );

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDUrl.php' );
    include_once( YD_DIR_HOME_CLS . '/YDForm.php' );
    include_once( YD_DIR_HOME_CLS . '/YDRequest.php' );
    include_once( YD_DIR_HOME_CLS . '/YDDatabase.php' );
    include_once( YD_DIR_HOME_CLS . '/YDDatabaseTree.php' );
    include_once( YD_DIR_HOME_CLS . '/YDTemplate.php' );

    // CMF Includes
    include_once( dirname( __FILE__ ) . '/YDCmfContentStore.php' );

    // Get the list of possible config files
    $config_files = array(
        dirname( __FILE__ ) . '/' . YDCMS_CONFIG_FILENAME . '/' . YDCMS_CONFIG_FILENAME . '_' . $_SERVER['SERVER_NAME'] . '.php',
        dirname( __FILE__ ) . '/' . YDCMS_CONFIG_FILENAME . '/' . YDCMS_CONFIG_FILENAME . '.php'
    );

    // Include the right configuration file
    foreach ( $config_files as $config_file ) {
        if ( file_exists( $config_file ) ) {
            include_once( $config_file );
            break;
        }
    }

    // Global variables
    $GLOBALS['CMS_DB'] = YDDatabase::getInstance(
        YDConfig::get( 'DB_TYPE' ), YDConfig::get( 'DB_NAME' ), YDConfig::get( 'DB_USER' ),
        YDConfig::get( 'DB_PASS' ), YDConfig::get( 'DB_HOST' )
    );

    // The language class
    class YDCmfLanguage {

        // Get the current language
        function getLanguage() {

            // Get the browser object
            $browser = new YDBrowserInfo();

            // Negotiate with the browser
            $language = $browser->getLanguage( YDCmfLanguage::getServerLanguages() );

            // Check if the language was specified by means of a cookie

            // Allow it to override from the query string
            $language_qs = strtolower( YDRequest::getQueryStringParameter( 'lang', null ) );
            if ( ! is_null( $language_qs ) && in_array( $language_qs, YDCmfLanguage::getServerLanguages() ) ) {
                $language = $language_qs;
            }

            // Return the language
            return $language;

        }

        // Get the proper languages
        function getPossibleLanguages() {
            $browser = new YDBrowserInfo();
            $languages = $browser->getBrowserLanguages();
            foreach ( $languages as $key => $language ) {
                if ( ! in_array( $language, YDCmfLanguage::getServerLanguages() ) ) {
                    unset( $languages[ $key ] );
                }
            }
            return $languages;
        }

        // Get the list of backup languages
        function getBackupLanguages() {
            $language  = YDCmfLanguage::getLanguage();
            $languages = YDCmfLanguage::getPossibleLanguages();
            foreach ( $languages as $key => $val ) {
                if ( strcasecmp( $val, $language ) === 0 ) {
                    unset( $languages[$key] );
                }
            }
            array_unshift( $languages, $language );
            return $languages;
        }

        // Get the list of supported languages
        function getSupportedLanguages() {
            return YDConfig::get( 'supported_languages', array( 'English' => 'en' ) );
        }

        // Get the list of server languages
        function getServerLanguages() {
            return array_values( YDCmfLanguage::getSupportedLanguages() );
        }

    }

    // CMF Request
    class YDCmfRequest extends YDRequest {

        // Class constructor
        function YDCmfRequest() {

            // Initialize the parent
            $this->YDRequest();

            // Get the database connection
            $this->db = & $GLOBALS['CMS_DB'];

            // Get the content store
            $this->store = new YDCmfContentStore( $GLOBALS['CMS_DB'], 'content' );

        }

        // Default action
        function actionDefault() {

            // Get the ID of the item we need to retrieve
            $query = trim( $this->getQueryStringParameter( 'q' ), '/' );

            // Check if we need to show the default or not
            if ( empty( $query ) ) {
                $query = '1';
            }

            // Split it up
            $query = explode( '/', $query );

            // Get the language from the query string
            if ( isset( $_GET['lang'] ) && ! in_array( $_GET['lang'], YDCmfLanguage::getServerLanguages() ) ) {
                unset( $_GET['lang'] );
            }

            // Set the default action if needed
            if ( ! isset( $_GET['action'] ) || empty( $_GET['action'] ) ) {
                $_GET['action'] = 'default';
            }

            // Recompose the query string
            $query = implode( '/', $query );

            // Get the node from the store
            if ( is_numeric( $query ) ) {
                $node = $this->store->getNode( $query );
            } else {
                $node = $this->store->getNode( $query, 'name' );
            }

            // Check if the node is found
            if ( ! $node ) {
                trigger_error( 'Node not found: ' . $query, YD_ERROR );
            }

            // Get the name of the action to perform
            $action = $this->getQueryStringParameter( 'action', 'default' );

            // Check if the action exists
            if ( ! $node->hasMethod( 'action' . $action ) ) {
                trigger_error( $action . ' is missing for this node' );
            }

            // Run the action
            call_user_func( array( $node, 'action' . $action ) );

        }

    }

    // The default node definition
    class YDCmfContentNode extends YDBase {

        // Class constructor
        function YDCmfContentNode() {

            // The data for the node
            $this->_nodeData = array();

            // The metadata for the node
            $this->_nodeMeta = array();

            // Create a new template
            $this->tpl = new YDTemplate();

            // Get the database connection
            $this->db = & $GLOBALS['CMS_DB'];

            // Get the content store
            $this->store = new YDCmfContentStore( $GLOBALS['CMS_DB'], 'content' );

            // Add the core properties
            $this->addMeta( 'id', null, 'int', false, null );
            $this->addMeta( 'parent_id', null, 'int', false, null );
            $this->addMeta( 'name', null, 'string', false, null );
            $this->addMeta( 'nleft', null, 'int', false, null, false );
            $this->addMeta( 'nright', null, 'int', false, null, false );
            $this->addMeta( 'nlevel', null, 'int', false, null, false );
            $this->addMeta( 'title', null, 'string', true, null );
//            $this->addMeta( 'property', 'property_0', 'string', true, null );
//            $this->addMeta( 'property_other', 'property_1', 'string', false, null );
            $this->addMeta( 'created', null, 'datetime', false, time() );
            $this->addMeta( 'created_by', null, 'string', false, YDBrowserInfo::getComputerName() );
            $this->addMeta( 'modified', null, 'datetime', false, time() );
            $this->addMeta( 'modified_by', null, 'string', false, YDBrowserInfo::getComputerName() );
            $this->addMeta( 'content_type', null, 'string', false, $this->getClassName() );
            $this->addMeta( 'can_delete', null, 'bool', false, 1, false );

        }

        // Add a field definition
        function addMeta(
            $name, $field, $type, $translated, $default, $required=true, $null=false, $maxlength=false, $minlength=false
        ) {

            // Use the name of the property if the field name is not specified
            if ( is_null( $field ) ) {
                $field = $name;
            }

            // Add the metadata
            $this->_nodeMeta[ $name ] = array(
                'type' => $type, 'field' => strtolower( $field ), 'translated' => $translated, 'default' => $default,
                'required' => $required,'null' => $null, 'maxlength' => $maxlength, 'minlength' => $minlength
            );

        }

        // From an array
        function fromArray( $array ) {

            // Add the data
            $this->_nodeData = array_merge( $this->_nodeData, $array );

            // Get the used properties
            $used_languages  = YDCmfLanguage::getSupportedLanguages();
            $used_properties = array();
            foreach ( array_keys( $this->_nodeMeta ) as $key ) {
                if ( YDStringUtil::startsWith( $this->_nodeMeta[ $key ]['field'], 'property_' ) ) {
                    foreach ( $used_languages as $language ) {
                        array_push( $used_properties, $this->_nodeMeta[ $key ]['field'] . '_' . $language );
                    }
                }
            }

            // Remove the unused properties
            foreach ( array_keys( $this->_nodeData ) as $key ) {
                if ( YDStringUtil::startsWith( $key, 'property_' ) && ! in_array( $key, $used_properties ) ) {
                    unset( $this->_nodeData[ $key ] );
                }
            }

        }

        // Return the array
        function toArray() {
            $data = $this->_nodeData;
            $data['content_type'] = $this->getClassName();
            return $data;
        }

        // Touch the date/time on the node
        function touch() {
            if ( is_null( $this->getValue( 'created' ) ) ) {
                $this->setValue( 'created', time() );
            }
            $this->setValue( 'modified', time() );
            if ( is_null( $this->getValue( 'created_by' ) ) ) {
                $this->setValue( 'created_by', YDBrowserInfo::getComputerName() );
            }
            $this->setValue( 'modified_by', YDBrowserInfo::getComputerName() );
        }

        // Check if this is a valid value
        function isValidValue( $name ) {
            return in_array( strtolower( $name ), array_keys( $this->_nodeMeta ) );
        }

        // Check for a valid field
        function checkValueIsValid( $name ) {
            if ( ! $this->isValidValue( $name ) ) {
                trigger_error( 'Unknown field: ' . $name, YD_ERROR );
            }
        }

        // Get a value of a node
        function getValue( $name, $default=null ) {

            // Fail if the field doesn't exist
            $this->checkValueIsValid( $name );

            // Get the languages
            $language = YDCmfLanguage::getLanguage();

            // Get the proper value
            if ( isset( $this->_nodeData[ $this->_nodeMeta[ $name ]['field'] . '_' . $language ] ) ) {
                return $this->_nodeData[ $this->_nodeMeta[ $name ]['field'] . '_' . $language ];
            }

            // Get the value if set, otherwise, use the default value
            return isset( $this->_nodeData[ $name ] ) ? $this->_nodeData[ $name ] : $default;

        }

        // Get a translated value
        function getTranslatedValue( $name, $default=null, $language=null ) {

            // Fail if the field doesn't exist
            $this->checkValueIsValid( $name );

            // Get the default language if not specified
            if ( empty( $language ) ) {
                $language = YDCmfLanguage::getLanguage();
            }

            // Get the field name
            $field_name = $this->_nodeMeta[ $name ]['field'] . '_' . $language;

            // Get the value if set, otherwise, use the default value
            return isset( $this->_nodeData[ $field_name ] ) ? $this->_nodeData[ $field_name ] : $default;

        }

        // Get a link to the item
        function getHref( $action=null, $language=null ) {

            // Get the language
            $language = is_null( $language ) ? YDCmfLanguage::getLanguage() : $language;

            // Get the action name
            $action   = is_null( $action ) ? $_GET['action'] : $action;

            // Get the URL
            $url = dirname( YD_SELF_SCRIPT ) . '/' . $this->getValue( 'name' ) . YDCMS_REQ_EXT . '?';

            // Add the query string
            if ( ! empty( $action ) && strtolower( $action ) != 'default' ) {
                $url .= 'action=' . rawurlencode( $action ) . '&';
            }
            $url .= 'lang=' . rawurlencode( $language );

            // Return the URL
            return YDUrl::makeLinkAbsolute( $url );

        }

        // Get a link with the title
        function getTitleLink( $action=null, $language=null, $title=null ) {
            if ( is_null( $title ) ) {
                $title = $this->getTranslatedValue( 'title' );
            }
            return sprintf( '<a href="%s">%s</a>', $this->getHref( $action, $language ), $title );
        }

        // Get the actions defined in this class
        function getPossibleActions() {
            $actions = get_class_methods( $this );
            foreach ( $actions as $key=>$action ) {
                if ( YDStringUtil::startsWith( $action, 'action' ) ) {
                    $actions[$key] = strtolower( substr( $action, strlen( 'action' ) ) );
                } else {
                    unset( $actions[$key] );
                }
            }
            return $actions;
        }

        // Get the children of the node
        function getChildren( $order=null ) {
            return $this->store->getChildren( $this->getValue( 'id' ), false, $order );
        }

        // Get the path to the node
        function getPath() {
            return $this->store->getPath( $this->getValue( 'id' ), true );
        }

        // Set a value of a node
        function setValue( $name, $val, $language=null ) {

            // Get the name of the field
            $field = $this->_nodeMeta[ $name ]['field'];

            // Check if it's a valid language
            if ( ! is_null( $language ) && ! in_array( $language, YDCmfLanguage::getServerLanguages() ) ) {
                trigger_error( 'Language ' . $language . ' is not supported' );
            }

            // Check if the item needs translation
            if ( $this->_nodeMeta[ $name ]['translated'] == true && ! empty( $language ) ) {
                $this->_nodeData[ $field . '_' . $language ] = $val;
            } else {
                if ( YDStringUtil::startsWith( $field, 'title' ) || YDStringUtil::startsWith( $field, 'property' ) ) {
                    foreach ( YDCmfLanguage::getServerLanguages() as $language ) {
                        $this->_nodeData[ $field . '_' . $language ] = $val;
                    }
                } else {
                    $this->_nodeData[ $field ] = $val;
                }
            }

        }

        // The default node action
        function actionDefault() {

            // Display the template
            $this->display();

        }

        // Display a named template
        function display( $name=null ) {

            // Assign the values
            $this->tpl->assign( 'node', $this );
            $this->tpl->assign( 'store', $this->store );

            // Add the action name
            $this->tpl->assign( 'YD_MODULE_ACTION', strtolower( $_GET['action'] ) );

            // Add the generic stuff
            $this->tpl->assign( 'languages', YDCmfLanguage::getSupportedLanguages() );
            $this->tpl->assign( 'language',  YDCmfLanguage::getLanguage() );

            // Get the name of the class
            $clazz = $this->getClassName();

            // If no name specified, use the class name
            $name = ( is_null( $name ) ? $clazz : $name ) . '.tpl';

            // Check if we need to use the standard or custom one
            if (
                ! file_exists( YDCMS_DIR_SKIN . '/' . $name )
                &&
                ! file_exists( YDCMS_DIR_MOD . '/' . $clazz . '/' . $name )
                &&
                ! file_exists( dirname( __FILE__ ) . '/' . $name )
            ) {
                trigger_error( 'Template not found: ' . $name, YD_ERROR );
            }

            // Get the path to the template
            if ( file_exists( YDCMS_DIR_SKIN . '/' . $name ) ) {
                $tplFile = YDCMS_DIR_SKIN . '/' . $name;
            } else {
                if ( $clazz == 'ydcmfcontentnode' ) {
                    $tplFile = dirname( __FILE__ ) . '/' . $name;
                } else {
                    $tplFile = YDCMS_DIR_MOD . '/' . $clazz . '/' . $name;
                }
            }

            // Assign the name of the template
            $this->tpl->assign( 'tplFile', realpath( $tplFile ) );

            // Display the template
            $this->tpl->display( $tplFile );

        }

    }

?>