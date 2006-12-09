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

	// include antispam lib
    include_once( YD_DIR_HOME . '/3rdparty/captcha/php-captcha.inc.php' );


    /**
     *  This class handles a captcha image
     */
    class YDCaptcha extends YDAddOnModule {

        /**
         *  Class constructor for the YDCaptcha class.
         */
        function YDCaptcha() {

            // Initializes YDBase
            $this->YDAddOnModule();

            // Setup the module
            $this->_author = 'Francisco Azevedo';
            $this->_version = '1.0';
            $this->_copyright = '(c) 2006 Francisco Azevedo, francisco@fusemail.com';
            $this->_description = 'This class manages a captcha image system.';

			// compute fonts directory
			$fdir = YD_DIR_HOME . '/3rdparty/fonts/';

			// define fonts to use
			$fonts = array( $fdir . 'VeraBd.ttf', $fdir . 'VeraIt.ttf', $fdir . 'Vera.ttf');

			// create image object
			$this->_img = new PhpCaptcha( $fonts, 150, 40 );
			
			$this->_img->DisplayShadow( false );
			$this->_img->UseColour( false );
			$this->_img->SetNumChars( 4 );
        }


        /**
         *	This function set the number of characters to display
         *
         *	@param $num		Number of characters
         */
        function setNumChars( $num ){
			$this->_img->setNumChars( $num );
		}


        /**
         *	This function defines use of shadows
         *
         *	@param $flag		True or false boolean
         */
        function displayShadow( $flag ){
			$this->_img->displayShadow( $flag );
		}


        /**
         *	This function defines use of colours
         *
         *	@param $flag		True or false boolean
         */
        function useColour( $flag ){
			$this->_img->useColour( $flag );
		}


        /**
         *  This function exports the image
         */
        function create() {
			return $this->_img->Create();
        }

  }

?>