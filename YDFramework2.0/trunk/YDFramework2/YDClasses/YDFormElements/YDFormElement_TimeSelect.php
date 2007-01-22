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
    include_once( YD_DIR_HOME_CLS . '/YDForm.php');
    include_once( YD_DIR_HOME_CLS . '/YDFormElements/YDFormElement_Date.php');

    /**
     *	This is the class that define a datetime select form element.
     *
     *  @ingroup YDForm
     */
    class YDFormElement_TimeSelect extends YDFormElement_Date {

        /**
         *	This is the class constructor for the YDFormElement_TimeSelect class.
         *
         *	@param $form		The name of the form to which this element is connected.
         *	@param $name		The name of the form element.
         *	@param $label		(optional) The label for the form element.
         *	@param $attributes	(optional) The attributes for the form element.
         *	@param $options		(optional) The options for the elment.
         */
        function YDFormElement_TimeSelect( $form, $name, $label='', $attributes=array(), $options=array() ) {

            $invalid = array( 'day', 'month', 'year', 'hours', 'minutes', 'seconds', 'date', 'time', 'datetime' );
            
            unset( $options[ 'elements' ] );
            
            foreach ( $options as $key => $value ) {
                if ( is_int( $key ) && in_array( $value, $invalid ) ) {
                    unset( $options[ $key ] );
                } 
            }
            
            $options = array_merge( $options, array( 'time' ) );

            // Initialize the parent
            $this->YDFormElement_Date( $form, $name, $label, $attributes, $options );

            // Set the type
            $this->_type = 'timeselect';
            
        }

    }

?>