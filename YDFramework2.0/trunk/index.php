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

    // Standard include
    require_once( dirname( __FILE__ ) . '/YDFramework2/YDF2_init.php' );

    // Includes
    require_once( 'YDRequest.php' );

    // Class definition
    class index extends YDRequest {

        // Class constructor
        function index() {
            $this->YDRequest();
        }

        // Default action
        function actionDefault() {
            $this->redirect( 'examples/index.php' );
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
