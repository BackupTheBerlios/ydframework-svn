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

    // Includes
    include_once( dirname( __FILE__ ) . '/../../YDClasses/YDUrl.php' );
    include_once( dirname( __FILE__ ) . '/../../YDClasses/YDFileSystem.php' );

    /**
     *	This class defines a RSS/ATOM feed. You can use this class to create RSS and Atom feeds in a very easy and
     *	straightforward way. If you set up your class instance, you can automatically output to the different versions
     *	of RSS and ATOM with the same source data.
     */
    class YDFeedCreator extends YDAddOnModule {

        /**
         *	This is the class constructor for the YDFeedCreator class.
         */
        function YDFeedCreator() {

            // Initialize YDBase
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'Pieter Claerhout';
            $this->_version = '1.0';
            $this->_copyright = '(c) 2005 Pieter Claerhout, pieter@yellowduck.be';
            $this->_description = 'This addon class defines a RSS/ATOM feed. You can use this class to create RSS and '
                               . 'Atom feeds in a very easy and straightforward way. If you set up your class instance, '
                               . 'you can automatically output to the different versions of RSS and ATOM with the same '
                               . 'source data.';

            // Start with the general variables
            $this->_encoding = 'ISO-8859-1';
            $this->_title = '';
            $this->_description = '';
            $this->_link = YDRequest::getCurrentUrl();
            $this->_items = array();
            $this->_generator = YD_FW_NAMEVERS . ' - YDFeedCreator';

        }

        /**
         *	Function to set the encoding of the feed.
         *
         *	@param $encoding	The encoding of the feed.
         */
        function setEncoding( $encoding ) {
            $this->_encoding = strtoupper( $encoding );
        }

        /**
         *	Function to set the title of the feed.
         *
         *	@param $title	The title of the feed.
         */
        function setTitle( $title ) {
            $this->_title = YDStringUtil::encodeString( $title, true );
        }

        /**
         *	Function to set the description of the feed.
         *
         *	@param $desc	The description of the feed.
         */
        function setDescription( $desc ) {
            $desc = YDUrl::makeLinksAbsolute( $desc, $this->_link );
            $this->_description = YDStringUtil::encodeString( $desc, true );
        }

        /**
         *	Function to set the link of the feed.
         *
         *	@param $link	The link of the feed.
         */
        function setLink( $link ) {
            $this->_link = YDUrl::makeLinkAbsolute( $link );
        }

        /**
         *	This function will add a new item to the feed. You need to at least give in a title, link. A description is
         *	advised but optional. Also a GUID is optional. If no GUID is given, an automatic one will be created which
         *	is the md5 checksum of the different elememts.
         *
         *	@param $title	        The title of the feed item.
         *	@param $link	        The link to the feed item.
         *	@param $desc	        (optional) The description for the feed item.
         *	@param $guid	        (optional) The guid for the feed item.
         *  @param $enclosure       (optional) The url of the enclosure.
         *  @param $enclosure_size  (optional) The size in bytes of the enclosure.
         *  @param $enclosure_type  (optional) The mime-type of the enclosure.
         *
         *  @remark
         *      Enclosures are only supported for ATOM and RSS 2.0 feeds.
         */
        function addItem( $title, $link, $desc=null, $guid=null, $enclosure=null, $enclosure_size=null, $enclosure_type=null ) {

            $link = YDUrl::makeLinkAbsolute( $link, $this->_link );
            $desc = YDUrl::makeLinksAbsolute( $desc, $this->_link );

            if ( empty( $guid ) ) {
                $checkSum = $this->_link . $title . $link;
                if ( $desc != null ) { $checkSum .= $desc; }
                $guid = md5( $checkSum );
            }

            if ( ! is_null( $enclosure ) && ( is_null( $enclosure_size ) || is_null( $enclosure_type ) ) ) {
                trigger_error( 'Enclosures must have both type and size specified!', YD_ERROR );
            }

            $item = array(
                'title' => YDStringUtil::encodeString( $title, true ), 'link' => htmlentities( $link ),
                'description' => $desc, 'guid' => $guid, 'enclosure' => $enclosure, 'enclosure_size' => $enclosure_size,
                'enclosure_type' => $enclosure_type
            );

            $this->_items[ $guid ] = $item;

        }

        /**
         *	This function will return the feed in the specified format. The following formats are recognized: RSS0.91,
         *	RSS1.0, RSS2.0, ATOM
         *
         *	@remark
         *		The default format is "RSS2.0". If you specify no argument indicating the requested format, the "RSS2.0"
         *		format will be used.
         *
         *	@param $format	(optional) The format in which the items should be converted.
         *
         *	@returns	String with the data in the requested format.
         */
        function toXml( $format='RSS2.0' ) {

            // Convert the format to uppercase
            $format = strtoupper( $format );

            // Check if the format is an allowed one
            if ( ! in_array(
                $format, array( 'RSS0.91', 'RSS1.0', 'RSS2.0', 'ATOM' )
            ) ) {
                trigger_error(
                    'The YDFeedCreator does not support the format called "' . $format . '". Only the formats "RSS0.91"'
                    . ', "RSS1.0", "RSS2.0" and "ATOM" are supported.', YD_ERROR
                );
            }

            // Start with the first XML line
            $xml = '<?xml version="1.0" encoding="' . $this->_encoding . '"?>';

            // Formatter for RSS 0.91
            if ( $format == 'RSS0.91' || $format == 'RSS2.0' ) {
                if ( $format == 'RSS0.91' ) {
                    $xml .= '<rss version="0.91">';
                } else {
                    $xml .= '<rss version="2.0">';
                }
                $xml .= '<channel>';
                $xml .= '<title>' . $this->_title . '</title>';
                if ( ! empty( $this->_description ) ) {
                    $xml .= '<description>' . $this->_description . '</description>';
                }
                $xml .= '<link>' . htmlentities( $this->_link ) . '</link>';
                $xml .= '<generator>' . $this->_generator . '</generator>';
                foreach ( $this->_items as $item ) {
                    $item['description'] = YDStringUtil::encodeString( $item['description'], true );
                    $xml .= '<item>';
                    $xml .= '<title>' . $item['title'] . '</title>';
                    $xml .= '<link>' . $item['link'] . '</link>';
                    $xml .= '<guid isPermanlink="false">' . $item['guid'] . '</guid>';
                    if ( ! empty( $item['description'] ) ) {
                        $xml .= '<description>' . $item['description'] . '</description>';
                    }
                    if ( $format == 'RSS2.0' && ! is_null( $item['enclosure'] ) ) {
                        $xml .= '<enclosure url="' . YDStringUtil::encodeString( $item['enclosure'], true )
                             .  '" length="' . YDStringUtil::encodeString( $item['enclosure_size'], true )
                             .  '" type="' . YDStringUtil::encodeString( $item['enclosure_type'], true )
                             .  '" />';
                    }
                    $xml .= '</item>';
                }
                $xml .= '</channel>';
                $xml .= '</rss>';
            }

            // Formatter for RSS1.0
            if ( $format == 'RSS1.0' ) {
                $xml .= '<rdf:RDF';
                $xml .= ' xmlns="http://purl.org/rss/1.0/"';
                $xml .= ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"';
                $xml .= ' xmlns:dc="http://purl.org/dc/elements/1.1/">';
                $xml .= '<channel rdf:about="">';
                $xml .= '<title>' . $this->_title . '</title>';
                $xml .= '<description>' . $this->_description . '</description>';
                $xml .= '<link>' . htmlentities( $this->_link ) . '</link>';
                $xml .= '<items>';
                $xml .= '<rdf:Seq>';
                foreach ( $this->_items as $item ) {
                    $xml .= '<rdf:li rdf:resource="' . $item['link'] . '"/>';
                }
                $xml .= '</rdf:Seq>';
                $xml .= '</items>';
                $xml .= '</channel>';
                foreach ( $this->_items as $item ) {
                    $item['description'] = YDStringUtil::encodeString( $item['description'], true );
                    $xml .= '<item rdf:about="' . $item['link'] . '">';
                    $xml .= '<dc:format>text/html</dc:format>';
                    $xml .= '<title>' . $item['title'] . '</title>';
                    $xml .= '<link>' . $item['link'] . '</link>';
                    if ( ! empty( $item['description'] ) ) {
                        $xml .= '<description>' . $item['description'] . '</description>';
                    }
                    $xml .= '</item>';

                }
                $xml .= '</rdf:RDF>';
            }

            // Formatter for ATOM
            if ( $format == 'ATOM' ) {
                $xml .= '<feed version="0.3" xmlns="http://purl.org/atom/ns#">';
                $xml .= '<title>' . $this->_title . '</title>';
                if ( ! empty( $this->_description ) ) {
                    $xml .= '<tagline>' . $this->_description . '</tagline>';
                }
                $xml .= '<link rel="alternate" type="text/html" href="' . htmlentities( $this->_link ) . '"/>';
                $xml .= '<id>' . htmlentities( $this->_link ) . '</id>';
                $xml .= '<generator>' . $this->_generator . '</generator>';
                foreach ( $this->_items as $item ) {
                    $xml .= '<entry>';
                    $xml .= '<title>' . $item['title'] . '</title>';
                    $xml .= '<link rel="alternate" type="text/html" href="' . $item['link'] . '"/>';
                    $xml .= '<id>' . $item['guid'] . '</id>';
                    if ( ! empty( $item['description'] ) ) {
                        $xml .= '<content type="text/html" mode="escaped" xml:base="' . $item['link'] . '"><![CDATA[ ';
                        $xml .= $item['description'];
                        $xml .= ' ]]></content>';
                    }
                    if ( ! is_null( $item['enclosure'] ) ) {
                        $xml .= '<link rel="enclosure" href="' . YDStringUtil::encodeString( $item['enclosure'], true )
                             .  '" length="' . YDStringUtil::encodeString( $item['enclosure_size'], true )
                             .  '" type="' . YDStringUtil::encodeString( $item['enclosure_type'], true )
                             .  '" />';
                    }
                    $xml .= '</entry>';
                }
                $xml .= '</feed>';
            }

            // Return the XML
            return $xml;

        }

        /**
         *	This function will output the feed in the specified format. It will send the output directly to the browser.
         *	The following formats are recognized: RSS0.91, RSS1.0, RSS2.0, ATOM
         *
         *	@remark
         *		The default format is "RSS2.0". If you specify no argument indicating the requested format, the "RSS2.0"
         *	format will be used.
         *
         *	@param $format	(optional) The format in which the items should be converted.
         */
        function outputXml( $format='RSS2.0' ) {

            // Get the XML data
            $xml = $this->toXml( $format );

            // Set the correct headers
            $etag = '"' . md5( $xml ) . '"';
            header( 'ETag: ' . $etag );
            $inm = split( ',', getenv( 'HTTP_IF_NONE_MATCH' ) );
            foreach ( $inm as $i ) {
                if ( trim($i) == $etag ) {
                    header( 'HTTP/1.0 304 Not Modified' );
                    die();
                }
            }

            // Output the XML
            header( 'Content-type: text/xml' );
            echo( $xml );

        }

        /**
         *	This function will save the XML data to the specified file.
         *
         *	@remark
         *		The default format is "RSS2.0". If you specify no argument indicating the requested format, the "RSS2.0"
         *	format will be used.
         *
         *	@param $path	The path to save the XML data to.
         *	@param $format	(optional) The format in which the items should be converted.
         */
        function saveXml( $path, $format='RSS2.0' ) {

            // Get the XML data
            $xml = $this->toXml( $format );

            // Get the directory information
            $dir = new YDFSDirectory( YDPath::getDirectoryName( $path ) );

            // Create the file
            $dir->createFile( YDPath::getFileName( $path ), $xml );

        }

    }

?>
