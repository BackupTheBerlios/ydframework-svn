<?php

    // Class
    class ydcmfgalleryitem extends YDCmfContentNode {

        // Constructor
        function ydcmfgalleryitem() {

            // Initialize the parent
            $this->YDCmfContentNode();

            // Add the path to the image
            $this->addMeta( 'relative_path', 'property_0', 'string', false, null );

        }

    }

?>