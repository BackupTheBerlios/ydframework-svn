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

    // Check if the framework is loaded
    if ( ! defined( 'YD_FW_NAME' ) ) {
        die( 'Yellow Duck Framework is not loaded.' );
    }

    /**
     *	This class contains abstract functions implementing the validation rules.
     */
    class YDValidateRules extends YDBase {

        /**
         *	This function returns false if the variable is empty, otherwise, it returns true.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function required( $val, $opts='' ) {
            if ( is_null( $val ) ) {
                return false;
            } elseif ( is_array( $val ) && sizeof( $val ) == 0 ) {
                return false;
            } elseif ( is_string( $val ) && strlen( $val ) == 0 ) {
                return false;
            } else {
                return true;
            }
        }

        /**
         *	This function returns true if the variable is equal to the specified value, otherwise, it returns false.
         *
         *	@param $val		The value to test.
         *	@param $opts	The required value.
         */
        function value( $val, $opts ) {
            return $val === $opts;
        }

        /**
         *	This function returns true if the variable is smaller than the specified length, otherwise, it returns
         *	false.
         *
         *	@param $val		The value to test.
         *	@param $opts	The maximum length of the variable.
         */
        function maxlength( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	The minimum length of the variable.
         */
        function minlength( $val, $opts ) {
            if ( strlen( $val ) >= intval( $opts ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the length of the variable is contained in the indicated range.
         *
         *	@param $val		The value to test.
         *	@param $opts	Array containing the minimum and maximum length.
         */
        function rangelength( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	Array containing the maximum length and the database driver object.
         */
        function maxlength_escape( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	Array containing the minimum length and the database driver object.
         */
        function minlength_escape( $val, $opts ) {
            if ( strlen( $opts[1]->escape( $val ) ) >= intval( $opts[0] ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *  This function returns true if the length of the escaped variable is contained in the indicated range.
         *
         *	@param $val		The value to test.
         *	@param $opts	Array containing the minimum length, maximum length and the database driver object.
         */
        function rangelength_escape( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	The regular expression to use (PCRE syntax).
         */
        function regex( $val, $opts ) {
            if ( preg_match( $opts, $val ) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	This function returns true if the variable is a correctly formatted email address.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function email( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/' );
        }

        /**
         *  This function returns true if the variable is not a correctly formatted email address.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function not_email( $val, $opts='' ) {
            return ( YDValidateRules::email( $val ) ) ? false : true;
        }

        /**
         *  This function returns true if the variable is a correctly formatted IP address.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function ip( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/' );
        }

        /**
         *	This function returns true if the variable contains only letters.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function lettersonly( $val, $opts='' ) {
            $result = YDValidateRules::regex( $val, '/([\D^ ]+)$/' );
            if ( $result === true ) {
                $result = YDValidateRules::nopunctuation( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable contains only single character.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function character( $val, $opts='' ) {
            return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::lettersonly( $val, array() );
        }

        /**
         *	This function returns true if the variable contains only letters and numbers.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function alphanumeric( $val, $opts='' ) {
            $result = YDValidateRules::regex( $val, '/([\w^ ]+)$/' );
            if ( $result === true ) {
                $result = YDValidateRules::nopunctuation( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable contains only numbers.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function numeric( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/' );
        }

        /**
         *	This function returns true if the variable contains only contains one digit (0-9).
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function digit( $val, $opts='' ) {
            return ( strlen( strval( $val ) ) == 1 ) && YDValidateRules::numeric( $val, array() );
        }

        /**
         *	This function returns true if the variable contains no punctuation characters.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function nopunctuation( $val, $opts='' ) {
            return YDValidateRules::regex( $val, '/^[^().\/\*\^\?#!@$%+=,\"\'><~\[\]{}]+$/' );
        }

        /**
         *	This function returns true if the variable is a number not starting with 0.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         *
         *	@todo
         *		Allows things other than digits
         */
        function nonzero( $val, $opts='' ) {
            $result = YDValidateRules::regex( $val, '/^-?[1-9][0-9]*/' );
            if ( $result === true ) {
                $result = YDValidateRules::numeric( $val, array() ) ? true : false;
            }
            return $result;
        }

        /**
         *	This function returns true if the variable is exactly as specified in the options.
         *
         *	@param $val		The value to test.
         *	@param $opts	The value to compare with.
         */
        function exact( $val, $opts ) {
            return $val === $opts;
        }

        /**
         *	This function returns true if the variable is in the array specified in the options.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should be.
         */
        function in_array( $val, $opts ) {
            return in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is not in the array specified in the options.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should not be.
         */
        function not_in_array( $val, $opts ) {
            return ! in_array( $val, $opts, true );
        }

        /**
         *	This function returns true if the variable is in the array specified in the options. This function is
         *  case insensitive.
         *
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should be.
         */
        function i_in_array( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	The array in which the value should not be.
         */
        function i_not_in_array( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	The maximum amount of hyperlinks that are allowed.
         */
        function maxhyperlinks( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	The maximum number of words allowed.
         */
        function maxwords( $val, $opts ) {
            if ( $opts < sizeof( explode( ' ', trim( $val ) ) ) ) {
                return false;
            }
            return true;
        }

        /**
         *	This rule checks if a string contains the minimum specified words or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	The minimum number of words allowed.
         */
        function minwords( $val, $opts ) {
            if ( $opts > sizeof( explode( ' ', trim( $val ) ) ) ) {
                return false;
            }
            return true;
        }

        /**
         *	This rule allows to use an external function/method for validation, either by registering it or by passing a
         *	callback as a format parameter.
         *
         *	@param $val		The value to test.
         *	@param $opts	The name of the function to use.
         */
        function callback( $val, $opts ) {
            return call_user_func( $opts, $val );
        }

        /**
         *	This rule checks if a file was uploaded.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function uploadedfile( $val, $opts='' ) {
            return is_uploaded_file( $val['tmp_name'] ) && ( filesize( $val['tmp_name'] ) > 0 );
        }

        /**
         *	This rule checks if a file upload exceeded the file size or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	The maximum file size in bytes.
         */
        function maxfilesize( $val, $opts ) {
            return ( filesize( $val['tmp_name'] ) <= intval( $opts ) );
        }

        /**
         *	This rule checks if a file upload had the right mime type.
         *
         *	@param $val		The value to test.
         *	@param $opts	The required mime type or an array of allowed mime types.
         */
        function mimetype( $val, $opts ) {
            if ( ! is_array( $opts ) ) {
                $opts = array( $opts );
            }
            return in_array( $val['type'], $opts );
        }

        /**
         *	This rule checks if a file upload had the filename based on a regular expression.
         *
         *	@param $val		The value to test.
         *	@param $opts	The regex to which the filename should match.
         */
        function filename( $val, $opts ) {
            return YDValidateRules::regex( $val['name'], $opts );
        }

        /**
         *	This rule checks if a file upload has the right file extension (case insensitive). 
         *
         *	@param $val		The value to test.
         *	@param $opts	The file extension it should match (can also be an array of extensions).
         */
        function extension( $val, $opts ) {
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
         *	@param $val		The value to test.
         *	@param $opts	An array with the options and elements of the date element.
         */
        function date( $val, $opts=array() ) {
           
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
         *	This checks if the array with hours, minutes and seconds elements is a valid time or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         *
         *  @deprecated    The date rule should be used instead.
         */
        function time( $val, $opts=array() ) {
            return YDValidateRules::date( $val, $opts );
        }

        /**
         *	This checks if the array with hours and minutes, days, months and years elements is a valid datetime or not.
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         *
         *  @deprecated    The date rule should be used instead.
         */
        function datetime( $val, $opts=array() ) {
            return YDValidateRules::date( $val, $opts );
        }

        /**
         *  This checks if the specified text is a valid HTTP url. It should start with http:// and it should have at
         *  least one dot in there.
         *
         *  @param $val     The value to test.
         *  @param $opts    (not required)
         */
        function httpurl( $val, $opts=array() ) {

            // Return true if empty
            if ( empty( $val ) ) {
                return true;
            }

            // Convert to lowercase and trim
            $val = strtolower( trim( $val ) );

            // Add http:// if needed
            if ( substr( $val, 0, 7 ) != 'http://' ) {
                $val = 'http://' . $val;
            }

            // Check if it starts with http://
            if ( ! YDStringUtil::startsWith( $val, 'http://' ) ) {
                return false;
            }

            // Check the hostname
            $host = substr( $val, 7 );
            if ( strpos( $host, '/' ) !== false ) {
                $host = trim( substr( $host, 0, strpos( $host, '/' ) ) );
            }
            if ( strpos( $host, ':' ) !== false ) {
                $host = trim( substr( $host, 0, strpos( $host, ':' ) ) );
            }

            // Localhost is allowed
            if ( $host == 'localhost' ) {
                return true;
            }

            // Check that we have at least a dot
            return ( strpos( $host, '.' ) === false ) ? false : true;

        }

        /**
         *	This function returns true if the variable matches captcha image
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function captcha( $val, $opts='' ) {

            include_once( YD_DIR_HOME . '/3rdparty/captcha/php-captcha.inc.php' );

            return PhpCaptcha::Validate( $val );
        }


        /**
         *	This function returns true if the variable matches a valid gmt value
         *
         *	@param $val		The value to test.
         *	@param $opts	(not required)
         */
        function timezone( $val, $opts='' ) {

			YDInclude( 'YDUtil.php' );

			$arr = YDArrayUtil::getGMT();

            return isset( $arr[ $val ] );
        }


    }

?>