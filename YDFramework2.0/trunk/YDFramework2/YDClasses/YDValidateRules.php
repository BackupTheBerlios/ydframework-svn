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
     *  @addtogroup YDFramework Core
     */

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *	This class contains abstract functions implementing the validation rules.
     *
     *  @ingroup YDFramework
     */
    class YDValidateRules extends YDBase {

        /**
         *	This function returns false if the variable is empty, otherwise, it returns true.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function required( $val, $opts='', $formelement = null ) {

            if( is_object( $formelement ) ){

                $type = $formelement->getType();

                if( $type == 'checkboxgroup' ){

                    // check if there is any 'on' (selected) value
                    foreach( $val as $k => $v ){
                        if( $v === 1 ){
                            return true;
                        }
                    }

                    return false;
                }

                if( $type == 'dateselect' || $type == 'datetimeselect' || $type == 'date' ){

                    if ( ! is_array( $val ) ){
                        return false;
                    }

                    foreach( $formelement->_getElements() as $element ){
                        if( ! isset( $val[ $element ] ) ){
                            return false;
                        }
                    }

                    return true;
                }
            }

            // check if element is an array
            if ( is_array( $val ) && sizeof( $val ) == 0 ){
                return false;
            }

            // check if is an empty string
            if ( is_string( $val ) && strlen( $val ) == 0 ) {
                return false;
            }

            // check if is int
            if ( is_int( $val ) && $val == 0 ){
                return false;
            }

            // value is ok
            return true;
        }


        /**
         *	This function returns true if the variable is equal to the specified value, otherwise, it returns false.
         *
         *	@param $val			The value to test.
         *	@param $opts		The required value.
         *	@param $formelement	(not required)
         */
        function value( $val, $opts, $formelement = null ) {
            return $val === $opts;
        }

        /**
         *	This function returns true if the variable is smaller than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val			The value to test.
         *	@param $opts		The maximum length of the variable.
         *	@param $formelement	(not required)
         */
        function maxlength( $val, $opts, $formelement = null ) {
            if ( strlen( $val ) <= intval( $opts ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable is bigger than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val			The value to test.
         *	@param $opts		The minimum length of the variable.
         *	@param $formelement	(not required)
         */
        function minlength( $val, $opts, $formelement = null ) {
            if ( strlen( $val ) >= intval( $opts ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the length of the variable is contained in the indicated range.
         *
         *	@param $val			The value to test.
         *	@param $opts		Array containing the minimum and maximum length.
         *	@param $formelement	(not required)
         */
        function rangelength( $val, $opts, $formelement = null ) {
            if ( ( strlen( $val ) >= intval( $opts[0] ) ) && ( strlen( $val ) <= intval( $opts[1] ) ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the escaped variable is smaller than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val			The value to test.
         *	@param $opts		Array containing the maximum length and the database driver object.
         *	@param $formelement	(not required)
         */
        function maxlength_escape( $val, $opts, $formelement = null ) {
            if ( strlen( $opts[1]->escape( $val ) ) <= intval( $opts[0] ) ) {
                return true;
            } else {
                return false;
            }
        }
        
        /**
         *	This function returns true if the escaped variable is bigger than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val			The value to test.
         *	@param $opts		Array containing the minimum length and the database driver object.
         *	@param $formelement	(not required)
         */
        function minlength_escape( $val, $opts, $formelement = null ) {
            if ( strlen( $opts[1]->escape( $val ) ) >= intval( $opts[0] ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *  This function returns true if the length of the escaped variable is contained in the indicated range.
         *
         *	@param $val			The value to test.
         *	@param $opts		Array containing the minimum length, maximum length and the database driver object.
         *	@param $formelement	(not required)
         */
        function rangelength_escape( $val, $opts, $formelement = null ) {
            if ( ( strlen( $opts[2]->escape( $val ) ) >= intval( $opts[0] ) ) && ( strlen( $opts[2]->escape( $val ) ) <= intval( $opts[1] ) ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable matches the given regular expression (PCRE syntax), otherwise, it
         *	returns false.
         *
         *	@param $val			The value to test.
         *	@param $opts		The regular expression to use (PCRE syntax).
         *	@param $formelement	(not required)
         */
        function regex( $val, $opts, $formelement = null ) {
            if ( preg_match( $opts, $val ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable is a correctly formatted email address.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function email( $val, $opts='', $formelement = null ) {
            return YDValidateRules::regex( $val, '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/' );
        }

        /**
         *  This function returns true if the variable is not a correctly formatted email address.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function not_email( $val, $opts='', $formelement = null ) {
            return ( YDValidateRules::email( $val ) ) ? false : true;
        }

        /**
         *  This function returns true if the variable is a correctly formatted IP address.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function ip( $val, $opts='', $formelement = null ) {
            return YDValidateRules::regex( $val, '/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/' );
        }

        /**
         *	This function returns true if the variable contains only letters.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function lettersonly( $val, $opts='', $formelement = null ) {
            $result = YDValidateRules::regex( $val, '/([\D^ ]+)$/' );
            if ( $result === true ) {
                $result = YDValidateRules::nopunctuation( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable contains only single character.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function character( $val, $opts='', $formelement = null ) {
            return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::lettersonly( $val, array() );
        }

        /**
         *	This function returns true if the variable contains only letters and numbers.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function alphanumeric( $val, $opts='', $formelement = null ) {
            $result = YDValidateRules::regex( strval( $val ), '/([\w^ ]+)$/' );
            if ( $result === true ) {
                $result = YDValidateRules::nopunctuation( strval( $val ), array() ) ? true : false;
            }
            return $result;
        }


        /**
         *	This function returns true if the variable contains only letters and numbers
         *  Strict version - special characters are invalid.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function alphanumericstrict( $val, $opts='', $formelement = null ) {
            return YDValidateRules::regex( strval( $val ), '/^([a-zA-Z0-9]+)$/' );
        }

        /**
         *	This function returns true if the variable contains only numbers.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function numeric( $val, $opts='', $formelement = null ) {
            return YDValidateRules::regex( strval( $val ), '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/' );
        }

        /**
         *	This function returns true if the variable is a md5 string
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function md5( $val, $opts='', $formelement = null ) {
            return YDValidateRules::regex( strval( $val ), '/^([a-z0-9]{32})$/' );
        }

        /**
         *	This function returns true if the variable is an array.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function isarray( $val, $opts='', $formelement = null ) {
            return is_array( $val );
        }

        /**
         *	This function returns true if the variable is boolean.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function isboolean( $val, $opts='', $formelement = null ) {
            return is_bool( $val );
        }

        /**
         *	This function returns true if the variable contains only contains one digit (0-9).
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function digit( $val, $opts='', $formelement = null ) {
            return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::numeric( $val, array() );
        }

        /**
         *	This function returns true if the variable contains no punctuation characters.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function nopunctuation( $val, $opts='', $formelement = null ) {
            return YDValidateRules::regex( strval( $val ), '/^[^().\/\*\^\?#!@$%+=,\"\'><~\[\]{}]+$/' );
        }

        /**
         *	This function returns true if the variable is a number not starting with 0.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         *
         *	@todo
         *		Allows things other than digits
         */
        function nonzero( $val, $opts='', $formelement = null ) {
            $result = YDValidateRules::regex( strval( $val ), '/^-?[1-9][0-9]*/' );
            if ( $result === true ) {
                $result = YDValidateRules::numeric( strval( $val ), array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable is exactly as specified in the options.
         *
         *	@param $val			The value to test.
         *	@param $opts		The value to compare with.
         *	@param $formelement	(not required)
         */
        function exact( $val, $opts, $formelement = null ) {
            return $val === $opts;
        }


        /**
         *	This function returns true if the variable is not exactly as specified in the options.
         *
         *	@param $val			The value to test.
         *	@param $opts		The value to compare with.
         *	@param $formelement	(not required)
         */
        function not( $val, $opts, $formelement = null ) {
            return strval( $val ) != strval( $opts );
        }


        /**
         *	This function returns true if the variable is in the array specified in the options.
         *
         *	@param $val			The value to test.
         *	@param $opts		The array in which the value should be.
         *	@param $formelement	(not required)
         */
        function in_array( $val, $opts, $formelement = null ) {
            return in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is not in the array specified in the options.
         *
         *	@param $val			The value to test.
         *	@param $opts		The array in which the value should not be.
         *	@param $formelement	(not required)
         */
        function not_in_array( $val, $opts, $formelement = null ) {
            return ! in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is in the array specified in the options. This function is
         *  case insensitive.
         *
         *	@param $val			The value to test.
         *	@param $opts		The array in which the value should be.
         *	@param $formelement	(not required)
         */
        function i_in_array( $val, $opts, $formelement = null ) {
            foreach ( $opts as $i=>$j ) {
                if ( is_string( $j ) ) {
                    $opts[$i] = strtolower( $j );
                }
            }
            if ( is_array( $val ) ) {
                foreach ( $val as $i=>$j ) {
                    if ( is_string( $j ) ) {
                        $val[$i] = strtolower( $j );
                    }
                }
            } else {
                $val = strtolower( $val );
            }
            return in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is not in the array specified in the options. This function is
         *  case insensitive.
         *
         *	@param $val			The value to test.
         *	@param $opts		The array in which the value should not be.
         *	@param $formelement	(not required)
         */
        function i_not_in_array( $val, $opts, $formelement = null ) {
            foreach ( $opts as $i=>$j ) {
                if ( is_string( $j ) ) {
                    $opts[$i] = strtolower( $j );
                }
            }
            if ( is_array( $val ) ) {
                foreach ( $val as $i=>$j ) {
                    if ( is_string( $j ) ) {
                        $val[$i] = strtolower( $j );
                    }
                }
            } else {
                $val = strtolower( $val );
            }
            return ! in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is containing less hyperlinks than indicated, otherwise, it
         *	returns false.
         *
         *	@param $val			The value to test.
         *	@param $opts		The maximum amount of hyperlinks that are allowed.
         *	@param $formelement	(not required)
         */
        function maxhyperlinks( $val, $opts, $formelement = null ) {
            $count = intval( $opts );
            $count1 = preg_match_all( "/href=/i", $val, $matches1 );
            $count2 = preg_match_all( "/\[url=/i", $val, $matches2 );
            if ( ( $count1 + $count2 ) > $count ) {
                return false;
            }
            $count3 = preg_match_all( "/http\:\/\//i", $val, $matches );
            if ( ( $count3 ) > $count ) {
                return false;
            }
            return true;
        }

        /**
         *	This rule checks if a string contains the maximum specified words or not.
         *
         *	@param $val			The value to test.
         *	@param $opts		The maximum number of words allowed.
         *	@param $formelement	(not required)
         */
        function maxwords( $val, $opts, $formelement = null ) {
            if ( $opts < sizeof( explode( ' ', trim( $val ) ) ) ) {
                return false;
            }
            return true;
        }

        /**
         *	This rule checks if a string contains the minimum specified words or not.
         *
         *	@param $val			The value to test.
         *	@param $opts		The minimum number of words allowed.
         *	@param $formelement	(not required)
         */
        function minwords( $val, $opts, $formelement = null ) {
            if ( $opts > sizeof( explode( ' ', trim( $val ) ) ) ) {
                return false;
            }
            return true;
        }

        /**
         *	This rule allows to use an external function/method for validation, either by registering it or by passing a
         *	callback as a format parameter.
         *
         *	@param $val			The value to test.
         *	@param $opts		The name of the function to use.
         *	@param $formelement	(not required)
         */
        function callback( $val, $opts, $formelement = null ) {
            return call_user_func( $opts, $val, $type );
        }

        /**
         *	This rule checks if a file was uploaded.
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function uploadedfile( $val, $opts='', $formelement = null ) {
            return is_uploaded_file( $val['tmp_name'] ) && ( filesize( $val['tmp_name'] ) > 0 );
        }

        /**
         *	This rule checks if a file upload exceeded the file size or not.
         *
         *	@param $val			The value to test.
         *	@param $opts		The maximum file size in bytes.
         *	@param $formelement	(not required)
         */
        function maxfilesize( $val, $opts, $formelement = null ) {
            return ( filesize( $val['tmp_name'] ) <= intval( $opts ) );
        }

        /**
         *	This rule checks if a file upload had the right mime type.
         *
         *	@param $val			The value to test.
         *	@param $opts		The required mime type or an array of allowed mime types.
         *	@param $formelement	(not required)
         */
        function mimetype( $val, $opts, $formelement = null ) {
            if ( ! is_array( $opts ) ) {
                $opts = array( $opts );
            }
            return in_array( $val['type'], $opts );
        }

        /**
         *	This rule checks if a file upload had the filename based on a regular expression.
         *
         *	@param $val			The value to test.
         *	@param $opts		The regex to which the filename should match.
         *	@param $formelement	(not required)
         */
        function filename( $val, $opts, $formelement = null ) {
            return YDValidateRules::regex( $val['name'], $opts );
        }

        /**
         *	This rule checks if a file upload has the right file extension (case insensitive). 
         *
         *	@param $val			The value to test.
         *	@param $opts		The file extension it should match (can also be an array of extensions).
         *	@param $formelement	(not required)
         */
        function extension( $val, $opts, $formelement = null ) {
            include_once( YD_DIR_HOME_CLS . '/YDFileSystem.php');
            if ( ! is_array( $opts ) ) {
                $opts = array( $opts );
            }
            $ext = YDPath::getExtension( $val['name'] );
            return YDValidateRules::i_in_array( $ext, $opts );
        }

        /**
         *	This checks if the array with day, month, year, hours, minutes and seconds
         *  elements is valid or not.
         *
         *	@param $val			The value to test.
         *	@param $opts		An array with the options and elements of the date element.
         *	@param $formelement	(not required)
         */
        function date( $val, $opts=array(), $formelement = null ) {
           
            if ( ! is_array( $val ) ) {
                return false;
            }
            
            // If there are no elements defined
            if ( ! isset( $opts['elements'] ) || empty( $opts['elements'] ) ) {
                foreach ( $val as $key => $v ) {
                    $opts['elements'][] = $key;
                }
            }
            
            // If there are no options defined
            if ( ! isset( $opts['options'] ) || empty( $opts['options'] ) ) {
                $opts['options']= array();
            }
            
            // Validate the elements values with the options
            $options  = & $opts['options'];
            $elements = & $opts['elements'];
            
            $year  = false;
            $month = false;
            $day   = false;
            
            // Year
            if ( in_array( 'year', $elements ) ) {
                if ( ! isset( $val['year'] ) || ! is_numeric( $val['year'] ) ) {
                    return false;
                }
                $val['year'] = intval( $val['year'] );
                if ( isset( $options['yearstart'] )
                     && $val['year'] < intval( $options['yearstart'] ) ) {
                    return false;
                }
                if ( isset( $options['yearend'] )
                     && $val['year'] > intval( $options['yearend'] ) ) {
                    return false;
                }
                if ( isset( $options['yearoffset'] )
                     && $val['year'] % intval( $options['yearoffset'] ) != 0 ) {
                    return false;
                }
                $year = true;
            }
            
            // Month
            if ( in_array( 'month', $elements ) ) {
                if ( ! isset( $val['month'] ) || ! is_numeric( $val['month'] ) ) {
                    return false;
                }
                $val['month'] = intval( $val['month'] );
                if ( $val['month'] < 1 || $val['month'] > 12 ) {
                    return false;
                }
                if ( isset( $options['monthstart'] )
                     && $val['month'] < intval( $options['monthstart'] ) ) {
                    return false;
                }
                if ( isset( $options['monthend'] )
                     && $val['month'] > intval( $options['monthend'] ) ) {
                    return false;
                }
                if ( isset( $options['yearoffset'] )
                     && $val['month'] % intval( $options['monthoffset'] ) != 0 ) {
                    return false;
                }
                $month = true;
            }
            
            // Day
            if ( in_array( 'day', $elements ) ) {
                if ( ! isset( $val['day'] ) || ! is_numeric( $val['day'] ) ) {
                    return false;
                }
                $val['day'] = intval( $val['day'] );
                if ( $val['day'] < 1 || $val['day'] > 31 ) {
                    return false;
                }
                if ( isset( $options['daystart'] )
                     && $val['day'] < intval( $options['daystart'] ) ) {
                    return false;
                }
                if ( isset( $options['dayend'] )
                     && $val['day'] > intval( $options['dayend'] ) ) {
                    return false;
                }
                if ( isset( $options['dayoffset'] )
                     && $val['day'] % intval( $options['dayoffset'] ) != 0 ) {
                    return false;
                }
                $day = true;
            }
            
            // Hours
            if ( in_array( 'hours', $elements ) ) {
                if ( ! isset( $val['hours'] ) || ! is_numeric( $val['hours'] ) ) {
                    return false;
                }
                $val['hours'] = intval( $val['hours'] );
                if ( $val['hours'] < 0 || $val['hours'] > 23 ) {
                    return false;
                }
                if ( isset( $options['hoursstart'] )
                     && $val['hours'] < intval( $options['hoursstart'] ) ) {
                    return false;
                }
                if ( isset( $options['hoursend'] )
                     && $val['hours'] > intval( $options['hoursend'] ) ) {
                    return false;
                }
                if ( isset( $options['hoursoffset'] )
                     && $val['hours'] % intval( $options['hoursoffset'] ) != 0 ) {
                    return false;
                }
            }
            
            // Minutes
            if ( in_array( 'minutes', $elements ) ) {
                if ( ! isset( $val['minutes'] ) || ! is_numeric( $val['minutes'] ) ) {
                    return false;
                }
                $val['minutes'] = intval( $val['minutes'] );
                if ( $val['minutes'] < 0 || $val['minutes'] > 59 ) {
                    return false;
                }
                if ( isset( $options['minutesstart'] )
                     && $val['minutes'] < intval( $options['minutesstart'] ) ) {
                    return false;
                }
                if ( isset( $options['minutesend'] )
                     && $val['minutes'] > intval( $options['minutesend'] ) ) {
                    return false;
                }
                if ( isset( $options['minutesoffset'] )
                     && $val['minutes'] % intval( $options['minutesoffset'] ) != 0 ) {
                    return false;
                }
            }
            
            // Seconds
            if ( in_array( 'seconds', $elements ) ) {
                if ( ! isset( $val['seconds'] ) || ! is_numeric( $val['seconds'] ) ) {
                    return false;
                }
                $val['seconds'] = intval( $val['seconds'] );
                if ( $val['seconds'] < 0 || $val['seconds'] > 59 ) {
                    return false;
                }
                if ( isset( $options['secondsstart'] )
                     && $val['seconds'] < intval( $options['secondsstart'] ) ) {
                    return false;
                }
                if ( isset( $options['secondsend'] )
                     && $val['seconds'] > intval( $options['secondsend'] ) ) {
                    return false;
                }
                if ( isset( $options['secondsoffset'] )
                     && $val['seconds'] % intval( $options['secondsoffset'] ) != 0 ) {
                    return false;
                }
            }
            
            // Check date if all elements are set
            if ( $year && $month && $day ) {
                return checkdate( $val['month'], $val['day'], $val['year'] );
            }
            
            // Check days in month (but can't do it for leap years)
            if ( $day && $month ) {
                switch ( $val['month'] ) {
                    case 2:
                        if ( $val['day'] > 29 ) return false;
                    case 4:
                    case 6:
                    case 9:
                    case 11:
                        if ( $val['day'] > 30 ) return false;
                    
                }
            }
            return true;
            
        }

        /**
         *  This checks if the specified text is a valid HTTP url. It should start with http:// and it should have at
         *  least one dot in there.
         *
         *  @param $val     	The value to test.
         *  @param $opts    	(not required)
         *	@param $formelement	(not required)
         */
        function httpurl( $val, $opts=array(), $formelement = null ) {

            // Return true if empty
            if ( empty( $val ) ) {
                return true;
            }

            // Convert to lowercase and trim
            $val = strtolower( trim( $val ) );

            // Check lenght
            if ( strlen( $val ) > 255 ) return false;

            // Add http:// if needed
            if ( substr( $val, 0, 7 ) != 'http://' && substr( $val, 0, 8 ) != 'https://' ) {
                $val = 'http://' . $val;
            }

            // Compute expression
            $expression = '/^(https?:\/\/)' . 
                          '?(([0-9a-z_!~*\'().&=+$%-]+:)?[0-9a-z_!~*\'().&=+$%-]+@)?' . // user@
                          '(([0-9]{1,3}\.){3}[0-9]{1,3}' . // IP
                          '|' . // or domain
                          '([0-9a-z_!~*\'()-]+\.)*' . // tertiary domain(s), eg www.
                          '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.' . // second level domain
                          '[a-z]{2,6}' /*)'*/ . // first level domain, eg com
                          '|' . // or localhost
                          'localhost)' .
                          '(:[0-9]{1,5})?' . // port
                          '((\/?)|' . // a slash isn't required if there is no file name
                          '(\/[0-9a-z_!~*\'().;?:@&=+$,%#-]+)+\/?)$/'; 

             return YDValidateRules::regex( $val, $expression );
        }


        /**
         *	This function returns true if the variable matches captcha image
         *
         *	@param $val			The value to test.
         *	@param $opts		(not required)
         *	@param $formelement	(not required)
         */
        function captcha( $val, $opts='', $formelement = null ) {

            include_once( YD_DIR_HOME . '/3rdparty/captcha/php-captcha.inc.php' );

            return PhpCaptcha::Validate( $val );
        }


        /**
         *	This function returns true if the variable is an array and each element is in opts.
         *
         *	@param $val			The value to test.
         *	@param $opts		The array in which the value should be.
         *	@param $formelement	(not required)
         */
        function valid( $val, $opts='', $formelement = null ) {

            $opts = $formelement->getOptions();

            switch( $formelement->getType() ){
                case 'checkboxgroup' :  foreach( $val as $value => $enable ){
                                            if( ! in_array( $value, $opts ) || $enable != 'on' ){
                                                return false;
                                            }
                                        }
                                        return true;

                case 'select' :         return in_array( $val, $opts );

                case 'country' :        YDInclude( 'YDList.php' );
                                        return in_array( $val, YDList::countries( 'keys' ) );

                case 'timezone' :       YDInclude( 'YDList.php' );
                                        return in_array( $val, YDList::gmts( 'keys' ) );

                default : die( 'YDValidateRule "valid" is not supported in element type ' . $formelement->getType() );
            }
        }


        /**
         *	This function returns true if the variable value is contained in the indicated range.
         *
         *	@param $val			The value to test.
         *	@param $opts		Array containing the minimum and maximum values.
         *	@param $formelement	(not required)
         */
        function rangevalue( $val, $opts, $formelement = null ) {
            if ( ( floatval( $val ) >= floatval( $opts[0] ) ) && ( floatval( $val ) <= floatval( $opts[1] ) ) ) {
                return true;
            } else {
                return false;
            }
        }


        /**
         *	This function returns true if the variable value is a safe html (do not contain possible XSS html code).
         *
         *	@param $val			The value to test.
         *	@param $opts		No options.
         *	@param $formelement	(not required)
         */
        function safe( $val, $opts = array(), $formelement = null ) {
            require_once( YD_DIR_HOME . '/3rdparty/safehtml/classes/safehtml.php' );
            $_safehtml = new safehtml();
            return ( $_safehtml->parse( $val ) === $val );
        }


        /**
         *	This function returns true if the variable value is a supported language code.
         *
         *	@param $val			The value to test.
         *	@param $opts		No options.
         *	@param $formelement	(not required)
         */
        function languagecode( $val, $opts = array(), $formelement = null ){
            return ( is_string( $val ) && isset( $GLOBALS[ 'YD_LANGUAGES' ][ $val ] ) );
        }
    }

?>