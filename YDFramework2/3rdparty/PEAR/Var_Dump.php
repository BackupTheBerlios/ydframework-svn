<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Frederic Poeydomenge <frederic.poeydomenge@free.fr>         |
// +----------------------------------------------------------------------+
//
// $Id:

require_once 'PEAR.php';
require_once 'Var_Dump/Renderer.php';

/**
 * Wrapper for the var_dump function.
 *
 * " The var_dump function displays structured information about expressions
 * that includes its type and value. Arrays are explored recursively
 * with values indented to show structure. "
 *
 * The Var_Dump class captures the output of the var_dump function,
 * by using output control functions, and then uses external renderer
 * classes for displaying the result in various graphical ways :
 * simple text, (X)HTML text, (X)HTML table, XML, ...
 *
 * @package Var_Dump
 * @category PHP
 * @author Frederic Poeydomenge <frederic.poeydomenge@free.fr>
 */

// {{{ constants

define ('VAR_DUMP_START_GROUP',           1);
define ('VAR_DUMP_FINISH_GROUP',          2);
define ('VAR_DUMP_START_ELEMENT_NUM',     3);
define ('VAR_DUMP_START_ELEMENT_STR',     4);
define ('VAR_DUMP_FINISH_ELEMENT',        5);
define ('VAR_DUMP_FINISH_STRING',         6);

define ('VAR_DUMP_TYPE_ARRAY',            1);
define ('VAR_DUMP_TYPE_OBJECT',           2);

define ('VAR_DUMP_PREG_MATCH',            0);
define ('VAR_DUMP_PREG_SPACES',           1);
define ('VAR_DUMP_PREG_KEY_QUOTE',        2);
define ('VAR_DUMP_PREG_KEY',              3);
define ('VAR_DUMP_PREG_STRING_TYPE',      4);
define ('VAR_DUMP_PREG_STRING_VALUE',     5);
define ('VAR_DUMP_PREG_VALUE',            6);
define ('VAR_DUMP_PREG_VALUE_REFERENCE',  7);
define ('VAR_DUMP_PREG_VALUE_TYPE',       8);
define ('VAR_DUMP_PREG_VALUE_COMPL',      9);
define ('VAR_DUMP_PREG_VALUE_RESOURCE',  10);
define ('VAR_DUMP_PREG_ARRAY_END',       11);
define ('VAR_DUMP_PREG_ARRAY_START',     12);
define ('VAR_DUMP_PREG_ARRAY_TYPE',      13);
define ('VAR_DUMP_PREG_STRING_COMPL',    14);

// }}}

class Var_Dump
{

    // {{{ properties

    /**
     * Run-time configuration options.
     * @var array
     * @access public
     */
    var $options = array();

    /**
     * Default configuration options.
     * @var array
     * @access public
     */
    var $defaultOptions = array(
        'display_mode' => 'XHTML_Text'
    );

    /**
     * Rendering configuration options.
     * See Var_Dump/Renderer/*.php for the complete list of options
     * @var array
     * @access public
     */
    var $rendererOptions = array();

    /**
     * Rendering object.
     * @var object
     * @access public
     */
    var $renderer = NULL;

    // }}}

    // {{{ constructor
    /**
     * Class constructor.
		 * The factory approach must be used in relationship with the
		 * toString() method.
     * See Var_Dump/Renderer/*.php for the complete list of options
     * @see Var_Dump::toString()
     * @param array $options         Global parameters.
     * @param array $rendererOptions Parameters for the rendering.
     * @access public
     */
    function Var_Dump($options = array(), $rendererOptions = array())
		{
        $this->options = array_merge (
            $this->defaultOptions,
            $options
        );
        $this->rendererOptions = $rendererOptions;
        $this->renderer = & Var_Dump_Renderer::factory(
            $this->options['display_mode'],
            $this->rendererOptions
        );
    }
    // }}}
	
    // {{{ factory()
    /**
     * Attempt to return a concrete Var_Dump instance.
		 * The factory approach must be used in relationship with the
		 * toString() method.
     * See Var_Dump/Renderer/*.php for the complete list of options
     * @see Var_Dump::toString()
     * @param array $options         Global parameters.
     * @param array $rendererOptions Parameters for the rendering.
     * @access public
     */
    function & factory($options = array(), $rendererOptions = array())
    {
        $obj = & new Var_Dump($options, $rendererOptions);
        return $obj;
    }
    // }}}

    // {{{ toString()
    /**
     * Uses a renderer object to return the string representation of a variable.
     * @param mixed $expression The variable to parse.
     * @return string The string representation of the variable.
     * @access public
     */
    function toString($expression)
    {

        if (PEAR::isError($this->renderer)) {
            return '';
        }

        $family = array(); // element family
        $depth  = array(); // element depth
        $type   = array(); // element type
        $value  = array(); // element value

        // Captures the output of the var_dump function,
        // by using output control functions.
 
        ob_start();
        var_dump($expression);
        $variable = ob_get_contents();
        ob_end_clean();

        // Regexp that parses the output of the var_dump function.
        // The numbers between square brackets [] are the reference
        // of the captured subpattern, and correspond to the entries
        // in the resulting $matches array.
        
        preg_match_all(
            '!^
              (\s*)                           # 2 spaces for each depth level
              (?:                             #
                (?:\[("?)(.*?)\\2\]=>)        # Key [2-3]
                  |                           #   or
                (?:(&?string\(\d+\))\s+"(.*)) # String [4-5]
                  |                           #   or
                (                             # Value [6-10]
                  (&?)                        #   - reference [7]
                  (bool|int|float|resource|   #   - type [8]
                  NULL|\*RECURSION\*)         #
									(?:\((.*?)\))?              #   - complement [9]
                  (?:\sof\stype\s\((.*?)\))?  #   - resource [10]
                )                             #
                  |                           #   or
                (})                           # End of array/object [11]
                  |                           #   or
                (?:(&?(array|object).*)\ {)   # Start of array/object [12-13]
                  |                           #   or
                (.*)                          # String (additional lines) [14]
              )                               #
            $!Smx',
            $variable,
            $matches,
            PREG_SET_ORDER
        );

        // Used to keep the maxLen of the keys for each nested variable.

        $stackLen = array();
        $keyLen = array();
        $maxLen = 0;

        // Used when matching a string, to count the remaining
        // number of chars before the end of the string.

        $countdown = 0;

        // Used to keep a pointer (reference) on every
        // variable parsed through the following loop.

        $reference = & $expression;
        $stackReference = array();
        $arrReference = array(
            VAR_DUMP_PREG_STRING_TYPE,
            VAR_DUMP_PREG_STRING_VALUE,
            VAR_DUMP_PREG_ARRAY_START,
            VAR_DUMP_PREG_ARRAY_TYPE
        );

        // Loop through the matches of the previously defined regexp.

        foreach($matches AS $match) {

            $count = count($match) - 1;

            // If we are waiting for additional lines of a string, decrease
            // the countdown by the len of the match + 1 (\n),
            // and skip to next loop.

						if ($countdown > 0) {
                $countdown -= strlen($match[VAR_DUMP_PREG_MATCH]) + 1;
								continue;
						}

            // If matched a string or the beginning of an array/object,
            // initialize a reference on the variable (=$obj).
            
            if (in_array($count, $arrReference)) {
                if (empty($stackReference)) {
                    $obj = $reference;
                } else {
                    list($vtyp, $vkey) = end($stackReference);
                    switch ($vtyp) {
                        case VAR_DUMP_TYPE_ARRAY:
                            $obj = & $vkey[$reference];
                            break;
                        case VAR_DUMP_TYPE_OBJECT:
                            $obj = & $vkey->$reference;
                            break;
                    }
                }
            }
            
            // Find which alternative has been matched in the regexp,
            // by looking at the number of elements in the $match array.

            switch ($count) {

                // Key
                //=====
                // - Compute the maxLen of the keys at the actual depth
                // - Store the key name in $reference

                case VAR_DUMP_PREG_KEY:
                    $len = strlen($match[VAR_DUMP_PREG_KEY]);
                    if ($len > $maxLen) {
                        $maxLen = $len;
                    }
                    if (empty($match[VAR_DUMP_PREG_KEY_QUOTE])) {
                        $family[] = VAR_DUMP_START_ELEMENT_NUM;
                        $reference = (integer) $match[VAR_DUMP_PREG_KEY];
                    } else {
                        $family[] = VAR_DUMP_START_ELEMENT_STR;
                        $reference = (string) $match[VAR_DUMP_PREG_KEY];
                    }
                    $depth[] = strlen($match[VAR_DUMP_PREG_SPACES]) >> 1;
                    $type[]  = NULL;
                    $value[] = $match[VAR_DUMP_PREG_KEY];
                    break;

                // String
                //========
                // - Set the countdown (remaining number of chars before eol) =
                //   len of the string - matched len + 1 (final ")

                case VAR_DUMP_PREG_STRING_TYPE:
                case VAR_DUMP_PREG_STRING_VALUE:
                    $countdown =
                        strlen($obj)
                        - strlen($match[VAR_DUMP_PREG_STRING_VALUE])
                        + 1;
                    $family[] = VAR_DUMP_FINISH_STRING;
                    $depth[] = strlen($match[VAR_DUMP_PREG_SPACES]) >> 1;
                    $type[] = $match[VAR_DUMP_PREG_STRING_TYPE];
                    $value[] = $obj;
                    break;

                // Value
                //=======
                
                case VAR_DUMP_PREG_VALUE:
                case VAR_DUMP_PREG_VALUE_REFERENCE:
                case VAR_DUMP_PREG_VALUE_TYPE:
                case VAR_DUMP_PREG_VALUE_COMPL:
                case VAR_DUMP_PREG_VALUE_RESOURCE:
                    $family[] = VAR_DUMP_FINISH_ELEMENT;
                    $depth[] = strlen($match[VAR_DUMP_PREG_SPACES]) >> 1;
                    switch ($match[VAR_DUMP_PREG_VALUE_TYPE]) {
                        case 'bool':
                        case 'int':
                        case 'float':
                            $type[] =
                                $match[VAR_DUMP_PREG_VALUE_REFERENCE] .
                                $match[VAR_DUMP_PREG_VALUE_TYPE];
                            $value[] = $match[VAR_DUMP_PREG_VALUE_COMPL];
                            break;
                        case 'resource':
                            $type[] =
                                $match[VAR_DUMP_PREG_VALUE_REFERENCE] .
                                $match[VAR_DUMP_PREG_VALUE_TYPE] .
                                '(' . $match[VAR_DUMP_PREG_VALUE_RESOURCE] . ')';
                            $value[] = $match[VAR_DUMP_PREG_VALUE_COMPL];
                            break;
                        default:
                            $type[] =
                                $match[VAR_DUMP_PREG_VALUE_REFERENCE] .
                                $match[VAR_DUMP_PREG_VALUE_TYPE];
                            $value[] = NULL;
                            break;
                    }
                    break;

                // End of array
                //==============
                // - Pop the maxLen of the keys off the end of the stack
                // - Pop the reference on the variable off the end of the stack
                
                case VAR_DUMP_PREG_ARRAY_END:
                    $oldLen = array_pop($stackLen);
                    $keyLen[$oldLen[0]] = $maxLen;
                    $maxLen = $oldLen[1];
                    list($vtyp, $vkey) = array_pop($stackReference);
                    $family[] = VAR_DUMP_FINISH_GROUP;
                    $depth[] = strlen($match[VAR_DUMP_PREG_SPACES]) >> 1;
                    $type[] = NULL;
                    $value[] = $match[VAR_DUMP_PREG_ARRAY_END];
                    break;

                // Start of array
                //================
                // - Push the maxLen of the keys onto the end of the stack
                // - Initialize new maxLen to 0
                // - Push the reference on the variable onto the end of the stack

                case VAR_DUMP_PREG_ARRAY_START:
                case VAR_DUMP_PREG_ARRAY_TYPE:
                    array_push($stackLen, array(count($family), $maxLen));
                    $maxLen = 0;
                    switch ($match[VAR_DUMP_PREG_ARRAY_TYPE]) {
                        case 'array':
                            array_push(
                                $stackReference,
                                array(VAR_DUMP_TYPE_ARRAY, $obj)
                            );
                            break;
                        case 'object':
                            array_push(
                                $stackReference,
                                array(VAR_DUMP_TYPE_OBJECT, $obj)
                            );
                            break;
                    }
                    $family[] = VAR_DUMP_START_GROUP;
                    $depth[] = strlen($match[VAR_DUMP_PREG_SPACES]) >> 1;
                    $type[] = NULL;
                    $value[] = $match[VAR_DUMP_PREG_ARRAY_START];
                    break;
                    
            } // switch ($count)

        }

        $this->renderer->initialize($family, $depth, $type, $value, $keyLen);
				
        return $this->renderer->toString();

    }
    // }}}

    // {{{ singleton()
    /**
     * Attempt to return a concrete singleton Var_Dump instance.
		 * The singleton approach must be used in relationship with the
		 * displayInit() and display() methods.
     * See Var_Dump/Renderer/*.php for the complete list of options
     * @see Var_Dump::display(), Var_Dump::displayInit()
     * @return object Var_Dump instance
     * @access public
     */
    function & singleton()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Var_Dump(array(), array());
        }
        return $instance;
    }
    // }}}

    // {{{ displayInit()
    /**
     * Initialise the singleton object used by the display() method.
     * @see Var_Dump::singleton(), Var_Dump::display()
     * @param array $options         Global parameters.
     * @param array $rendererOptions Parameters for the rendering.
     * @access public
     */
    function displayInit($options = array(), $rendererOptions = array())
    {
        $displayInit = & Var_Dump::singleton();
				$displayInit->Var_Dump($options, $rendererOptions);
    }
    // }}}

    // {{{ display()
    /**
     * Outputs or returns a string representation of a variable.
     * @see Var_Dump::singleton(), Var_Dump::displayInit()
     * @param mixed $expression The variable to parse.
     * @param bool  $return     Whether the variable should be echoed or returned.
     * @return string If returned, the string representation of the variable.
     * @access public
     */
    function display($expression, $return=FALSE)
    {
        $display = & Var_Dump::singleton();
        if ($return) {
            return $display->toString($expression);
        } else {
            echo $display->toString($expression);
        }
    }
    // }}}

}

?>