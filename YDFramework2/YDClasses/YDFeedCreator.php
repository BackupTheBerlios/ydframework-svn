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
    require_once( 'YDBase.php' );
    require_once( 'YDError.php' );
    require_once( 'YDStringUtil.php' );
    require_once( 'feedcreator/feedcreator.class.php' );

    /**
     *  This class defines a RSS/ATOM feed. You can use this class to create RSS
     *  and Atom feeds in a very easy and straightforward way. If you set up
     *  your class instance, you can automatically output to the different
     *  versions of RSS and ATOM with the same source data.
     */
    class YDFeedCreator extends YDBase {

        /**
         *  This is the class constructor for the YDFeedCreator class.
         */
        function YDFeedCreator() {

            // Initialize YDBase
            $this->YDBase();

            // Instantiate the feed creator
            $this->_ufc = new UniversalFeedCreator();

            // Set the generator version
            $this->_ufc->generatorVersion = YD_FW_NAMEVERS . ' - YDFeedCreator';

        }

        /**
         *  Function to set the title of the feed.
         *
         *  @param $title The title of the feed.
         */
        function setTitle( $title ) {
            $this->_ufc->title = YDStringUtil::encodeString( $title );
        }

        /**
         *  Function to set the description of the feed.
         *
         *  @param $desc The description of the feed.
         */
        function setDescription( $desc ) {
            $this->_ufc->description = YDStringUtil::encodeString( $desc );
        }

        /**
         *  Function to set the link of the feed.
         *
         *  @param $link The link of the feed.
         */
        function setLink( $link ) {
            $this->_ufc->link = $link;
        }

        /**
         *  This function will set the image properties for the feed. It
         *  requires you to specify a title, url and link for the image. The
         *  description is an optional element.
         *
         *  @remark
         *      Feed images are only supported for RSS feeds. ATOM feeds do not
         *      support a feed image. If you decide to output the feed to ATOM,
         *      the information set with this function will be discarded.
         *
         *  @param $title The title of the feed image.
         *  @param $link  The link to the feed image.
         *  @param $url   The URL of the feed image.
         *  @param $descr (optional) The description for the feed image.
         */
        function setImage( $title, $link, $url, $desc=null ) {

            // Instantiate the FeedImage object
            $img = new FeedImage();

            // Add the required properties
            $img->title = YDStringUtil::encodeString( $title );
            $img->link = $link;
            $img->url = $url;

            // Add the optional properties
            if ( $desc != null ) {
                $img->desc = $desc;
            }

            // Add the image to the feed
            $this->_ufc->image = $img;

        }

        /**
         *  This function will add a new item to the feed. You need to at least
         *  give in a title, link. A description is advised but optional. Also
         *  a GUID is optional. If no GUID is given, an automatic one will be
         *  created which is the md5 checksum of the different elememts.
         *
         *  @param $title The title of the feed item.
         *  @param $link  The link to the feed item.
         *  @param $desc  (optional) The description for the feed item.
         *  @param $guid  (optional) The guid for the feed item.
         */
        function addItem( $title, $link, $desc=null, $guid=null ) {

            // Create a new item
            $item = new FeedItem();

            // Add the title and link
            $item->title = YDStringUtil::encodeString( $title );
            $item->link = $link;

            // Add the description if any
            if ( $desc != null ) {
                $item->description = YDStringUtil::encodeString( $desc );
            }

            // Add a guid or create one
            if ( $guid != null ) {
                $item->guid = $guid;
            } else {

                // Create the checksum parts
                $checkSum = $item->title . $item->link;

                // Add the description if any
                if ( $desc != null ) {
                    $checkSum .= $item->description;
                }

                // Assign the guid
                $item->guid = md5( $checkSum );

            }

            // Add the item to the feed
            $this->_ufc->addItem( $item );

        }

        /**
         *  This function will return the feed in the specified format. The
         *  following formats are recognized:
         *
         *  - RSS0.91
         *  - RSS1.0
         *  - RSS2.0
         *  - ATOM
         *
         *  @remark
         *      The default format is "RSS2.0". If you specify no argument
         *      indicating the requested format, the "RSS2.0" format will be
         *      used.
         *
         *  @param $format (optional) The format in which the items should be
         *                 converted.
         *
         *  @returns String with the data in the requested format.
         */
        function toXml( $format='RSS2.0' ) {

            // Convert the format to uppercase
            $format = strtoupper( $format );

            // Check if the format is an allowed one
            if ( ! in_array(
                $format, array( 'RSS0.91', 'RSS1.0', 'RSS2.0', 'ATOM' )
            ) ) {
                new YDFatalError(
                    'The YDFeedCreator does not support the format called '
                    . '"' . $format . '". Only the formats "RSS0.91", '
                    , '"RSS1.0", "RSS2.0" and "ATOM" are supported.'
                );
            }

            // The Universal Feed Creator has a different name for ATOM feeds
            if ( $format == 'ATOM' ) {
                $format = 'PIE0.1';
            }

            // Create the feed and return it
            return $this->_ufc->createFeed( strtoupper( $format ) );

        }

        /**
         *  This function will output the feed in the specified format. It will
         *  send the output directly to the browser. The following formats are
         *  recognized:
         *
         *  - RSS0.91
         *  - RSS1.0
         *  - RSS2.0
         *  - ATOM
         *
         *  @remark
         *      The default format is "RSS2.0". If you specify no argument
         *      indicating the requested format, the "RSS2.0" format will be
         *      used.
         *
         *  @param $format (optional) The format in which the items should be
         *                 converted.
         */
        function outputXml( $format='RSS2.0' ) {

            // Set the headers
            header( 'Content-type: text/xml' );

            // Create the feed and return it
            echo( $this->toXml( $format ) );

        }

        /**
         *  This function will output the feed in the specified format and will
         *  color the XML elements using HTML code. The following formats are
         *  recognized:
         *
         *  - RSS0.91
         *  - RSS1.0
         *  - RSS2.0
         *  - ATOM
         *
         *  @remark
         *      The default format is "RSS2.0". If you specify no argument
         *      indicating the requested format, the "RSS2.0" format will be
         *      used.
         *
         *  @param $format (optional) The format in which the items should be
         *                 converted.
         *
         *  @returns HTML colored XML data.
         */
        function getColoredXml( $format='RSS2.0', $color='darkred' ) {

            // Create the feed as XML
            $xml = $this->toXml( $format );

            // Convert to pre and HTML entities
            $xml = '<pre>' . htmlentities( $xml ) . '</pre>';

            // Color code the xml
            $xml = str_replace(
                '&lt;', '<font color="' . $color . '">&lt;', $xml
            );
            $xml = str_replace( '&gt;', '&gt;</font>', $xml );

            // Return the colored XML
            return $xml;

        }

    }

?>
