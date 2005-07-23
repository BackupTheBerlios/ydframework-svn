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
    include_once( dirname( __FILE__ ) . '/YDUrl.php');
    include_once( dirname( __FILE__ ) . '/YDUtil.php');
    include_once( dirname( __FILE__ ) . '/YDPersistent.php');

    // Configure the default for this class
    YDConfig::set( 'YD_DB_DEFAULTPAGESIZE', 20, false );

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

            // Sort the records
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

            // Get the number of pages
            $this->totalPages = ceil( sizeof( $this->records ) / ( float ) $this->pagesize );
            $this->totalRows = sizeof( $this->records );

            // Set the number of pages correctly to zero for an empty recordset
            if ( $this->totalPages == 0 ) {
                $this->page = 0;
            }

            // Fix the page number if bigger than the amount of pages
            if ( $this->page > $this->totalPages ) {
                $this->page = $this->totalPages;
            }

            // Get the offset
            $this->offset = $this->pagesize * ( $this->page - 1 );

            // Get the subset of the records we need
            $this->set = array_slice( $this->records, $this->offset, $this->pagesize );

            // Get the total number of rows on a page
            $this->totalRowsOnPage = sizeof( $this->set );

            // Get the previous and next page
            $this->pagePrevious = ( $this->page <= 1 ) ? false : $this->page - 1;
            $this->pageNext = ( $this->page >= $this->totalPages ) ? false : $this->page + 1;

            // Indicate if we are on the last or first page
            $this->isFirstPage = ( $this->pagePrevious == false ) ? true : false;
            $this->isLastPage = ( $this->pageNext == false ) ? true : false;

            // Add the list of pages as an array
            $this->pages = ( $this->totalPages <= 1 ) ? array() : range( 1, $this->totalPages );

            // Publish the URL as an object
            $this->url = new YDUrl( YD_SELF_URI );

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
            if ( YDPersistent::get( $this->sortvar, '' ) == $sortfld ) {
                if ( strtoupper( YDPersistent::get( $this->sortdir ) ) == 'ASC' ) {
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

    }

?>