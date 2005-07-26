<?php
 
    /*

        Yellow Duck Framework version 2.0
        Copyright (C) (c) copyright 2004 Pieter Claerhout

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
    include_once( dirname( __FILE__ ) . '/html2pdf.php' );

    /**
     *  This class defines a YDPdfProject object.
     */
    class YDPdfReport extends YDAddOnModule {

        /**
         *    The class constructor 
         */
        function YDPdfReport() {
            $this->YDAddOnModule();

            // Setup the module
            $this->_author       = 'Francisco Azevedo';
            $this->_version      = '0.0.2 beta :)';
            $this->_copyright    = '(c) 2005 Francisco Azevedo';
            $this->_description  = 'This class defines a YDPDF object. Based on Radek HULAN work.';
        
            // main vars
            $this->html          = '';      // html text to convert to PDF
            $this->title         = '';      // article title
            $this->article       = '';      // article name
            $this->author        = '';      // article author
            $this->setdate       = false;   // set date
            $this->setHrule      = false;
            $this->date          = time();  // date being published

            // other options
            $this->directory     = '/';     // directory for temp files
            $this->http          = '/';     // http path
            $this->delete        = 60;      // keep temp files for 60 minutes
            $this->useiconv      = false;   // use iconv
            $this->bi            = false;   // support bold tags

            // pdf object
            $this->pdf = new PDF('P', 'mm', 'A4', $this->title, '', false);
            $this->pdf->Open();

            // set compression, creator and display
            $this->pdf->SetCompression(true);
            $this->pdf->SetCreator(YD_FW_NAME);
            $this->pdf->SetDisplayMode('real');

        }

        /**
         *  @internal
         */
        function _convert( $s ){
            if ( $this->useiconv ) {
                return iconv( 'iso-8859-2', 'cp1250', $s );
            } else {
                return $s;
            }
        }

        /**
         *  Defines the author for the pdf document
         *
         *  @param  $author    Pdf author
         */
        function setAuthor( $author ){
            $this->author = $author;
        }

        /**
         *  Defines language parameters
         *
         *  @param  $language    Language, eg: array('page' => 'Page ',  'createdby' => 'Me', 'pageseparator' => "/", 'createdlink'    => "My homepage");
         */
        function setLanguage( $language ){
            $this->pdf->setLanguage( $language );
        }

        /**
         *  Defines article name
         *
         *  @param  $article    Article name
         */
        function setArticle( $article ){
            $this->article = $article;
        }

        /**
         *  Defines the document title
         *
         *  @param  $title    Document title
         */
        function setTitle( $title ){
            $this->title = $title;
        }

        /**
         *  Defines the html content to parse
         *
         *  @param  $html    HTML string to parse
         */
        function setHTML( $html ){
            $this->html = $html;
        }

        /**
         *  Defines the color used in headers
         *
         *  @param  $r    RED decimal
         *  @param  $g    GREEN decimal
         *  @param  $b    BLUE decimal
         */        
        function setHeaderColor( $r, $g, $b ){
            $this->pdf->setHeaderColor( $r, $g, $b );
        }

        /**
         *  Creates the pdf file
         *
         *  @param  $format     (optional) The format for the download. Default is "download".
         *  @param  $name       (optional) Filepath
         */                
        function output( $format = 'download', $name = 'ydf.pdf' ) {
        
            // change some win codes, and xhtml into html
            $str = array(
                '<br />'   => '<br>',
                '<hr />'    => '<hr>',
                '[r]'       => '<red>',
                '[/r]'      => '</red>',
                '[l]'       => '<blue>',
                '[/l]'      => '</blue>',
                '&#8220;'   => '"',
                '&#8221;'   => '"',
                '&#8222;'   => '"',
                '&#8230;'   => '...',
                '&#8217;'   => '\''
            );

            // replace above tags
            foreach ($str as $_from => $_to) {
                $this->html = str_replace( $_from, $_to, $this->html );
            }

            // set title, author and create the first page
            $this->pdf->SetTitle( $this->title );
            $this->pdf->SetAuthor( $this->author );
            $this->pdf->AddPage();

            // set title
            if ( $this->title != '' ) {
                $this->pdf->PutMainTitle( $this->_convert( $this->title ) );
            }

            // set article name
            if ( $this->article != '' ){
                $this->pdf->PutMinorHeading( 'Módulo' );
                $this->pdf->PutMinorTitle( $this->_convert( $this->article ) );
            }
            
            // set date
            if ( $this->setdate ) {
                $this->pdf->PutMinorHeading( "Criado em " . date( "d - m - Y, H:i", $this->date ) );
            }

            // create line separator if there are elements before
            if ( $this->setHrule ){
                $this->pdf->PutLine();
                $this->pdf->Ln( 2 );
            }

            // define html
            $this->pdf->WriteHTML( $this->_convert( stripslashes( $this->html ) ), $this->bi );

            // create it
            $this->pdf->create( $format, $name );

            // Return true
            return true;

        }

    }
?>
