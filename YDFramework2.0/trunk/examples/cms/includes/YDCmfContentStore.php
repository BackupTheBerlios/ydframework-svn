<?php

    // Class definition
    class YDCmfContentStore extends YDDatabaseTree {

        // Class constructor
        function YDCmfContentStore( $db, $table ) {

            // Publish the variables
            $this->db      = & $db;
            $this->table   =   $table;

            // The field definitions
            $this->field_id         = 'id';
            $this->field_parent_id  = 'parent_id';
            $this->field_sort       = 'name';
            $this->default_obj_type = 'YDCmfContentNode';

            // Get the list of supported language codes
            $this->languages = YDCmfLanguage::getSupportedLanguages();

            // Get the tree
            $this->YDDatabaseTree(
                $this->db, $this->table, $this->field_id, $this->field_parent_id, $this->field_sort
            );

            // Add the title fields
            foreach ( array_values( $this->languages ) as $language ) {
                $this->addField( 'title_' . $language );
            }

            // Add the property fields
            foreach ( range( 0, 10 ) as $i ) {
                foreach ( array_values( $this->languages ) as $language ) {
                    $this->addField( 'property_' . $i . '_' . $language );
                }
            }

            // Add the generic fields
            $this->addFields(
                array( 'created', 'created_by', 'modified', 'modified_by', 'content_type', 'can_delete' )
            );

            // Initialize the store
            if ( ! defined( 'YD_CMS_STORE_INITIALIZED' ) ) {
                $this->init();
            }

        }

        // Init the database store
        function init() {

            // Only install if we are running in debug mode
            if ( YDConfig::get( 'YD_DEBUG', 0 ) > 0 ) {
                include_once( dirname( __FILE__ ) . '/YDCmfContentStoreInstaller.php' );
            }

            // Indicate that we are initialized
            @ define( 'YD_CMS_STORE_INITIALIZED', 1 );

        }

        // Convert a node to an object
        function _fromNodeArray( $node ) {

            // Return false if no node is found
            if ( ! $node ) {
                return $node;
            }

            // Fix some fields
            $content_type = strtolower( $node['content_type'] );

            // Include the right file
            if ( $content_type != 'ydcmfcontentnode' ) {

                // Get the module file to include
                $file = YDCMS_DIR_MOD . '/' . $content_type . '/' . $content_type . '.php';

                // Check if the file exists
                if ( ! file_exists( $file ) ) {
                    trigger_error( 'Module not found: ' . $content_type, YD_ERROR );
                }

                // Include the module file
                include_once( $file );

            }

            // Create the object
            $obj = new $content_type();

            // Initialize the object
            $obj->fromArray( $node );

            // Return the object
            return $obj;

        }

        // Convert an object to an array
        function _toNodeArray( $node ) {
            $node->touch();
            return $node->toArray();
        }


    }

?>
