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
     *  @addtogroup YDFeedCreator Addons - FeedCreator
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDUrl.php' );
    include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php' );
    include_once( YD_DIR_HOME_CLS . '/YDXml.php' );

    /**
     *	This class defines a RSS/ATOM feed. You can use this class to create RSS and Atom feeds in a very easy and
     *	straightforward way. If you set up your class instance, you can automatically output to the different versions
     *	of RSS and ATOM with the same source data.
     *
     *  @ingroup YDFeedCreator
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
            $this->_copyright = '(c) 2002-2007 Pieter Claerhout, pieter@yellowduck.be';
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
            $this->_encodeStrings = true;
        }

        /**
         *	Function to set the encoding of the feed.
         *
         *	@param $encoding	The encoding of the feed.
         */
        function setEncoding( $encoding, $encodeStrings = true ) {
            $this->_encoding = strtoupper( $encoding );
            $this->_encodeStrings = $encodeStrings;
        }

        /**
         *	Function to set the title of the feed.
         *
         *	@param $title	The title of the feed.
         */
        function setTitle( $title ) {
            $this->_title = $this->_encodeStrings ? YDStringUtil::encodeString( $title ) : $title;
        }

        /**
         *	Function to set the description of the feed.
         *
         *	@param $desc	The description of the feed.
         */
        function setDescription( $desc ) {
            $desc = YDUrl::makeLinksAbsolute( $desc, $this->_link );
            $this->_description = $this->_encodeStrings ? YDStringUtil::encodeString( $desc ) : $desc;
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
         *  Function to set the generator of the feed
         *
         *  @param $generator   The name of the application that generates the feed.
         */
        function setGenerator( $generator ) {
            $this->_generator = $generator;
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
         *  @param $commentlink     (optional) The link to the comment page for the item.
         *
         *  @remark
         *      Enclosures are only supported for ATOM and RSS 2.0 feeds.
         */
        function addItem( $title, $link, $desc=null, $guid=null, $enclosure=null, $enclosure_size=null, $enclosure_type=null, $commentlink=null ) {

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
                'title' => $this->_encodeStrings ? YDStringUtil::encodeString( $title ) : $title,
                'link' => $link,
                'description' => $this->_encodeStrings ? YDStringUtil::encodeString( $desc ) : $desc,
                'guid' => $guid,
                'enclosure' => $enclosure,
                'enclosure_size' => $enclosure_size,
                'enclosure_type' => $enclosure_type,
                'comments' => $commentlink
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

            $xml = new YDXml();
            $xml->encoding = $this->_encoding;
            
            // Formatter for RSS 0.91
            if ( $format == 'RSS0.91' || $format == 'RSS2.0' ) {
                
                $feed['rss'][0]['#'] = array();
                
                if ( $format == 'RSS0.91' ) {
                    $feed['rss'][0]['@']['version'] = '0.91';
                } else {
                    $feed['rss'][0]['@']['version'] = '2.0';
                }
                
                $feed['rss'][0]['#']['channel'][0]['#'] = array();
                
                $channel = & $feed['rss'][0]['#']['channel'][0]['#'];
                
                $channel['title'][0]['#'] = $this->_title;
                
                if ( ! empty( $this->_description ) ) {
                    $channel['description'][0]['#'] = $this->_description;
                }
                
                $channel['link'][0]['#'] = $this->_link;
                $channel['generator'][0]['#'] = $this->_generator;
                
                $i=0;
                foreach ( $this->_items as $arr ) {
                    
                    $channel['item'][$i]['#'] = array();
                    
                    $item = & $channel['item'][$i]['#'];
                    
                    $item['title'][0]['#'] = $arr['title'];
                    $item['link'][0]['#'] = $arr['link'];
                    $item['guid'][0]['#'] = $arr['guid'];
                    $item['guid'][0]['@']['isPermanlink'] = 'false';

                    if ( ! is_null( $arr['comments'] ) ) {
                        $item['comments'] = $arr['comments'];
                    }

                    if ( ! empty( $arr['description'] ) ) {
                        $item['description'] = $arr['description'];
                    }
                    
                    if ( $format == 'RSS2.0' && ! is_null( $arr['enclosure'] ) ) {
                        
                        $item['enclosure'][0]['@']['url']    = $arr['enclosure'];
                        $item['enclosure'][0]['@']['length'] = $arr['enclosure_size'];
                        $item['enclosure'][0]['@']['type']   = $arr['enclosure_type'];
                        
                    }
                    $i++;

                }
                
            }

            // Formatter for RSS1.0
            if ( $format == 'RSS1.0' ) {
                
                $feed['rdf:RDF'][0]['#'] = array();
                $feed['rdf:RDF'][0]['@']['xmlns']     = "http://purl.org/rss/1.0/";
                $feed['rdf:RDF'][0]['@']['xmlns:rdf'] = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
                $feed['rdf:RDF'][0]['@']['xmlns:dc']  = "http://purl.org/dc/elements/1.1/";
                
                $feed['rdf:RDF'][0]['#']['channel'][0]['#'] = array();
                $feed['rdf:RDF'][0]['#']['channel'][0]['@']['rdf:about'] = '';
                $channel = & $feed['rdf:RDF'][0]['#']['channel'][0]['#'];
                
                $channel['title'][0]['#'] = $this->_title;
                $channel['description'][0]['#'] = $this->_description;
                $channel['link'][0]['#'] = $this->_link;
                $channel['items'][0]['#']['rdf:Seq'][0]['#'] = array();
                
                $i = 0;
                foreach ( $this->_items as $item ) {
                    $li = & $channel['items'][0]['#']['rdf:Seq'][0]['#']['rdf:li'][$i];
                    $li['@']['rdf:resource'] = $item['link'];
                    $i++;
                }
                
                $i = 0;
                foreach ( $this->_items as $arr ) {
                
                    $rss['rdf:RDF'][0]['#']['item'][$i]['@']['rdf:about'] = $arr['link'];
                    
                    $item = & $feed['rdf:RDF'][0]['#']['item'][$i]['#'];
                    $item['dc:format'][0]['#'] = 'text/html';
                    $item['title'][0]['#'] = $arr['title'];
                    $item['link'][0]['#']  = $arr['link'];
                    if ( ! empty( $arr['description'] ) ) {
                        $item['description'][0]['#'] = $arr['description'];
                    }
                    $i++;
                    
                }
                
            }

            // Formatter for ATOM
            if ( $format == 'ATOM' ) {
                
                $feed['feed'][0]['#'] = array();
                $feed['feed'][0]['@']['version'] = "0.3";
                $feed['feed'][0]['@']['xmlns']   = "http://purl.org/atom/ns#";
                
                $feed['feed'][0]['#']['title'][0]['#'] = $this->_title;
                if ( ! empty( $this->_description ) ) {
                    $feed['feed'][0]['#']['tagline'][0]['#'] = $this->_description;
                }
                
                $feed['feed'][0]['#']['link'][0]['@']['rel'] = 'alternate';
                $feed['feed'][0]['#']['link'][0]['@']['type'] = 'text/html';
                $feed['feed'][0]['#']['link'][0]['@']['href'] = $this->_link;
                
                $feed['feed'][0]['#']['id'][0]['#'] = $this->_link;
                $feed['feed'][0]['#']['generator'][0]['#'] = $this->_generator;
                
                $i = 0;
                foreach ( $this->_items as $arr ) {
                    
                    $item = &  $feed['feed'][0]['#']['entry'][$i]['#'];
                    $item['title'][0]['#'] = $arr['title'];
                    $item['link'][0]['@']['rel'] = 'alternate';
                    $item['link'][0]['@']['type'] = 'text/html';
                    $item['link'][0]['@']['href'] = $arr['link'];
                    $item['id'][0]['#'] = $arr['guid'];
                    
                    if ( ! empty( $arr['description'] ) ) {
                        $item['content'][0]['@']['type'] = 'text/html';
                        $item['content'][0]['@']['mode'] = 'escaped';
                        $item['content'][0]['@']['xml:base'] = $arr['link'];
                        $item['content'][0]['#'] = '<![CDATA[' . $arr['description'] . ']]>';
                    }
                    
                    if ( ! is_null( $arr['enclosure'] ) ) {
                        
                        $item['link'][0]['@']['rel'] = 'enclosure';
                        $item['link'][0]['@']['href'] = $arr['enclosure'];
                        $item['link'][0]['@']['length'] = $arr['enclosure_size'];
                        $item['link'][0]['@']['type'] = $arr['enclosure_type'];
                        
                    }
                    $i++;
                }
                
            }

            $xml->loadArray( $feed );
            $xml->encoding = $this->_encoding;
            return $xml->toString();

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
            header( 'Content-type: text/xml; charset=utf-8' );
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