<?php

    /*

       Yellow Duck Framework version 2.0
       (c) copyright 2004 Pieter Claerhout, pieter@yellowduck.be

    */

    // Check if the YDFramework is loaded.
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'ERROR: Yellow Duck Framework is not loaded.' );
    }

    /**
     *  This class defines a RSS/ATOM feed. You can use this class to create RSS
     *  and Atom feeds in a very easy and straightforward way.
     *
     *  @todo
     *      Make sure all the different RSS formats and items are supported. If
     *      there are possible variations, make sure we support these as well.
     *
     *  @todo
     *      Add an option to add a feed image.
     *
     *  @todo
     *      Remove the support for MBOX files. We also need some checking of
     *      the type that was selected and raise errors accordingly.
     */
    class YDFeedCreator {

        /**
         *  This is the class constructor for the YDFeedCreator class.
         */
        function YDFeedCreator() {

            // Include the feedcreator library
            require_once( YD_DIR_3RDP . '/feedcreator/feedcreator.class.php' );

            // Instantiate the feed creator
            $this->ufc = new UniversalFeedCreator();

            // Set the generator version
            $this->ufc->generatorVersion = YD_FW_NAMEVERS . ' - YDFeedCreator';

        }

        /**
         *  Function to set the title of the feed.
         *
         *  @param $title The title of the feed.
         */
        function setTitle( $title ) {
            $this->ufc->title = YDStringUtil::encodeString( $title );
        }

        /**
         *  Function to set the description of the feed.
         *
         *  @param $desc The description of the feed.
         */
        function setDescription( $desc ) {
            $this->ufc->description = YDStringUtil::encodeString( $desc );
        }

        /**
         *  Function to set the link of the feed.
         *
         *  @param $desc The link of the feed.
         */
        function setLink( $link ) {
            $this->ufc->link = $link;
        }

        /**
         *  This function will add a new item to the feed. You need to at least
         *  give in a title, link. A description is advised but optional. Also
         *  a GUID is optional. If no GUID is given, an automatic one will be
         *  created which is the md5 checksum of the different elememts.
         *
         *  @param $title The title of the feed item.
         *  @param $link  The link to the feed item.
         *  @param $desc  The description for the feed item.
         *  @param $guid  The guid for the feed item.
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
            $this->ufc->addItem( $item );

        }

        /**
         *  This function will return the feed in the specified format. The
         *  following formats are recognized:
         *
         *  - RSS0.91
         *  - RSS1.0
         *  - RSS2.0
         *  - PIE0.1
         *  - MBOX
         *  - OPML
         *
         *  The default format is RSS2.0.
         *
         *  @param $format The format in which the items should be converted.
         *
         *  @returns String with the data in the requested format.
         */
        function toXml( $format='RSS2.0' ) {

            // Create the feed and return it
            return $this->ufc->createFeed( strtoupper( $format ) );

        }

        /**
         *  This function will output the feed in the specified format. It will
         *  send the output directly to the browser. The following formats are 
         *  recognized:
         *
         *  - RSS0.91
         *  - RSS1.0
         *  - RSS2.0
         *  - PIE0.1
         *  - MBOX
         *  - OPML
         *
         *  The default format is RSS2.0.
         *
         *  @param $format The format in which the items should be outputted.
         */
        function outputXml( $format='RSS2.0' ) {

            // Set the headers
            header( 'Content-type: text/xml' );

            // Create the feed and return it
            echo( $this->ufc->createFeed( strtoupper( $format ) ) );

        }

    }

?>
