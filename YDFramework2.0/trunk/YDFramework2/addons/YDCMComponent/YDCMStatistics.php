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

    /*
     *  @addtogroup YDCMComponent Addons - CMComponent
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *  @ingroup YDCMComponent
     */
    class YDCMStatistics {

        function YDCMStatistics() {
		}
		

        /**
         *  This static function returns php informations
         *
         *  @returns    an array with php info
         */
		function php(){
		
            // Get the general PHP info
            ob_start();
            phpinfo( INFO_GENERAL );
            $phpInfo .= ob_get_contents();
            ob_end_clean();

            // Get the right part
            $phpInfo = substr( $phpInfo, strpos( $phpInfo, '<tr>' ) );
            $phpInfo = substr( $phpInfo, 0, strpos( $phpInfo, '</table>' ) );

            // Strip unneeded things
            $phpInfo = str_replace( '<tr><td class="e">', '', $phpInfo );
            $phpInfo = str_replace( ' </td></tr>', '', $phpInfo );
            $phpInfo = trim( $phpInfo );

            // Get the settings
            $settings = array();
            foreach ( explode( "\n", $phpInfo ) as $line ) {
                $line = explode( ' </td><td class="v">', $line );
                if ( isset( $line[1] ) ) $settings[ strtolower( str_replace( ' ', '_', $line[0] ) ) ] = $line[1];
                else                     $settings[ strtolower( str_replace( ' ', '_', $line[0] ) ) ] = '';
            }

			// export other values
			$settings[ 'version' ]     = phpversion();
			$settings[ 'modules' ]     = implode( get_loaded_extensions(), ', ' );
			$settings[ 'os' ]          = PHP_OS;
			$settings[ 'includePath' ] = $GLOBALS['YD_INCLUDE_PATH'];

			return $settings;
		}


        /**
         *  This static function returns the mail info
         *
         *  @returns    an array with mail info
         */
		function mailer(){

		    // Includes
		    require_once( dirname( __FILE__ ) . '/../3rdparty/phpmailer/class.phpmailer.php' );

            // Get the version of phpMailer
            $PHPMailer = new PHPMailer();

			return array( 'version' => $PHPMailer->Version, 'complete' => 'PHPMailer [version ' . $PHPMailer->Version . ']' );
		}


        /**
         *  This static function returns an YDCMStatistics module
         *
         *  @returns    an module object
         */
		function module( $name ){

			// include lib
			// TODO: check if name is a valid lib name
			require_once( YDConfig::get( 'YD_DBOBJECT_PATH' ) . '/' . $name . '.php' );

			// return class
			return new $name();
		}


    }

?>