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
    include_once( YD_DIR_HOME_CLS . '/YDUtil.php' );

    // Constants
    define( 'YD_DB_FETCH_ASSOC', 1 );
    define( 'YD_DB_FETCH_NUM', 2 );
    define( 'YD_BROWSEBAR_FULL', 3 );
    define( 'YD_BROWSEBAR_SHORT', 4 );

    // Configure the default for this class
    YDConfig::set( 'YD_DB_FETCHTYPE', YD_DB_FETCH_ASSOC, false );
    YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 20, false );
    YDConfig::set( 'YD_DB_TABLEPREFIX', '', false );
    YDConfig::set( 'YD_DB_ALLOW_PERSISTENT_SORT', false, false );

    YDConfig::set( 'YD_DB_RS_CYCLENAVIGATION', false, false );

    /**
     *  This class implements a (paged) recordset. It contains a lot of extra information about the recordset which is
     *  not available if you return the database results as an array. This object is really handy if you want to work
     *  with paged recordsets.
     *
     *  Here's the extra information that is available:
     *
     *  - page: current page number
     *  - pagesize: total size of each page
     *  - pagePrevious: the number of the previous page
     *  - pageNext: the number of the next page
     *  - offset: the first record we started reading from
     *  - totalPages: the total number of pages
     *  - totalRows: the total number of rows in the unpaged recordset
     *  - totalRowsOnPages: the total number of rows on the current page
     *  - isFirstPage: boolean indicating if we are on the first page or not
     *  - isLastPage: boolean indicating if we are on the last page or not
     *  - pages: all the page numbers as a single-dimension array
     *  - getPreviousUrl: the URL to the previous page
     *  - getCurrentUrl: the URL of the current page
     *  - getNextUrl: the URL of the next page
     *  - getPageUrl: the URL of the given page
     *
     *  All these options are available as class variables.
     *
     *  @todo
     *      Improve performance with very large recordsets (millions of rows).
     */
    class YDRecordSet extends YDBase {

        /**
         * This is the class constructor for the YDRecordSet class.
         *
         *  @param  $records    The list of records as an array (as returned by the YDDatabaseDriver::getRecords
         *                      function.
         *  @param  $page       (optional) The page you want to retrieve. If omitted all records will be returned.
         *  @param  $pagesize   (optional) The maximum number of rows for each page. If a page number is given, the
         *                      default will be to return a maximum of 20 rows. If no page number is given, the pagesize
         *                      will be the same as the total number of rows in the recordset.
         *  @param  $pagevar    (optional) The name of the query string variable indicating the page. Defaults to "page"
         *  @param  $sizevar    (optional) The name of the query string variable indicating the page size. Defaults to
         *                      "size"
         *  @param  $sortvar    (optional) The name of the query string variable indicating the sort feild. Defaults to 
         *                      "sortfld"
         *  @param  $sortdir    (optional) The name of the query string variable indicating the direction. Defaults to
         *                      "sortdir"
         */
        function YDRecordSet( $records, $page=-1, $pagesize=null, $pagevar='page', $sizevar='size', $sortvar='sortfld', $sortdir='sortdir' ) {

            // Include the YDPersistent library
            include_once( YD_DIR_HOME_CLS . '/YDPersistent.php');

            // Define the query string variables
            $this->pagevar = $pagevar;
            $this->sizevar = $sizevar; 
            $this->sortvar = $sortvar;
            $this->sortdir = $sortdir;

            // The sort field and direction
            $this->sortfield = null;
            $this->sortdirection = null;

            // The complete list of all records
            $this->records = $records;

            // Check if persistent sort is allowed or not
            if ( YDConfig::get( 'YD_DB_ALLOW_PERSISTENT_SORT' ) ) {

                // Sort the records persistent
                if ( YDPersistent::get( $sortvar, '' ) != '' ) {

                    // Set the sortfield and direction
                    $this->sortfield = YDPersistent::get( $sortvar );

                    // Get the sort direction
                    if ( strtoupper( YDPersistent::get( $this->sortdir, 'DESC' ) ) == 'DESC' ) {
                        $this->sortdirection = 'DESC';
                    } else {
                        $this->sortdirection = 'ASC';
                    }

                    // Perform the sorting
                    $this->records = $this->sort( $this->sortfield, $this->sortdirection );

                }

            } else {

                // Sort the records
                if ( ! empty( $_GET[$sortvar] ) ) {

                    // Set the sortfield and direction
                    $this->sortfield = $_GET[$sortvar];

                    // Get the sort direction
                    if ( isset( $_GET[$this->sortdir] ) && ( $_GET[$this->sortdir] == 'DESC' ) ) {
                        $this->sortdirection = 'DESC';
                    } else {
                        $this->sortdirection = 'ASC';
                    }

                    // Perform the sorting
                    $this->records = $this->sort( $this->sortfield, $this->sortdirection );

                }

            }

            // Convert the page and pagesize to integers
            $page = ( is_numeric( $page ) ) ? intval( $page ) : -1;
            $pagesize = ( is_numeric( $pagesize ) ) ? intval( $pagesize ) : YDConfig::get( 'YD_DB_DEFAULTPAGESIZE' );

            // This original recordset
            $this->page = ( $page >= 1 ) ? $page : 1;
            $page = ( $page >= 1 ) ? $page : -1;
            if ( $page == -1 ) {
                $this->pagesize = ( $pagesize >= 1 ) ? $pagesize : sizeof( $this->records );
            } else {
                $this->pagesize = ( $pagesize >= 1 ) ? $pagesize : YDConfig::get( 'YD_DB_DEFAULTPAGESIZE' );
            }
            
            // Keep the original page value
            $this->pageorig = $this->page;
            
            // Set the total of pages and rows
            $this->setTotalRows( sizeof( $this->records ), true );
            
            // Publish the URL as an object
            include_once( YD_DIR_HOME_CLS . '/YDUrl.php' );
            $this->url = new YDUrl( YD_SELF_URI );

        }
        
        /**
         *  This function replaces the total number of rows with a defined value.
         *
         *  @param  $value    The total number of rows.
         *  @param  $offset   (optional) If true, the current set will be a slice of
         *                    the records based on the page size and the current page. Otherwise,
         *                    the offset value will be zero and the "pagesize" number of rows will be used.
         *                    Default: false.
         */
        function setTotalRows( $value, $offset=false ) {

            // Set the number of records an integer
            $value = intval( $value );

            // Get the number of pages
            if ( $this->pagesize == 0 ) {
                $this->totalPages = 1;
            } else {
                $this->totalPages = ceil( $value / ( float ) $this->pagesize );
            }
            $this->totalRows  = $value;

            // Get the original page number
            $this->page = $this->pageorig;
            
            // Set the number of pages correctly to zero for an empty recordset
            if ( $this->totalPages == 0 ) {
                $this->page = 0;
            }
            
            // Fix the page number if bigger than the amount of pages
            if ( $this->page > $this->totalPages ) {
                $this->page = $this->totalPages;
            }
            
            // Get the offset
            $this->offset = $offset ? ( $this->pagesize * ( $this->page - 1 ) ) : 0;

            // Get the previous and next page
            $this->pagePrevious = ( $this->page <= 1 ) ? ( YDConfig::get( 'YD_DB_RS_CYCLENAVIGATION' ) ? $this->totalPages : false ) : $this->page - 1;
            $this->pageNext = ( $this->page >= $this->totalPages ) ? ( YDConfig::get( 'YD_DB_RS_CYCLENAVIGATION' ) ? 1 : false ) : $this->page + 1;

            // Indicate if we are on the last or first page
            $this->isFirstPage = ( $this->pagePrevious === false ) ? true : false;
            $this->isLastPage = ( $this->pageNext === false ) ? true : false;

            // Add the list of pages as an array
            $this->pages = ( $this->totalPages <= 1 ) ? array( 1 ) : range( 1, $this->totalPages );

            // Get subset of the pages a la phpBB (ideal for large page lists
            $this->pagesShort = $this->pages;
            if ( sizeof( $this->pages ) > 10 ) {

                // Get the first 3 pages
                $this->pagesShort = range( 1, 3 );

                // Get the current page, the previous one and the next one
                if ( $this->pagePrevious !== false && ! in_array( $this->pagePrevious, $this->pagesShort ) ) {
                    array_push( $this->pagesShort, $this->pagePrevious );
                }
                if ( ! in_array( $this->page, $this->pagesShort ) ) {
                    array_push( $this->pagesShort, $this->page );
                }
                if ( $this->pageNext !== false && ! in_array( $this->pageNext, $this->pagesShort ) ) {
                    array_push( $this->pagesShort, $this->pageNext );
                }

                // Get the last 3 pages
                $this->pagesShort = array_merge( $this->pagesShort, range( $this->totalPages-2, $this->totalPages ) );

                // Get the unique values
                $this->pagesShort = array_unique( $this->pagesShort );

                // Sort the list
                sort( $this->pagesShort );

                // Add the holes
                $pagesShort = $this->pagesShort;
                $counter = 1;
                foreach ( $this->pagesShort as $key=>$page ) {
                    $next = next( $this->pagesShort );
                    if ( $next !== false && ( $page + 1 ) != $next ) {
                        array_splice(
                            $pagesShort,
                            $key+$counter,
                            count( $pagesShort ),
                            array_merge( array( false ), array_slice( $pagesShort, $key+$counter ) )
                        );
                        $counter++;
                    }
                }
                $this->pagesShort = $pagesShort;

            }

            // Get the subset of the records we need
            $this->set = array_slice( $this->records, $this->offset, $this->pagesize );

            // Get the total number of rows on a page
            $this->totalRowsOnPage = sizeof( $this->set );
            
        }

        /**
         *  This function will sort the data given the name of the field to sort on and the sort direction.
         *
         *  @param  $sortfld    The field to sort on.
         *  @param  $sortdir    (optional) The sort direction. The default is ASC.
         */
        function sort( $sortfld, $sortdir='ASC' ) {

            // Set the new class variables
            $this->sortfield = $sortfld;
            $this->sortdirection = strtoupper( $sortdir ) == 'ASC' ? 'ASC' : 'DESC';

            // Get the list of array indexes with the right column
            $sort_array = array();
            foreach ( $this->records as $key=>$record ) {
                if ( ! isset( $record[$this->sortfield] ) ) {
                    $record[$this->sortfield] = '';
                }
                $sort_array[ $key ] = $record[$this->sortfield];
            }

            // Do the sorting
            if ( strtoupper( $this->sortdirection ) == 'DESC' ) {
                arsort( $sort_array );
            } else {
                asort( $sort_array );
            }

            // Reconstruct the records array
            $records_new = array();
            foreach ( $sort_array as $key=>$val ) {
                array_push( $records_new, $this->records[$key] );
            }

            // Return the new recordset
            return $records_new;

        }

        /**
         *  This function returns a reference to the URL for this recordset object. If you want to alter this url, you
         *  should get a instance of it as a reference. This code shows you how to do this:
         *
         *  @code
         *  $url = & $dataset->getUrl();
         *  @endcode
         *
         *  @returns    Reference to the YDUrl object for this YDRecordSet object.
         */
        function & getUrl() {
            return $this->url;
        }

        /**
         *  This returns the URL to the previous page. If there is no previous page, it will return false.
         *
         *  @returns    The URL to the previous page or false if no previous page.
         */
        function getPreviousUrl() {

            // Return false if no previous page
            if ( $this->isFirstPage ) {

                // Check if the set is empty or not
                if ( sizeof( $this->set ) > 0 ) {

                    // Return zero for empty set
                    return $this->getPageUrl( 0 );

                } else {

                    // Return one for non-empty set
                    return $this->getPageUrl( 1 );

                }

            }

            // Return the updated URL
            return $this->getPageUrl( $this->pagePrevious );

        }

        /**
         *  This returns the URL to the current page.
         *
         *  @returns    The URL to the current page.
         */
        function getCurrentUrl() {

            // Return the updated URL
            return $this->getPageUrl( $this->page );

        }

        /**
         *  This returns the URL to the next page. If there is no next page, it will return false.
         *
         *  @returns    The URL to the next page or false if no next page.
         */
        function getNextUrl() {

            // Return false if no next page
            if ( $this->isLastPage ) {

                // Return the updated URL
                return $this->getPageUrl( $this->totalPages );

            }

            // Return the updated URL
            return $this->getPageUrl( $this->pageNext );

        }

        /**
         *  This function will update the query string to set the page size and page number.
         *
         *  @param  $page       The page number.
         *  
         *  @returns    The updated URL.
         */
        function getPageUrl( $page ) {

            // Doublecheck the pagenumber
            $page = ( is_numeric( $page ) ) ? intval( $page ) : -1;
            if ( ! in_array( $page, $this->pages ) ) {
                $page = 1;
            }

            // Get the URL
            $url = $this->url;

            if ( $this->totalPages > 1 ) {
                $url->setQueryVar( $this->pagevar, $page );
                $url->setQueryVar( $this->sizevar, $this->pagesize );
            }

            // Return the url
            return $url->getUri();

        }

        /**
         *  This function will return an URL to sort on a field.
         *
         *  @param  $sortfld    The field to sort on.
         *  @param  $sortdir    (optional) The sort direction. Default is ASC.
         */
        function getSortUrl( $sortfld, $sortdir='ASC' ) {

            // Get the current sort direction
            $sortdir = ( strtoupper( $sortdir ) == 'ASC' ) ? 'ASC' : 'DESC';

            // Get the URL
            $url = $this->url;

            if ( $this->totalPages > 1 ) {
                $url->setQueryVar( $this->pagevar, $this->page );
                $url->setQueryVar( $this->sizevar, $this->pagesize );
            }

            // Set the sort field name
            $url->setQueryVar( $this->sortvar, $sortfld );

            // Get the sort direction
            if ( isset( $_GET[$this->sortvar] ) && $_GET[$this->sortvar] == $sortfld ) {
                if ( isset( $_GET[$this->sortdir] ) && ( $_GET[$this->sortdir] == 'ASC' ) ) {
                    $sortdir = 'DESC';
                } else {
                    $sortdir = 'ASC';
                }
                $url->setQueryVar( $this->sortdir, $sortdir );
            } else {
                $url->setQueryVar( $this->sortdir, $sortdir );
            }

            // Return the url
            return $url->getUri();

        }

        /**
         *  This function returns the url of the last page in the set.
         *
         *  @returns    The url of the last page in the set.
         */
        function getLastUrl(){
            return $this->getPageUrl( $this->totalPages );
        }

        /**
         *  This function returns the url of the first page in the set.
         *
         *  @returns    The url of the last first in the set.
         */
        function getFirstUrl(){
            return $this->getPageUrl(1);
        }

        /**
         *  Converts the YDDatabase set to an array containing the records of the recordset. The meta information about
         *  the dataset is not kept.
         */
        function toArray() {
            return $this->set;
        }

        /**
         *  Sets fields names
         *
         *  @param $fields          Field name of sort
         */
        function setFields( $fields ) {
            return $this->gr_fields = $fields;
        }

        /**
         *  Gets fields names
         */
        function getFields() {
            return ( isset( $this->gr_fields ) ? $this->gr_fields : array() );
        }

        /**
         *  Set current field name
         *
         *  @param $field          Field name of sort
         *  @param $sortdirection  Sort direction
         */
        function setCurrentField( $field, $sortdirection ) {
            $this->gr_currentField = $field;
            $this->sortdirection   = $sortdirection;
        }

        /**
         *  Get current field name
         */
        function getCurrentField() {
            return ( isset( $this->gr_currentField ) ? $this->gr_currentField : array() );
        }

        /**
         *  Get sort direction
         *
         *  @param $reverse    (optional) Boolean if we want a reverse direction
         *
         */
        function getSortdirection( $reverse = false ) {
            if ( $reverse ) {
                return ( strtoupper( $this->sortdirection ) == 'ASC' ? 'DESC' : 'ASC' );
            } else {
                return $this->sortdirection;
            }
        }

        /**
         *  This function gives a prerendered browsing bar for the recordset.
         *
         *  @param  $previousLabel  (optional) Label for the previous link. Defaults to "&laquo;".
         *  @param  $nextLabel      (optional) Label for the next link. Defaults to "&raquo;".
         *  @param  $style          (optional) Style of the browse bar. This can be either YD_BROWSEBAR_FULL or
         *                          YD_BROWSEBAR_FULL. The full one shows all page numbers, the short one doesn't.
         *  @param  $currentLink    (optional) Boolean indicating if you should be able to click on the link for the
         *                          current page or not.
         *
         *  @returns    The browsebar as formatted HTML.
         */
        function getBrowseBar(
            $previousLabel='&laquo;', $nextLabel='&raquo;', $style=YD_BROWSEBAR_SHORT, $currentLink=false, $allLink=null
        ) {

            // Capture the HTML output
            $html = '';

            // If no pages, return an empty string
            if ( $this->totalPages <= 1 ) {
                return $html;
            }

            // Get the correct currentLink value
            $currentLink = ( $currentLink === false ) ? false : true;

            // Get the list of pages we need to use
            $pages = ( $style == YD_BROWSEBAR_SHORT ) ? $this->pagesShort : $this->pages;

            // Get the previous link if any
            if ( $this->page != 1 ) {
                $html .= sprintf( '<a href="%s">%s</a> ', $this->getPreviousUrl(), $previousLabel );
            } else {
                $html .= sprintf( '%s ', $previousLabel );
            }

            // Loop over the pages
            foreach ( $pages as $page ) {
                if ( $page !== false ) {
                    if ( $page == $this->page ) {
                        if ( $currentLink ) {
                            $html .= sprintf( '<a href="%s"><b>%s</b></a> ', $this->getPageUrl( $page ), $page );
                        } else {
                            $html .= sprintf( '<b>%s</b> ', $page );
                        }
                    } else {
                        $html .= sprintf( '<a href="%s">%s</a> ', $this->getPageUrl( $page ), $page );
                    }
                } else {
                    $html .= '... ';
                }
            }

            // Add an all link if need
            if ( ! empty( $allLink ) ) {
                $url = $this->url;
                $url->deleteQueryVar( $this->pagevar );
                $url->deleteQueryVar( $this->sizevar );
                $url->setQueryVar( 'show', 'all' );
                $html .= sprintf( '<a href="%s">%s</a> ', $url->getUri(), $allLink );
            }

            // Get the next link if any
            if ( $this->page != $this->totalPages ) {
                $html .= sprintf( '<a href="%s">%s</a> ', $this->getNextUrl(), $nextLabel );
            } else {
                $html .= sprintf( '%s ', $nextLabel );
            }

            // Return the HTML
            return $html;

        }

    }


    /**
     *  This class defines a database object.
     */
    class YDDatabase extends YDBase {

        /**
         *  This function registers a named database instance.
         *
         *  @param $name    The name of the database instance.
         *  @param $driver  Name of the database driver or array containing drivername, file name and class name.
         *  @param $db      Database name to use for the connection.
         *  @param $user    (optional) User name to use for the connection.
         *  @param $pass    (optional) Password to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *
         *  @static
         */
        function registerInstance( $name, $driver, $db, $user='', $pass='', $host='' ) {

            // Check if the global array exists
            YDDatabase::_initNamedInstances();

            // Register the instance
            $GLOBALS['YD_DB_INSTANCES'][ strtolower( $name ) ] = array( $driver, $db, $user, $pass, $host );

        }

        /**
         *  This function will return a YDDatabaseDriver instance for the named database instance. If no instance is
         *  given, the one with the name "default" will be used. You can use database instance objects too.
         *
         *  @param $name    (optional) The name or object of the database instance. Defaults to "default".
         *
         *  @returns    An instance of YDDatabaseDriver
         *
         *  @static
         */
        function getNamedInstance( $name='default' ) {

            // Check if name is a YDDatabaseDriver object
            if ( is_object( $name ) ) return $name;

            // Check if the global array exists
            YDDatabase::_initNamedInstances();

            // Trigger an error if the named instance doesn't exist.
            if ( ! array_key_exists( strtolower( $name ), $GLOBALS['YD_DB_INSTANCES'] ) ) {
                trigger_error( 'The named database instance "' . $name . '" is not defined.', YD_ERROR );
            }

            // Get the parameters
            $params = $GLOBALS['YD_DB_INSTANCES'][ strtolower( $name ) ];

            // Return the right named instance
            return YDDatabase::getInstance( $params[0], $params[1], $params[2], $params[3], $params[4] );

        }

        /**
         *  Function to setup the array for the named instances.
         *
         *  @internal
         *
         *  @static
         */
        function _initNamedInstances() {
            if ( ! isset( $GLOBALS['YD_DB_INSTANCES'] ) ) {
                $GLOBALS['YD_DB_INSTANCES'] = array();
            }
        }

        /**
         *  Using this static function, you can get an instance of a YDDatabaseDriver class.
         *
         *  @param $driver  Name of the database driver or array containing drivername, file name and class name.
         *  @param $db      Database name to use for the connection.
         *  @param $user    (optional) User name to use for the connection.
         *  @param $pass    (optional) Password to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *
         *  @returns    An instance of YDDatabaseDriver
         *
         *  @static
         */
        function getInstance( $driver, $db, $user='', $pass='', $host='' ) {

            // The list of known drivers
            $regDrivers = array();

            // Register new driver
            if( is_array( $driver ) && isset( $driver['drivername'] ) ) {

                // Register a new driver
                $regDrivers[ strtolower( $driver['drivername'] ) ] = $driver;
                $driver = $driver[ 'drivername' ];

            } else {

                // Get the standard driver list
                $regDrivers = YDDatabase::getSupportedDrivers();

                // Check if the driver exists
                if ( ! array_key_exists( strtolower( $driver ), $regDrivers ) ) {
                    trigger_error( 'Unsupported database type: "' . $driver . '".', YD_ERROR );
                }

            }

            // Include the driver
            if ( ! empty( $regDrivers[ strtolower( $driver ) ]['file'] ) ) {
                if ( is_file( $regDrivers[ strtolower( $driver ) ]['file'] ) ) {
                    include_once( $regDrivers[ strtolower( $driver ) ]['file'] );
                } else {
                    include_once( dirname( __FILE__ ) . '/YDDatabaseDrivers/' . $regDrivers[ strtolower( $driver ) ]['file'] );
                }
            }

            // Check if the driver is supported
            if ( ! call_user_func( array( $regDrivers[ strtolower( $driver ) ]['class'], 'isSupported' ) ) ) {
                trigger_error( 'Unsupported database type: "' . $driver . '". Extension is not loaded.', YD_ERROR );
            }

            // Make a new connection object and return it
            $className = $regDrivers[ strtolower( $driver ) ]['class'];
            return new $className( $db, $user, $pass, $host );

        }

        /**
         *  This function returns the list of supported drivers.
         *
         *  @returns    An array with the list of supported drivers.
         */
        function getSupportedDrivers() {

            // The list of known drivers
            $regDrivers = array();

            // Register the standard drivers
            $regDrivers[ strtolower( 'mysql' ) ] = array(
                'class' => 'YDDatabaseDriver_mysql', 'file' => ''
            );
            $regDrivers[ strtolower( 'oracle' ) ] = array(
                'class' => 'YDDatabaseDriver_oracle', 'file' => 'YDDatabaseDriver_oracle.php'
            );
            $regDrivers[ strtolower( 'postgres' ) ] = array(
                'class' => 'YDDatabaseDriver_postgres', 'file' => 'YDDatabaseDriver_postgres.php'
            );
            $regDrivers[ strtolower( 'sqlite' ) ] = array(
                'class' => 'YDDatabaseDriver_sqlite', 'file' => 'YDDatabaseDriver_sqlite.php'
            );

            // Return the list
            return $regDrivers;

        }

    }

    /**
     *  This class defines a database driver.
     */
    class YDDatabaseDriver extends YDBase {

        /**
         *  This is the class constructor for the YDDatabaseDriver class.
         *
         *  @param $db              Database name to use for the connection.
         *  @param $user            (optional) User name to use for the connection.
         *  @param $pass            (optional) Password to use for the connection.
         *  @param $host            (optional) Host name to use for the connection.
         *  @param $options         (optional) Options to pass to the driver.
         *  @param $failOnError     (optional) Trigger fatal error on database error. Default is true.
         */
        function YDDatabaseDriver( $db, $user='', $pass='', $host='', $options=array(), $failOnError=true ) {

            // Initialize YDBase
            $this->YDBase();

            // Initialize the variables
            $this->_db = $db;
            $this->_user = $user;
            $this->_pass = $pass;
            $this->_host = $host;
            $this->_options = $options;
            $this->_driver = 'unknown';

            // Fail on errors or not
            $this->_failOnError = $failOnError;

            // The connection object
            $this->_conn = null;

            // Date and timestamp, and quote styles
            $this->_fmtDate = 'Y-m-d';
            $this->_fmtTimeStamp = 'Y-m-d H:i:s';
            $this->_fmtQuote = "'";

        }

        /**
         *  Functioo to indicate if an SQL error should die the script or not.
         *
         *  @param $val     (optional) True if you want the script to die on an error, false otherwise.
         */
        function setFailOnError( $val=true ) { 
            $this->_failOnError = ( bool ) $val; 
        }

        /**
         *  This funciton returns the database name.
         *
         *  @returns    The database name.
         */
        function getDatabase() {
            return $this->_db;
        }
        
        /**
         *  This funciton returns the user name of the connection.
         *
         *  @returns    The user name.
         */
        function getUser() {
            return $this->_user;
        }
        
        /**
         *  This funciton returns the password of the connection.
         *
         *  @returns    The password.
         */
        function getPassword() {
            return $this->_pass;
        }
        
        /**
         *  This funciton returns the host name of the connection.
         *
         *  @returns    The host name.
         */
        function getHost() {
            return $this->_host;
        }
        
        /**
         *  This funciton returns the options of the connection.
         *
         *  @returns    The array of options
         */
        function getOptions() {
            return $this->_options;
        }

        /**
         *  This funciton returns the driver in use.
         *
         *  @returns    The driver name in use.
         */
        function getDriver() {
            return $this->_driver;
        }

        /**
         *  This function will check if the server supports this database type.
         *
         *  @returns    Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return true;
        }

        /**
         *  This function will return the version of the database server software.
         *
         *  @returns    The version of the database server software.
         */
        function getServerVersion() {
            return 'unknown';
        }

        /**
         *  Function that makes the actual connection.
         *
         *  @returns    Boolean indicating if the connection was succesfull or not.
         */
        function connect() {
        }

        /**
         *  This function will return the number of queries that were executed.
         *
         *  @returns    The number of queries that were executed.
         */
        function getSqlCount() {
            return sizeof( $GLOBALS['YD_SQL_QUERY'] );
        }

        /**
         *  This function will return a single value.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $index   (optional) The index of the column you want to use for getting the value.
         *
         *  @returns    A single value matching the SQL statement. False when no record was found.
         */
        function getValue( $sql, $index=0 ) {
            $records = $this->getRecord( $sql );
            if (! $records ) {
                return false;
            }
            $record = array_values( $records );
            if ( ! isset( $record[ $index ] ) ) {
                return false;
            }
            return $record[ $index ];
        }

        /**
         *  This function will return a single value.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $name    The field value to return.
         *
         *  @returns    A single value matching the SQL statement, false when no value was found.
         */
        function getValueByName( $sql, $name ) {
            $record = array_values( $this->getRecords( $sql ) );
            if ( ! isset( $record[0][ strtolower( $name ) ] ) ) {
                return false;
            }
            return $record[0][ strtolower( $name ) ];
        }

        /**
         *  This function will return an array of single values.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $name    The field value to return.
         *
         *  @returns    An array of single values matching the SQL statement.
         */
        function getValuesByName( $sql, $name ) {
            $records = $this->getRecords( $sql );
            if ( ! $records ) {
                return array();
            }
            $values = array();
            foreach ( $records as $record ) {
                array_push( $values, $record[ strtolower( $name  ) ] );
            }
            return $values;
        }

        /**
         *  Get the values as an associative array using the indicated columns for keys and values.
         *
         *  @param $sql     The SQL statement to use.
         *  @param $key     The field to use for the keys.
         *  @param $val     The field or array of fileds to use for the values.
         *  @param $prefix  (optional) The text to prepend to the key name.
         *
         *  @returns An associative array.
         */
        function getAsAssocArray( $sql, $key, $val, $prefix='' ) {

            $records = $this->getRecords( $sql );
            if ( ! $records ) {
                return array();
            }
            $result = array();
            $key = strtolower( $key );
            $prefix = strtolower( $prefix );
           
            if ( is_array( $val ) ) {
                foreach ( $records as $record ) {
                    $result[ $prefix . $record[ $key ] ] = array();
                    foreach( $val as $v ) {
                        $v = strtolower( $v );
                        $result[ $prefix . $record[ $key ] ][ $v ] = $record[ $v ];
                    }
                }
            }
            else{
                $val = strtolower( $val );
                foreach ( $records as $record ) {
                    $result[ $prefix . $record[ $key ] ] = $record[ $val ];
                }
            }
           
            return $result; 

        }

        /**
         *  This function will return a single record.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    A single record matching the SQL statement.
         */
        function getRecord( $sql ) {
        }

        /**
         *  This function will execute the SQL statement and return the records as an associative array. Optionally, you
         *  can limit the number of records that are returned. Optionally, you can also specify which record to start
         *  from.
         *
         *  @param $sql The SQL statement to use.
         *  @param $limit   (optional) How many records to return
         *  @param $offset  (optional) Where to start from
         *
         *  @returns    The records matching the SQL statement as an associative array.
         */
        function getRecords( $sql, $limit=-1, $offset=-1 ) {
        }

        /**
         *  This function executes the SQL statement and returns the records as a YDRecordSet object, which contains
         *  meta information about the recordset as well as the recordset itself. Optionally, you can limit the number
         *  of records that are returned. Optionally, you can also specify which record to start from. This is the
         *  preferred way when you are using paged resultsets.
         *
         *  @param  $sql        The list of records as an array (as returned by the YDDatabaseDriver::getRecords
         *                      function.
         *  @param  $page       (optional) The page you want to retrieve. If omitted all records will be returned.
         *  @param  $pagesize   (optional) The maximum number of rows for each page. If a page number is given, the
         *                      default will be to return a maximum of 20 rows. If no page number is given, the pagesize
         *                      will be the same as the total number of rows in the recordset.
         *  @param  $pagevar    (optional) The name of the query string variable indicating the page. Defaults to "page"
         *  @param  $sizevar    (optional) The name of the query string variable indicating the page size. Defaults to
         *                      "size"

         *
         *  @returns    The records matching the SQL statement as a YDRecordSet object.
         *
         *  @todo
         *      Performance needs to be improved. This is a quick and dirty solution right now.
         */
        function getRecordsAsSet( $sql, $page=-1, $pagesize=-1, $pagevar = 'page', $sizevar = 'size' ) {

            // Get all records
            $records = $this->getRecords( $sql );

            // Return the YDRecordSet
            return new YDRecordSet( $records, $page, $pagesize, $pagevar, $sizevar );

        }

        /**
         *  This function will execute the SQL statement and return the number of affected records.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of affected rows.
         */
        function executeSql( $sql ) {
        }

        /**
         *  This function will return the number of rows matched by the SQL query.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of rows matched by the SQL query.
         */
        function getMatchedRowsNum( $sql ) {
        }

        /**
         *  This function will return the ID of the last insert for databases that support it. If the database doesn't
         *  support this, an error will be triggered.
         *
         *  @returns    The last insert ID of the last insert. Returns 0 If no auto-increment was generated, and false
         *              if there was no connection. If the database driver doesn't support this functionality, a fatal
         *              error will be raised.
         */
        function getLastInsertID() {
            trigger_error( 'The database driver ' . $this->_driver . ' does not support getLastInsertID.' );
        }

        /**
         *  This function will insert the specified values in to the specified table.
         *
         *  @param $table   The table to insert the data into.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The number of affected rows.
         */
        function executeInsert( $table, $values, $filter=true ) {
            $sql = $this->_createSqlInsert( $table, $values, $filter );
            return $this->executeSql( $sql );
        }

        /**
         *  This function will update the specified values in to the specified table using the specified where clause.
         *
         *  @param $table   The table to update the data froms.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $where   (optional) The where statement to execute.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The number of affected rows.
         */
        function executeUpdate( $table, $values, $where='', $filter=true ) {
            $sql = $this->_createSqlUpdate( $table, $values, $where, $filter );
            return $this->executeSql( $sql );
        }

        /**
         *  This function will delete the records matching the where string from the specified database table.
         *
         *  @param $table   The table to delete the data from.
         *  @param $where   (optional) The where statement to execute.
         *
         *  @returns    The number of affected rows.
         */
        function executeDelete( $table, $where='' ) {
            $sql = 'DELETE FROM ' . $table;
            if ( ! empty( $where ) ) {
                $sql .= ' WHERE ' . trim( $where );
            }
            return $this->executeSql( $sql );
        }

        /**
         *  This function will close the database connection.
         */
        function close() {
        }

        /**
         *  This function will escape a string so that it's safe to include it in an SQL statement.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string.
         */
        function escape( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return str_replace( "'", "''", $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *  This function will escape a string so that it's safe to include it in an SQL statement and will surround it
         *  with the quotes appropriate for the database backend.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string surrounded by quotes.
         */
        function escapeSql( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    return $this->_fmtQuote . $this->escape( $string ) . $this->_fmtQuote;
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }


        /**
         *  This function will escape all array elements with the quotes appropriate for the database backend.
         *
         *  @param $array  The array to escape. Eg: array( 1, null, 'hi' ) will result in string: "1, null, 'hi'"
         *
         *  @returns    The escaped string surrounded by quotes.
         */
        function escapeSqlArray( $arr ) {

			return implode( ',', array_map( array( 'YDDatabaseDriver', 'escapeSql' ), $arr ) );
		}


        /**
         *  Function to prepare an SQL statement with parameters. The parameters start counting from 1.
         
         *  This works as follows:
         *
         *  \code
         *  $sql = 'SELECT * FROM comments WHERE id = :1 and author = :2'
         *  \endcode
         *
         *  After a function call as follows:
         *
         *  \code
         *  prepareSql( $sql, 1, 'Pieter Claerhout' );
         *  \endcode
         *
         *  The SQL statement becomes:
         *
         *  \code
         *  SELECT * FROM comments WHERE id = '1' and author = 'Pieter Claerhout'
         *  \endcode
         *
         *  All the arguments are automatically escaped.
         *
         *  @returns A prepared SQL statement
         */
        function prepareSql() {

            // Check that we have at least one argument (the SQL statement)
            $numargs = func_num_args();
            if ( $numargs < 1 ) {
                trigger_error( "The function prepareSql needs at least 1 parameter.", YD_ERROR );
            }

            // Get the list of arguments
            $args = func_get_args();

            // First element is the SQL statement itself
            $sql = array_shift( $args );

            // Reset the indexes on the arguments
            $args = array_values( $args );

            // Update the SQL statement
            foreach ( $args as $key => $arg ) {
                $sql = str_replace( ':' . ($key+1), $this->escapeSql( $arg ), $sql );
            }

            // Return the SQL statement
            return $sql;

        }

        /**
         *  Format the $date in the format the database accepts. The $date parameter can be a Unix integer timestamp or
         *  an ISO format Y-m-d. Uses the fmtDate field, which holds the format to use. If null or false or '' is passed
         *  in, it will be converted to an SQL null.
         *
         *  @param $date    (optional) Unix integer timestamp or an ISO format Y-m-d. If you give it the string value
         *                  __NOW__, the current time will be used.
         *
         *  @returns    The properly formatted date for the database.
         */
        function getDate( $date='' ) {
            return $this->_getDateOrTime( $date, $this->_fmtDate );
        }

        /**
         *  Format the timestamp $ts in the format the database accepts; this can be a Unix integer timestamp or an ISO
         *  format Y-m-d H:i:s. Uses the fmtTimeStamp field, which holds the format to use. If null or false or '' is
         *  passed in, it will be converted to an SQL null.
         *
         *  @param $time    (optional) Unix integer timestamp or an ISO format Y-m-d H:i:s. If you give it the string
         *                  value __NOW__, the current time will be used.
         *
         *  @returns    The properly formatted timestamp for the database.
         */
        function getTime( $time='' ) {
            return $this->_getDateOrTime( $time, $this->_fmtTimeStamp );
        }

        /**
         *  This function will convert the argument to a date or timestamp.
         *
         *  @param $arg The argument to convert.
         *  @param $fmt     The format to return the argument in.
         *
         *  @returns    The argument as date or time.
         *
         *  @internal
         */
        function _getDateOrTime( $arg='', $fmt='' ) {
            if ( empty( $arg ) || $arg == false || $arg == null ) {
                return 'null';
            }
            if ( $arg == '__NOW__' ) {
                $arg = time();
            }
            if ( is_string( $arg ) ) {
                $arg = strtotime( $arg );
            }
            return date( $fmt, $arg );
        }

        /**
         *  This function will create an SQL insert statement from the specified array and table name.
         *
         *  @remarks
         *      All the field names that start with an underscore will not be used.
         *
         *  @param $table   The table to insert the data into.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The insert SQL statement
         *
         *  @internal
         */
        function _createSqlInsert( $table, $values, $filter=true ) {

            // Check if there are values
            if ( empty( $table ) ) {
                trigger_error( 'No table specified for the INSERT statement.', YD_ERROR );
            }

            // Get the list of field names and values
            $ifields = array();
            $ivalues = array();
            foreach ( $values as $key=>$value ) {
                if ( $filter ) {
                    if ( substr( $key, 0, 1 ) != '_' ) {
                        array_push( $ifields, $key );
                        array_push( $ivalues, $this->escapeSql( $value ) );
                    }
                } else {
                    array_push( $ifields, $key );
                    array_push( $ivalues, $this->escapeSql( $value ) );
                }
            }

            // Check if there are values
            if ( sizeof( $ifields ) == 0 ) {
                trigger_error( 'No values were submitted for the INSERT statement.', YD_ERROR );
            }

            // Convert the fields and values to a string
            $ifields = implode( ', ', $ifields );
            $ivalues = implode( ', ', $ivalues );

            // Create the SQL statement
            return 'INSERT INTO ' . $table . ' (' . $ifields . ') VALUES (' . $ivalues . ')';

        }

        /**
         *  This function will create an SQL update statement from the specified array, table name and where clause.
         *
         *  @remarks
         *      All the field names that start with an underscore will not be used.
         *
         *  @param $table   The table to insert the data into.
         *  @param $values  Associative array with the field names and their values to insert.
         *  @param $where   The where statement to execute.
         *  @param $filter  If true, all field names that start with underscore will be left out. Default: true.
         *
         *  @returns    The insert SQL statement
         *
         *  @internal
         */
        function _createSqlUpdate( $table, $values, $where='', $filter=true ) {

            // Check if there are values
            if ( empty( $table ) ) {
                trigger_error( 'No table specified for the INSERT statement.', YD_ERROR );
            }

            // Get the list of field names and values
            $uvalues = array();
            foreach ( $values as $key=>$value ) {
                if ( $filter ) {
                    if ( substr( $key, 0, 1 ) != '_' ) {
                        array_push( $uvalues, $key . "=" . $this->escapeSql( $value ) );
                    }
                } else {
                    array_push( $uvalues, $key . "=" . $this->escapeSql( $value ) );
                }
            }

            // Check if there are values
            if ( sizeof( $uvalues ) == 0 ) {
                trigger_error( 'No values were submitted for the UPDATE statement.', YD_ERROR );
            }

            // Convert the fields and values to a string
            $uvalues = implode( ', ', $uvalues );

            // Create the SQL statement
            $sql =  'UPDATE ' . $table . ' SET ' . $uvalues;
            if ( ! empty( $where ) ) { $sql .= ' WHERE ' . $where; }

            // Return the SQL
            return $sql;

        }

        /**
         *  This function will connect to the database, execute a query and will return the result handle.
         *
         *  @param $sql The SQL statement to execute.
         *
         *  @returns    Handle to the result of the query.
         *
         *  @internal
         */
        function & _connectAndExec( $sql ) {
        }

        /**
         *  This function will convert the given record so that all the keys are lowercase.
         *
         *  @param $array   The array to convert.
         *
         *  @returns    The original array with all the field names as lowercase.
         *
         *  @internal
         */
        function _lowerKeyNames( $array ) {
            if ( $array ) {
                return array_change_key_case( $array, CASE_LOWER );
            } else {
                return $array;
            }
        }

        /**
         *  This function will log the SQL statement to the debug log and keep track of the number of queries that were
         *  executed. This will only happen if YD_DEBUG equals 1 or 2
         *
         *  @param $sql The SQL statement to log.
         *
         *  @internal
         */
        function _logSql( $sql ) {

            // Show debugging info if needed
            if ( YDConfig::get( 'YD_DEBUG' ) == 1 || YDConfig::get( 'YD_DEBUG' ) == 2 ) {

                // Split the SQL in different lines
                $sql = str_ireplace( 'SELECT', 'SELECT', $sql );
                $sql = str_ireplace( 'UPDATE', 'UPDATE', $sql );
                $sql = str_ireplace( 'DELETE', 'DELETE', $sql );
                $sql = str_ireplace( 'INSERT', 'INSERT', $sql );
                $sql = str_ireplace( 'FROM', YD_CRLF . 'FROM', $sql );
                $sql = str_ireplace( 'INTO', YD_CRLF . 'INTO', $sql );
                $sql = str_ireplace( 'VALUES', YD_CRLF . 'VALUES', $sql );
                $sql = str_ireplace( 'LEFT JOIN', YD_CRLF . 'LEFT JOIN', $sql );
                $sql = str_ireplace( 'RIGHT JOIN', YD_CRLF . 'RIGHT JOIN', $sql );
                $sql = str_ireplace( 'INNER JOIN', YD_CRLF . 'INNER JOIN', $sql );
                $sql = str_ireplace( 'WHERE', YD_CRLF . 'WHERE', $sql );
                $sql = str_ireplace( 'ORDER BY', YD_CRLF . 'ORDER BY', $sql );
                $sql = str_ireplace( 'GROUP BY', YD_CRLF . 'GROUP BY', $sql );
                $sql = str_ireplace( 'LIMIT', YD_CRLF . 'LIMIT', $sql );

                // Get the backtrace
                $trace = debug_backtrace();

                // Format the SQL
                $sql_final = '';
                foreach ( explode( YD_CRLF, $sql ) as $line ) {
                    $sql_final .= '[' . $trace[2]['file'] . ':' . $trace[2]['line'] . '] ' . $line . YD_CRLF;
                }

                // Add it to the query log
                array_push( $GLOBALS['YD_SQL_QUERY'], $sql_final );

            }

        }

        /**
         *  This function will preprare an SQL SELECT statement for the getRecordsLimit and getRecordsPaged functions.
         *
         *  @param $sql The SQL statement to prepare
         *  @param $limit   (optional) How many records to return
         *  @param $offset  (optional) Where to start from
         *
         *  @internal
         */
        function _prepareSqlForLimit( $sql, $limit=-1, $offset=-1 ) { 

            // If no limit and offset, return the original SQL statement 
            if ( $limit < 0 && $offset < 0 ) {
                return $sql;
            }

            // if offset is defined but limit isn't return everything after offset 
            // if limit is defined test offset 
            if ( $limit < 0 ) {
                $sql_append = $offset . ',18446744073709551615';
            } else {
             
               // if offset is not defined return everything before limit 
               // otherwise use offset and limit 
               if ( $offset < 0 ) {
                   $sql_append = $limit;
               } else {
                   $sql_append = $offset . ',' . $limit;
               }

            } 

            // Return the changed SQL statement 
            return $sql . ' LIMIT ' . $sql_append;
        }

    }

    /**
     *  This class defines a database driver for MySQL.
     */
    class YDDatabaseDriver_mysql extends YDDatabaseDriver {

        /**
         *  This is the class constructor for the YDDatabaseDriver_mysql class.
         *
         *  @param $db      Name of the database.
         *  @param $user    (optional) User name to use for the connection.
         *  @param $pass    (optional) Password to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *  @param $host    (optional) Host name to use for the connection.
         *  @param $options (optional) Options to pass to the driver.
         */
        function YDDatabaseDriver_mysql( $db, $user='', $pass='', $host='', $options=array() ) {
            $this->YDDatabaseDriver( $db,  $user, $pass, $host, $options );
            $this->_driver = 'mysql';
        }

        /**
         *  This function will check if the server supports this database type.
         *
         *  @returns    Boolean indicating if the database type is supported by the server.
         */
        function isSupported() {
            return extension_loaded( 'mysql' );
        }

        /**
         *  This function will return the version of the database server software.
         *
         *  @returns    The version of the database server software.
         */
        function getServerVersion() {
            $result = $this->connect();

            // Handle errors
            if ( ! $result && is_null( $this->_conn ) && $this->_failOnError === true ) {
                trigger_error( mysql_error(), YD_ERROR );
            }
            if ( ! $result && ! is_null( $this->_conn ) && $this->_failOnError === true ) {
                trigger_error( mysql_error( $this->_conn ), YD_ERROR );
            }

            // Return the version
            return 'MySQL ' . mysql_get_server_info();

        }

        /**
         *  Function that makes the actual connection.
         *
         *  @returns    Boolean indicating if the connection was succesfull or not.
         */
        function connect() {
            if ( $this->_conn == null ) {
                $conn = @mysql_connect( $this->_host, $this->_user, $this->_pass );
                if ( ! $conn ) {
                    return false;
                }
                if ( ! @mysql_select_db( $this->_db, $conn ) ) {
                    return false;
                }
                $this->_conn = $conn;
            }
            return true;
        }

        /**
         *  This function will return a single record.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    A single record matching the SQL statement.
         */
        function getRecord( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? MYSQL_NUM : MYSQL_ASSOC;
            $record = $this->_lowerKeyNames( mysql_fetch_array( $result, $type ) );
            mysql_free_result( $result );
            return $record;
        }

        /**
         *  This function will execute the SQL statement and return the records as an associative array. Optionally, you
         *  can limit the number of records that are returned. Optionally, you can also specify which record to start
         *  from.
         *
         *  @param $sql The SQL statement to use.
         *  @param $limit   (optional) How many records to return
         *  @param $offset  (optional) Where to start from
         *
         *  @returns    The records matching the SQL statement as an associative array.
         */
        function getRecords( $sql, $limit=-1, $offset=-1 ) {
            $sql = $this->_prepareSqlForLimit( $sql, $limit, $offset );
            $result = & $this->_connectAndExec( $sql );
            $type = YDConfig::get( 'YD_DB_FETCHTYPE' ) == YD_DB_FETCH_NUM ? MYSQL_NUM : MYSQL_ASSOC;
            $dataset = array();
            while ( $line = $this->_lowerKeyNames( mysql_fetch_array( $result, $type ) ) ) {
                array_push( $dataset, $line );
            }
            mysql_free_result( $result );
            return $dataset;
        }

        /**
         *  This function will execute the SQL statement and return the number of affected records.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of affected rows.
         */
        function executeSql( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            return mysql_affected_rows( $this->_conn );
        }

        /**
         *  This function will return the number of rows matched by the SQL query.
         *
         *  @param $sql The SQL statement to use.
         *
         *  @returns    The number of rows matched by the SQL query.
         */
        function getMatchedRowsNum( $sql ) {
            $result = & $this->_connectAndExec( $sql );
            return mysql_num_rows( $result );
        }

        /**
         *  This function will return the ID of the last insert for databases that support it. If the database doesn't
         *  support this, an error will be triggered.
         *
         *  @returns    The last insert ID of the last insert. Returns 0 If no auto-increment was generated, and false 
         *              if there was no connection
         */
        function getLastInsertID() {
            return mysql_insert_id( $this->_conn );
        }

        /**
         *  This function will close the database connection.
         */
        function close() {
            if ( $this->_conn != null ) {
                $this->_conn = null;
                @mysql_close( $this->_conn );
            }
        }

        /**
         *  This function will escape a string so that it's safe to include it in an SQL statement.
         *
         *  @param $string  The string to escape.
         *
         *  @returns    The escaped string.
         */
        function escape( $string ) {
            if ( is_string( $string ) ) {
                if ( strtolower( $string ) != 'null' ) {
                    $this->connect();
                    return mysql_real_escape_string( $string );
                }
            } else if ( is_null( $string ) ) {
                return 'null';
            }
            return $string;
        }

        /**
         *  This function will connect to the database, execute a query and will return the result handle.
         *
         *  @param $sql The SQL statement to execute.
         *
         *  @returns    Handle to the result of the query.
         *
         *  @internal
         */
        function & _connectAndExec( $sql ) {

            // Add the table prefix
            $sql = str_replace( ' #_', ' ' . YDConfig::get( 'YD_DB_TABLEPREFIX', '' ), $sql );

            // Log the statement
            $this->_logSql( $sql );

            // Connect
            $result = $this->connect();

            // Handle errors
            if ( ! $result && is_null( $this->_conn ) && $this->_failOnError === true ) {
                trigger_error( mysql_error(), YD_ERROR );
            }
            if ( ! $result && ! is_null( $this->_conn ) && $this->_failOnError === true ) {
                trigger_error( mysql_error( $this->_conn ), YD_ERROR );
            }

            // Execute the query
            $result = @mysql_query( $sql, $this->_conn );

            // Handle errors
            if ( $result === false && $this->_failOnError === true ) {
                trigger_error( '[' . mysql_errno( $this->_conn ) . '] ' . mysql_error( $this->_conn ), YD_ERROR );
            }

            // Return the result
            return $result;

        }

    }

?>