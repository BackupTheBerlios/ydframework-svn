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
    include_once( dirname( __FILE__ ) . '/YDFormElement_Select.php');

    /**
     *	This is the class that define a select timezone.
     */
    class YDFormElement_Timezone extends YDFormElement_Select {

        /**
         *	This is the class constructor for the YDFormElement_Timezone class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) Format string: 'simple' or 'full'
         */
        function YDFormElement_Timezone( $form, $name, $label='', $attributes=array(), $options='full' ) {

            // initialize parent
            $this->YDFormElement_Select( $form, $name, $label, $attributes, YDArrayUtil::getGMT( $options ) );

            // set type
            $this->_type = 'timezone';

			// set default
			$this->setDefault( 0 );
        }

    }

?>