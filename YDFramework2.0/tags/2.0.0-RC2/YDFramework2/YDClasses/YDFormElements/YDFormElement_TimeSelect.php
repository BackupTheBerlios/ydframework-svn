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
    include_once( dirname( __FILE__ ) . '/../YDForm.php');
    include_once( dirname( __FILE__ ) . '/YDFormElement_Select.php');

    /**
     *	This is the class that define a time select form element.
     */
    class YDFormElement_TimeSelect extends YDFormElement {

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

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // The default value is an array
            $this->setValue();

            // Set the type
            $this->_type = 'timeselect';

            // Get the names of the hours
            $hours = array();
            for ( $i = 0; $i <= 23; $i++ ) {
                $hours[$i] = ( strlen( $i ) == 1 ) ? '0' . $i : $i;
            }

            // Get the names of the minutes
            $minutes = array();
            for ( $i = 0; $i <= 59; $i++ ) {
                $minutes[$i] = ( strlen( $i ) == 1 ) ? '0' . $i : $i;
            }

            // Add the different elements
            $this->hours = new YDFormElement_Select(
                $this->_form, $this->_name . '[hours]', '', $this->_attributes, $hours
            );
            $this->minutes = new YDFormElement_Select(
                $this->_form, $this->_name . '[minutes]', '', $this->_attributes, $minutes
            );

        }

        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            
            if ( ! is_null( $this->_default ) ) {
                if ( intval( $this->_value['hours'] )   == intval( $this->_default['hours'] ) 
                  && intval( $this->_value['minutes'] ) == intval( $this->_default['minutes'] ) ) {
                    return false;
                }
            }
            return true;

        }
        
        /**
         *      This function sets the default value of the element.
         *
         *      @param  $val    The default value of this object.
         *      @param  $raw    (optional) Boolean indicating if the default value is a raw value.
         */
        function setDefault( $val, $raw=false ) {
            
            $this->_raw_default = $raw;
            
            if ( is_numeric( $val ) ) {
                if ( ! is_int( $val ) ) {
                    $val = intval( $val );
                }
                $now = getdate( $val );
                $this->_default = array();
                $this->_default['hours'] = $now['hours'];
                $this->_default['minutes'] = $now['minutes'];
            } elseif ( ! empty( $val ) ) {
                $this->_default = $val;
            }
        }

        /**
         *	This function sets the value for the date element.
         *
         *	@param	$val	(optional) The value for this object.
         */
        function setValue( $val=array() ) {
            $now = getdate();
            if ( is_numeric( $val ) ) {
                $now = getdate( $val );
                if ( ! is_int( $val ) ) {
                    $val = intval( $val );
                }
                $this->_value = array();
                $this->_value['hours'] = $now['hours'];
                $this->_value['minutes'] = $now['minutes'];
            } elseif ( $val == array() ) {
                $this->_value['hours'] = $now['hours'];
                $this->_value['minutes'] = $now['minutes'];
            } else {
                $this->_value = $val;
                if ( ! isset( $this->_value['hours'] ) ) {
                    $this->_value['hours'] = $now['hours'];
                }
                if ( ! isset( $this->_value['minutes'] ) ) {
                    $this->_value['minutes'] = $now['minutes'];
                }
            }
            if ( strlen( $this->_value['hours'] ) == 1 ) {
                $this->_value['hours_with_zero'] = '0' . $this->_value['hours'];
            } else {
                $this->_value['hours_with_zero'] = $this->_value['hours'];
            }
            if ( strlen( $this->_value['minutes'] ) == 1 ) {
                $this->_value['minutes_with_zero'] = '0' . $this->_value['minutes'];
            } else {
                $this->_value['minutes_with_zero'] = $this->_value['minutes'];
            }
            $this->_value['timestamp_string'] = $this->_value['hours_with_zero']
                                              . $this->_value['minutes_with_zero']
                                              . '00';
        }

        /**
         *	This will return a unix timestamp of the selected time.
         *
         *	@param $format	(optional) The format if you want to get a formatted date/time. If null, an integer is
         *					returned. The syntax is the one as used by the strftime function.
         *
         *	@returns	An integer with the current date/time stamp.
         */
        function getTimeStamp( $format=null ) {
            $this->setValue( $this->_value );
            $tstamp = mktime( $this->_value['hours'], $this->_value['minutes'], 0, 1, 1, 1980 );
            if ( is_null( $format ) ) {
                return $tstamp;
            } else {
                return strftime( $format, $tstamp );
            }
        }

        /**
         *	This function will return the element as HTML.
         *
         *	@returns	The form element as HTML text.
         */
        function toHtml() {

            // Update the value
            $this->setValue( $this->_value );

            // Convert to HTML
            $this->hours->_value = isset( $this->_value['hours'] ) ? $this->_value['hours'] : '';
            $this->minutes->_value = isset( $this->_value['minutes'] ) ? $this->_value['minutes'] : '';
            return $this->hours->toHtml() . ' : ' . $this->minutes->toHtml();

        }

    }

?>