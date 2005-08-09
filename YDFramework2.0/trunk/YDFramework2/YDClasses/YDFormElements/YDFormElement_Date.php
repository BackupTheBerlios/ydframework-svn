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
     *  This is the class that define a datetime select form element.
     */
    class YDFormElement_Date extends YDFormElement {

        /**
         *  This is the class constructor for the YDFormElement_DateTimeSelect class.
         *
         *  @param $form        The name of the form to which this element is connected.
         *  @param $name        The name of the form element.
         *  @param $label       (optional) The label for the form element.
         *  @param $attributes  (optional) The attributes for the form element.
         *  @param $options     (optional) The options for the elment.
         */
        function YDFormElement_Date( $form, $name, $label='', $attributes=array(), $options=array() ) {

            // Initialize the parent
            $this->YDFormElement( $form, $name, $label, $attributes, $options );

            // The default value is an array
            $this->setValue();

            // Set the type
            $this->_type = 'date';
            
            // Parse the options
            $elements = $this->_getElements();

            // Set default parameters
            $now = getdate();

            // Get the starting and ending
            $yearstart    = $this->_getOption( 'yearstart',   $now['year'] - 5 );
            $yearend      = $this->_getOption( 'yearend',     $now['year'] + 5 );
            $monthstart   = $this->_getOption( 'monthstart',  1 );
            $monthend     = $this->_getOption( 'monthend',    12 );
            $daystart     = $this->_getOption( 'daystart',    1 );
            $dayend       = $this->_getOption( 'dayend',      31 );
            $hoursstart    = $this->_getOption( 'hoursstart',   0 );
            $hoursend      = $this->_getOption( 'hoursend',     23 );
            $minutesstart  = $this->_getOption( 'minutesstart', 0 );
            $minutesend    = $this->_getOption( 'minutesend',   59 );
            $secondsstart  = $this->_getOption( 'secondsstart', 0 );
            $secondsend    = $this->_getOption( 'secondsend',   59 );
            
            // Get the offsets
            $yearoffset   = $this->_getOption( 'yearoffset',    1 );
            $monthoffset  = $this->_getOption( 'monthoffset',   1 );
            $dayoffset    = $this->_getOption( 'dayoffset',     1 );
            $hoursoffset   = $this->_getOption( 'hoursoffset',    1 );
            $minutesoffset = $this->_getOption( 'minutesoffset',  1 );
            $secondsoffset = $this->_getOption( 'secondsoffset',  1 );
            
            // Get the arrays of values
            $year    = $this->_getArray( $yearstart,    $yearend,    $yearoffset );
            $month   = $this->_getArray( $monthstart,   $monthend,   $monthoffset, true );
            $day     = $this->_getArray( $daystart,     $dayend,     $dayoffset );
            $hours   = $this->_getArray( $hoursstart,   $hoursend,   $hoursoffset );
            $minutes = $this->_getArray( $minutesstart, $minutesend, $minutesoffset );
            $seconds = $this->_getArray( $secondsstart, $secondsend, $secondsoffset );
            
            // Add the elements
            foreach ( $elements as $element ) {
                
                $this->$element = new YDFormElement_Select(
                    $this->_form, $this->_name . '[' . $element . ']', '', $this->_attributes, $$element
                );
                
            }

        }
        
        /**
         *      This function returns the integer value of the option $name, if passed,
         *      or the $default value otherwise.
         *
         *      @param  $name     The option name
         *      @param  $default  The defaul value in case the option is missing.
         *
         *      @returns          The integer option value or the default value.
         *      
         *      @internal
         */
        function _getOption( $name, $default ) {
            if ( isset( $this->_options[$name] ) && is_numeric( $this->_options[$name] ) ) {
                return intval( $this->_options[$name] );
            }
            return $default;
        }
        
        /**
         *      This function returns an array of values from $start to $end and
         *      adding $offset at each iteration.
         *
         *      @param  $start     The start value.
         *      @param  $end       The end value.
         *      @param  $offset    The offset value.
         *      @param  $month    (Optional) If true, returns the month names (if
         *                        option 'monthnumber' is not set). Default: false.
         *
         *      @returns          An array with the values.
         *      
         *      @internal
         */
        function _getArray( $start, $end, $offset, $month=false ) {
            
            $arr = array();
            for ( $i = $start; $i <= $end; $i = $i + $offset ) {
                if ( $month && ! isset( $this->_options[ 'monthnumber' ] ) ) {
                    $format = '%B';
                    if ( isset( $this->_options[ 'monthabbr' ] ) ) {
                        $format = '%b';
                    }
                    $value = strtolower( strftime( $format, mktime( 0, 0, 0, $i, 1, 2000 ) ) );
                    if ( isset( $this->_options[ 'monthucfirst' ] ) ) {
                        $value = ucfirst( $value );
                    }
                    $arr[$i] = $value;
                } else {
                    $arr[$i] = ( strlen( $i ) == 1 ) ? '0' . $i : $i;
                }
            }
            
            return $arr;
            
        }
        
        /**
         *      This function returns an array with the subelements defined in the options
         *      of the form element.
         *
         *      @returns          An array with the subelements.
         *      
         *      @internal
         */
        function _getElements() {
            
            $elem = $this->_options;
            if ( array_key_exists( 'elements', $this->_options ) ) {
                $elem = $this->_options[ 'elements' ];
            }

            $elements = array();
            foreach ( $elem as $key => $value ) {
                
                if ( is_int( $key ) ) {
                    switch ( $value ) {
                        
                        case 'datetime':
                            $elements[] = 'day';
                            $elements[] = 'month';
                            $elements[] = 'year';
                            $elements[] = 'hours';
                            $elements[] = 'minutes';
                            $elements[] = 'seconds';
                            break;
                        case 'date':
                            $elements[] = 'day';
                            $elements[] = 'month';
                            $elements[] = 'year';
                            break;
                        case 'time':
                            $elements[] = 'hours';
                            $elements[] = 'minutes';
                            $elements[] = 'seconds';
                            break;
                        case 'day':
                        case 'month':
                        case 'year':
                        case 'hours':
                        case 'minutes':
                        case 'seconds':
                            $elements[] = $value;
                            break;
                    }
                }
            }
            
            if ( empty( $elements ) ) {
                $elements = array( 'day', 'month', 'year' );
            }
            return $elements;
            
        }
        
        /**
         *      This function returns a boolean indicating if the element value was
         *      modified from it's default value.
         *
         *      @returns        Boolean indicating if the element was modified.
         */
        function isModified() {
            
            if ( ! is_null( $this->_default ) ) {
                foreach ( $this->_getElements() as $element ) {
                    if ( intval( $this->_value[ $element ] ) != intval( $this->_default[ $element ] ) ) {
                        return true;
                    }
                }
                return false;
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
                
                $getdate = array(
                    'day'     => 'mday',
                    'month'   => 'mon',
                    'year'    => 'year',
                    'hours'   => 'hours',
                    'minutes' => 'minutes',
                    'seconds' => 'seconds'
                );
                
                foreach ( $this->_getElements() as $element ) {
                    $this->_default[ $element ] = $now[ $getdate[ $element ] ];
                }
                
            } elseif ( ! empty( $val ) ) {
                $this->_default = $val;
            }
            
        }

        /**
         *  This function sets the value for the date element.
         *
         *  @param  $val    (optional) The value for this object.
         */
        function setValue( $val=array() ) {

            $now = getdate();
            $elements = $this->_getElements();

            $getdate = array(
                'day'     => 'mday',
                'month'   => 'mon',
                'year'    => 'year',
                'hours'   => 'hours',
                'minutes' => 'minutes',
                'seconds' => 'seconds'
            );
            
            if ( is_numeric( $val ) ) {
                
                $now = getdate( $val );
                
                if ( ! is_int( $val ) ) {
                    $val = intval( $val );
                }
                
                $this->_value = array();
                
                foreach ( $elements as $element ) {
                    $this->_value[ $element ] = $now[ $getdate[ $element ] ];
                }
                
            } elseif ( $val == array() ) {
                
                foreach ( $elements as $element ) {
                    $this->_value[ $element ] = $now[ $getdate[ $element ] ];
                }
                
            } else {
                
                $this->_value = $val;
                
                foreach ( $elements as $element ) {
                    if ( ! isset( $this->_value[ $element ] ) ) {
                        $this->_value[ $element ] = $now[ $getdate[ $element ] ];
                    }
                }
            }
            
            foreach ( $elements as $element ) {
                if ( $element == 'year' ) {
                    continue;
                }
                if ( strlen( $this->_value[ $element ] ) == 1 ) {
                    $this->_value[ $element . '_with_zero' ] = '0' . $this->_value[ $element ];
                } else {
                    $this->_value[ $element . '_with_zero' ] = $this->_value[ $element ];
                }
            }
            
            $this->_value['timestamp_string'] = '';
            
            if ( isset( $this->_value['year'] ) ) {
                $this->_value['timestamp_string'] .=  $this->_value['year'];
            }
            if ( isset( $this->_value['month_with_zero'] ) ) {
                $this->_value['timestamp_string'] .=  $this->_value['month_with_zero'];
            }
            if ( isset( $this->_value['day_with_zero'] ) ) {
                $this->_value['timestamp_string'] .=  $this->_value['day_with_zero'];
            }
            if ( isset( $this->_value['hours_with_zero'] ) ) {
                $this->_value['timestamp_string'] .=  $this->_value['hours_with_zero'];
            }
            if ( isset( $this->_value['minutes_with_zero'] ) ) {
                $this->_value['timestamp_string'] .=  $this->_value['minutes_with_zero'];
            }
            if ( isset( $this->_value['seconds_with_zero'] ) ) {
                $this->_value['timestamp_string'] .=  $this->_value['seconds_with_zero'];
            }
            
        }

        /**
         *  This will return a unix timestamp of the selected time.
         *
         *  @param $format  (optional) The format if you want to get a formatted date/time.
         *                             If null, an integer is returned. The syntax is the
         *                             one as used by the strftime function.
         *
         *  @returns    An integer with the current date/time stamp.
         */
        function getTimeStamp( $format=null ) {
            
            $this->setValue( $this->_value );
            
            $elements = array( 'day', 'month', 'year', 'hours', 'minutes', 'seconds' );
            
            foreach ( $elements as $element ) {
                if ( isset( $this->_value[ $element ] ) ) {
                    $$element = $this->_value[ $element ];
                } else {
                    $$element = 0;
                }
            }
            
            if ( $year == 0 || $month == 0 || $day == 0 ) {
                // trigger_error( 'Unable to calculate the timestamp of "' . $this->_name . '"', YD_ERROR );
                return;
            }
            
            $tstamp = mktime( $hours, $minutes, $seconds, $month, $day, $year );
            
            if ( is_null( $format ) ) {
                return $tstamp;
            } else {
                return strftime( $format, $tstamp );
            }
        }

        /**
         *  Function to set the attribute of a form element
         *
         *  @param  $key    The name of the attribute
         *  @param  $val    The value of the attribute
         */
        function setAttribute( $key, $val ) {

            $this->_attributes[$key] = $val;
            
            foreach ( $this->_getElements() as $element ) {
                $this->$element->setAttribute( $key, $val );
            }
            
        }

        /**
         *  This function will return the element as HTML.
         *
         *  @returns    The form element as HTML text.
         */
        function toHtml() {
            
           // Update the value
            $this->setValue( $this->_value );
            
            // Update the values of the subelements
            $elements = $this->_getElements();
            
            foreach ( $elements as $element ) {
                $this->$element->_value = isset( $this->_value[ $element ] ) ? $this->_value[ $element ] : '';
            }
            
            // Convert to HTML
            
            $dates = array( 'day', 'month', 'year' );
            $times = array( 'hours', 'minutes', 'seconds' );
            
            $html = '';
            $first = true;
            $date  = false;
            $time  = false;
            
            foreach ( $elements as $element ) {
            
                if ( ! $first ) {
                    if ( $date && in_array( $element, $dates ) ) {
                        $html .= ' / ';
                    } else if ( $time && in_array( $element, $times ) ) {
                        $html .= ' : ';
                    } else {
                        $html .= ' - ';
                    }
                }
            
                $html .= $this->$element->toHtml();
                
                if ( in_array( $element, $dates ) ) {
                    $date = true;
                    $time = false;
                } else {
                    $time = true;
                    $date = false;
                }
                $first = false;
            }
            
            return $html;

        }

    }

?>