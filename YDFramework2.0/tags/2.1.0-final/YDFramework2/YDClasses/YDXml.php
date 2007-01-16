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
    @include_once( YD_DIR_HOME . '/3rdparty/phpdomxml/lib.xml.inc.php' );
    
    // YDXml constants
    define( 'YD_XML_FILE',   1 );
    define( 'YD_XML_URL',    2 );
    define( 'YD_XML_STRING', 3 );
    define( 'YD_XML_ARRAY',  4 );
    
    /**
     *  This class is a wrapper around phpdomxml. Documentation can be found on:
     *  http://phpdomxml.webtweakers.com/
     */
    class YDXml extends _XML {

        var $version  = '1.0';
        var $encoding = 'ISO-8859-1';

        /**
         *  This is the class constructor of YDXml.
         *
         *  @param $mixed   (Optional) A string, file, url or array for loading when
         *                  instantiating the object. Default: empty.
         *  @param $type    (Optional) The type of the $mixed input. Must be one of the following:
         *                  YD_XML_FILE, YD_XML_URL, YD_XML_STRING or YD_XML_ARRAY.
         */
        function YDXml( $mixed='', $type=YD_XML_FILE ) {
            
            $this->_XML();
            
            if ( ! empty( $mixed ) ) {
                $this->load( $mixed, $type );
            } 
            
        }
        
        /**
         *  This function loads the contents of a file to the object.
         *
         *  @param $path   The path to the file.
         */
        function loadFile( $path ) {
            $this->load( $path, YD_XML_FILE );
        }
        
        /**
         *  This function loads a string of XML code to the object.
         *
         *  @param $string   The XML string.
         */
        function loadString( $string ) {
            $this->load( $string, YD_XML_STRING );
        }
        
        /**
         *  This function loads the contents of an url to the object.
         *
         *  @param $url   The URL.
         */
        function loadUrl( $url ) {
            $this->load( $url, YD_XML_URL );
        }
        
        /**
         *  This function loads any type of content based on the YD_XML constants.
         *
         *  @param $mixed   A string, file, url or array for loading when instantiating
         *                  the object. Default: empty.
         *  @param $type    The type of the $mixed input. Must be one of the following:
         *                  YD_XML_FILE, YD_XML_URL, YD_XML_STRING or YD_XML_ARRAY.
         */
        function load( $mixed, $type=YD_XML_FILE ) {
            
            if ( empty( $mixed ) ) {
                trigger_error( 'No file, string, array or url specified', YD_ERROR );
            }
            
            switch ( $type ) {
                
                case YD_XML_URL:
                
                    include_once( YD_DIR_HOME_CLS . '/YDUrl.php' );
                    
                    $url = new YDUrl( $mixed );
                    $contents = $url->getContents( false );
                    break;
            
                case YD_XML_FILE:
                
                    include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php' );
                    
                    $file = new YDFSFile( $mixed );
                    $contents = $file->getContents();
                    break;
                
                case YD_XML_STRING:
                
                    $contents = $mixed;
                    break;
                
                case YD_XML_ARRAY:
                
                    $this->loadArray( $mixed );
                    return;
                
                default:
                    trigger_error( 'Type of input unknown.', YD_ERROR );
            }
            
            $this->reset();
            $this->parseXML( $contents );
            
        }
        
        /**
         *  This function parses the XML data and retrieves the version and encoding.
         *
         *  @param $data   The XML data.
         */
        function parseXML( $data ) {
            
            // Strip white space
            $data = preg_replace("/>\s+</i", "><", $data);

            $parser = new XML_Parser( $this );
            $parser->parse( $data );
            
            // Get version and encoding
            if ( ! is_null( $parser->version ) ) {
                $this->version = $parser->version;
            }
            if ( ! is_null( $parser->encoding ) ) {
                $this->encoding = $parser->encoding;
            }
            
        }
        
        /**
         *  This function resets the object.
         */
        function reset() {
            
            $xml = new YDXml();
            foreach ( get_object_vars( $xml ) as $key => $value ) {
                $this->$key = $value;
            }
            
        }
        
        /**
         *  This function loads an array to the object. The array have a defined
         *  structure that must be used.
         *
         *  @param $array  The array.
         *  @param $level  (Optional) The level for parsing.
         *  @param @prefix (Optional) The node's prefixes.
         */
        function loadArray( $array, $level=0, $prefix='' ) {
            
            if ( $prefix == '' ) {
                $this->reset();
            }
            
            $node = & $this;
            for ( $i = 1; $i<=$level; $i++ ) {
                $node = & $node->lastChild;
            }
            
            if ( is_array( $array ) ) {
            
                foreach ( $array as $key => $child ) {
                    
                    if ( is_array( $child ) ) {
                        
                        foreach ( $child as $k => $info ) {
                        
                            $append = true;
                            $root = $prefix.$key.$k;
                            $$root = $this->createElement( $key );
                            
                            // attributes
                            if ( isset( $info['@'] ) ) {
                                foreach ( $info['@'] as $att => $val ) {
                                    $$root->setAttribute( $att, $val );
                                }
                            }
                            
                            // children or value
                            if ( isset( $info['#'] ) ) {
                                if ( is_array( $info['#'] ) ) {
                                    $node->appendChild( $$root );
                                    $this->loadArray( $info['#'], ( $level+1 ), $root );
                                    $append = false;
                                } else {
                                    if ( preg_match( "/<!\[CDATA\[(.*)\]\]>/s", $info['#'], $matches ) ) {
                                        $$root->appendChild(
                                            $this->createCDATASection( $matches[1] )
                                        );
                                        
                                    } else {
                                        $$root->appendChild(
                                            $this->createTextNode( $info['#'] )
                                        );
                                    }
                                }
                            }
                            
                            if ( $append ) {
                                $node->appendChild( $$root );
                            }
                            
                        }
                        
                    } else {
                        
                        $root = $prefix.$key.'0';
                        $$root = $this->createElement( $key );
                        
                        if ( preg_match( "<!\[CDATA\[(.*)\]\]>", $child, $matches ) ) {
                            $$root->appendChild(
                                $this->createCDATASection( $matches[1] )
                            );
                            
                        } else {
                            $$root->appendChild(
                                $this->createTextNode( $child )
                            );
                        }
                        
                        $node->appendChild( $$root );
                        
                    }
                    
                }
                
            }
            
        }
        
        /**
         *  This function saves the XML to a file.
         *
         *  @param $path    The path to the file. If it doesn't exist, the function will try
         *                  to create it. The file will be emptied before adding the contents.
         *  @param $pretty  (Optional) Saves the "pretty" version of the XML - with indentation.
         */
        function save( $path, $pretty=true ) {
            
            include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php' );
            
            $file = new YDFSFile( $path, true );
            $file->setContents( $this->toString( $pretty ) );
            
        }
        
        /**
         *  This function returns the XML as a string.
         *
         *  @param $pretty  (Optional) Returns the "pretty" version of the XML - with indentation.
         *
         *  @returns  The XML code as a string.
         */
        function toString( $pretty=true ) {
            return "<?xml version=\"" . $this->version . "\" encoding=\"" . $this->encoding . "\"?>" . ( $pretty ? "\n" : "" ) . trim( parent::toString( $pretty ) );
        }
        
        /**
         *  This function returns the XML as an array.
         *
         *  @param $node  (Optional) The starting node for parsing.
         *
         *  @returns  An array with a custom format. The character "@" defines attributes
         *            and the character "#" defines children or values.
         */
        function toArray( $node=null ) {
            
            if ( is_null( $node ) ) {
                $node = & $this;
            }
            
            $last = '';
            $arr = array();
            
            foreach ( $node->childNodes as $child ) {
                
                if ( $child->nodeType == XML_COMMENT_NODE ) {
                    continue;
                }
                
                if ( $child->nodeName != $last ) {
                    $i = 0;
                }
                
                if ( $child->nodeType != XML_ELEMENT_NODE ) {
                    $arr = $child->nodeValue;
                } else {
                    
                    if ( $child->hasAttributes() ) {
                        $arr[ $child->nodeName ][ $i ]['@'] = $child->attributes;
                    }
                    
                    $arr[ $child->nodeName ][ $i ]['#'] = '';

                    if ( $child->hasChildNodes() ) {
                        $arr[ $child->nodeName ][ $i ]['#'] = $this->toArray( $child );
                    } else {
                        if ( isset( $child->nodeValue ) ) {
                            $arr[ $child->nodeName ][ $i ]['#'] = $child->nodeValue;
                        }
                    }
                    
                }
                
                $last = $child->nodeName;
                $i++;
            }
            
            return $arr;
            
        }
        
        /**
         *  This function returns the XML as a string representation of the array
         *  returned by toArray.
         *
         *  @param $array  (Optional) The array to traverse. If null, the result from
         *                 toArray of the current object. Default: null.
         *  @param $name   (Optional) The name of the array. Default: array.
         *  @param $level  (Optional) The level of parsing the array.
         *
         *  @returns  A string representation of the array returned by toArray.
         *
         *  @static  If $array is passed.
         */
        function traverse( $array=null, $name='array', $level=0 ) {
            
            if ( is_null( $array ) ) {
                $array = $this->toArray();
            }
            
            $traverse = array();
            
            if ( is_array( $array ) ) {
                
                foreach( $array as $key => $val ) {
                    if ( is_array( $val ) ) {
                        $traverse = array_merge( $traverse, YDXml::traverse( $val, $name . "[" . $key . "]", $level + 1 ) );
                    } else {
                        $traverse[] = '$' . $name . '[' . $key . '] = "' . str_replace( "\n", "", trim( $val ) ) . "\"\n";
                    }
                }
                
            }
            return implode( '', $traverse );
            
        }
    }

?>
