<?php

    /*
     *  Yellow Duck Framework version 2.0
     *  (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be
     */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    // Includes
    require_once( 'YDError.php' );
    require_once( 'YDFSFile.php' );

    /**
     *  This class defines an image file.
     */
    class YDFSImage extends YDFSFile {

        /**
         *  The class constructor of the YDImage class takes the path to the
         *  image as it's only argument. It will then provide you with a number
         *  of functions to get the properties of the image and also provides
         *  some actions like generating thumbnails.
         *
         *  @param $path Path of the image.
         */
        function YDImage( $path ) {

            // Initialize the parent class
            $this->YDFSFile( $path );

        }

        /**
         *  Function to output the thumbnail of an image. The function directly
         *  outputs the thumbnail to the client including the right headers
         *  needed to display the image.
         *
         *  This function can cache the thumbnails and regenerate them on the
         *  fly if needed. The cached thumbnails are stored in the temp
         *  directory of the Yellow Duck framework and have the extension
         *  "tmn". You can delete these automatically as they will be recreated
         *  on the fly if needed.
         *
         *  @param $width  The maximum width of the thumbnail.
         *  @param $height The maximum height of the thumbnail.
         *  @param $cache  (optional) Indicate if the thumbnails should be
         *                 cached or not. By default, caching is turned on.
         */
        function outputThumbnail( $width, $height, $cache=true ) {

            // Check if the GD library is loaded.
            if ( ! extension_loaded( 'gd' ) ) {
                  $this->_error( 'YD_gd_not_installed' );
            }

            // Include phpThumb
            require_once( 'phpThumb/phpthumb.class.php' );

            // Create a new thumbnail object
            $thumb = new phpThumb();
            $thumb->src = $this->getAbsolutePath();

            // Set the options for the creation of thumbnails
            $thumb->config_nohotlink_enabled = false;
            $thumb->config_cache_directory = YD_DIR_TEMP;

            // Set the width and the height
            $thumb->w = $width;
            $thumb->h = $height;

            // Create the cached thumbnail
            $cacheFName = $thumb->GenerateCachedFilename();
            $cacheFName .= $this->getLastModified();
            $cacheFName .= $this->getAbsolutePath();
            $cacheFName = YD_TMP_PRE . md5( $cacheFName ) . '.tmn';
            $cacheFName = YD_DIR_TEMP . '/' . $cacheFName;

            // Check if caching is enabled
            if ( $cache == true ) {

               // Output the cached version if any
               if ( is_file( $cacheFName ) ) {

                  // Create a file object for the image
                  $img = new YDFSImage( $cacheFName );

                  // Output the thumbnail
                  header( 'Content-type: ' . $img->getMimeType() );
                  echo( $img->getContents() );

                  // Stop the execution of the script
                  die();

              }

            }

            // Width should be positive integer
            if ( ! is_int( $width ) ) {
                  $this->_error();
            }
            if ( $width < 1 ) {
                  $this->_error();
            }

            // Height should be positive integer
            if ( ! is_int( $width ) ) {
                  $this->_error();
            }
            if ( $width < 1 ) {
                  $this->_error();
            }

            // Generate the thumbnail
            $thumb->GenerateThumbnail();

            // Check if caching is enabled
            if ( $cache == true ) {

               // Cache the thumbnail
               $thumb->RenderToFile( $cacheFName );

            }

            // Output the thumbnail
            $thumb->OutputThumbnail();

        }

        /**
         *  This function will return the size of the image in pixels.
         *
         *  @returns The imagesize in pixels. This is returned as an array of
         *           which the first element is the width, the second element is
         *           the height in pixels.
         */
        function getImageSize() {

            // Check for the getimagesize function
            if ( ! function_exists( 'getimagesize' ) ) {
                new YDFatalError(
                    'The "getimagesize" function does not exists. Make sure '
                    . 'that the GD libraries are loaded before using the '
                    . 'YDFSImage::getImageSize function.'
                );
            }

            // Get the parameters
            $params = getimagesize( $this->getAbsolutePath() );

            // Get the first two elements
            $imgSize = array_slice( $params, 0, 2 );

            // Return the image size
            return $imgSize;

        }

        /**
         *  This function will return the width of the image in pixels.
         *
         *  @returns The width in pixels.
         */
        function getWidth() {

            // Get the image size
            $imgSize = $this->getImageSize();

            // Return the image height
            return $imgSize[0];

        }

        /**
         *  This function will return the height of the image in pixels.
         *
         *  @returns The height in pixels.
         */
        function getHeight() {

            // Get the image size
            $imgSize = $this->getImageSize();

            // Return the image height
            return $imgSize[1];

        }

        /**
         *  Function that returns the type of the image. Currently, it only
         *  supports GIF, JPG and PNG. This might change in the future.
         *
         *  @returns The type of the image, which is either jpg, png or gif.
         */
        function getImageType() {

            // Get the parameters
            $params = getimagesize( $this->getAbsolutePath() );

            // Return the right image type
            switch( $params[2] ) {

                // GIF image
                case 1:
                    return 'gif';

                // JPG image
                case 2:
                    return 'jpeg';

                // PNG image
                case 3:
                    return 'png';

            }

            // Raise error about unsupported image type
            new YDFatalError(
                'The getImageType function does not support the file format of '
                . 'the file "' . $this->getAbsolutePath() . '"'
            );

         }

        /**
         *  Function that returns the MIME type of the image. Currently, it only
         *  supports GIF, JPG and PNG. This might change in the future.
         *
         *  @returns The type of the image, which is either jpg, png or gif.
         */
        function getMimeType() {

            // Get the image type
            $type = $this->getImageType();

            // Return the mime type
            return 'image/' . strtolower( $type );

         }

         /**
          *  This function is used to output an error image.
          *
          *  @param $name (optional) Name of the error image. Default image that
          *               is shown is the generic "YD_ydfsimage_fatal_error".
          *
          *  @internal
          */
        function _error( $name='YD_ydfsimage_fatal_error' ) {

            // Create a file object for the image
            $img = new YDFSImage( YD_DIR_HOME . '/images/' . $name . '.gif' );

            // Output the thumbnail
            header( 'Content-type: ' . $img->getMimeType() );
            echo( $img->getContents() );

            // Stop the execution of the script
            die();

        }

    }

?>
