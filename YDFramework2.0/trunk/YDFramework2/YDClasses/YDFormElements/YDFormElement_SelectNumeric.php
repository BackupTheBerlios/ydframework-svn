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
     *  @addtogroup YDForm Core - Form
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    // Includes
    include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Select.php');

    /**
     *	This is the class that define a numerical select box form element.
     *
     *  @ingroup YDForm
     */
    class YDFormElement_SelectNumeric extends YDFormElement_Select {

        /**
         *	This is the class constructor for the YDFormElement_SelectNumeric class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) Associative array with the values to show in the select box.
         */
        function YDFormElement_SelectNumeric( $form, $name, $label='', $attributes=array(), $options=array() ) {

			// compute default values
			$min = isset( $options[ 'min' ] ) ? $options[ 'min' ] : 1;
			$max = isset( $options[ 'max' ] ) ? $options[ 'max' ] : 20;
			$med = isset( $options[ 'med' ] ) ? $options[ 'med' ] : 5;
			$interval = isset( $options[ 'interval' ] ) ? $options[ 'interval' ] : 2;

			// compute real options array
			$options = array();
			for( ; $min < $med; $min ++ )
				$options[ $min ] = $min;

			for( ; $min <= $max; $min += $interval )
				$options[ $min ] = $min;

            // Initialize the parent
            $this->YDFormElement_Select( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'selectnumeric';
        }

    }

?>