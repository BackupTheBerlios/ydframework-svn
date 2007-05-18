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
     *  @addtogroup YDCaptcha Addons - Captcha
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }


    /**
     *  This config defines the total characters to use
     *  Default: 5.
     */
    YDConfig::set( 'YD_CAPTCHA_NUMCHARS', 5, false );


    /**
     *  This config defines if image should have color
     *  Default: false.
     */
    YDConfig::set( 'YD_CAPTCHA_COLOR', false, false );


    /**
     *  This config defines if image should have shadow
     *  Default: false.
     */
    YDConfig::set( 'YD_CAPTCHA_SHADOW', false, false );



    // include antispam lib
    include_once( YD_DIR_HOME . '/3rdparty/captcha/php-captcha.inc.php' );


    /**
     *  This class handles a captcha image
     *
     *  @ingroup YDCaptcha
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
            $this->_img = new PhpCaptcha( $fonts, 200, 40 );
            
            // add simbols ;) 
            // don't add possible character problems for user, eg, l <--> 1 (lower L or number ONE?), 0 <--> O (ZERO OR upper O?)
            $this->_img->SetCharSet( "a-h,!,j-k,#,&,%,$,m-n,@,p-r,2-4,6,£,?,8-9,t-w,y-z,#,&,%,$,A-H,!,J-K,#,&,%,$,M-N,@,P-R,T-W,Y-Z,2-4,6,£,?,8-9" );
        }


        /**
         *  This function exports the image
         */
        function create() {

            $this->_img->DisplayShadow( YDConfig::get( 'YD_CAPTCHA_SHADOW' ) );
            $this->_img->useColour( YDConfig::get( 'YD_CAPTCHA_COLOR' ) );
            $this->_img->SetNumChars( YDConfig::get( 'YD_CAPTCHA_NUMCHARS' ) );

            return $this->_img->Create();
        }

  }

?>