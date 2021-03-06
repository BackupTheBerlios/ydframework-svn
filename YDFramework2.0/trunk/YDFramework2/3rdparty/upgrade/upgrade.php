<?php
/**
 * api:		php
 * title:	WentPHP5 / upgrade.php
 * description:	Emulates functions from new PHP versions on older interpreters.
 * version:	15
 * license:	Public Domain
 * url:		http://freshmeat.net/p/upgradephp
 * type:	functions
 * category:	library
 * priority:	auto
 * sort:	-255
 * provides:	upgrade-php, api:php5
 *
 *
 * By loading this library you get PHP version independence. It provides
 * downwards compatibility to older PHP interpreters by emulating missing
 * functions or constants using IDENTICAL NAMES. So this doesn't slow down
 * script execution on setups where the native functions already exist. It
 * is meant as quick drop-in solution. It spares you from rewriting code or
 * using cumbersome workarounds, instead of the more powerful v5 functions.
 * 
 * It cannot mirror PHP5s extended OO-semantics and functionality into PHP4
 * however. A few features are added here that weren't part of PHP yet. And
 * some other function collections are separated out into the ext/ directory.
 * It doesn't produce many custom error messages (YAGNI), and instead leaves
 * reporting to invoked functions or for execution on native PHP.
 * 
 * And further this is PUBLIC DOMAIN (no copyright, no license, no warranty)
 * so therefore compatible to ALL open source licenses. You could rip this
 * paragraph out to republish this instead only under more restrictive terms
 * or your favorite license (GNU LGPL/GPL, BSDL, MPL/CDDL, Artistic/PHPL, ..)
 *
 * Any contribution is appreciated. <milky*users#sf#net>
 *
 */



/**
 *                                   ------------------------------ CVS ---
 * @group CVS
 * @since CVS
 *
 * planned, but as of yet unimplemented functions
 * - some of these might appear in 6.0 or ParrotPHP
 *
 * @emulated
 *    sys_get_temp_dir
 *
 */



/**
 * returns path of the system directory for temporary files
 *
 */
if (!function_exists("sys_get_temp_dir")) {
   function sys_get_temp_dir() {
      # check possible alternatives
      ($temp = ini_get("temp_dir"))
      or
      ($temp = $_SERVER["TEMP"])
      or
      ($temp = $_SERVER["TMP"])
      or
      ($temp = "/tmp");
      # fin
      return($temp);
   }
}





/**
 *                                   ------------------------------ 5.2 ---
 * @group 5_2
 * @since 5.2
 *
 * Additions of PHP 5.2.0
 * - some listed here might have appeared earlier or in release candidates
 *
 * @emulated
 *    json_encode
 *    json_decode
 *    error_get_last
 *    preg_last_error
 *    lchown
 *    lchgrp
 *    E_RECOVERABLE_ERROR
 *    M_SQRTPI
 *    M_LNPI
 *    M_EULER
 *    M_SQRT3
 *
 * @missing
 *    sys_getloadavg
 *    inet_ntop
 *    inet_pton
 *    array_fill_keys
 *    array_intersect_key
 *    array_intersect_ukey
 *    array_diff_key
 *    array_diff_ukey
 *    array_product
 *    pdo_drivers
 *    ftp_ssl_connect
 *    XmlReader
 *    XmlWriter
 *    PDO*
 *
 * @unimplementable
 *    stream_*
 *
 */



/**
 * @since unknown
 */
if (!defined("E_RECOVERABLE_ERROR")) { define("E_RECOVERABLE_ERROR", 4096); }



/**
 * Converts PHP variable or array into a "JSON" (JavaScript value expression
 * or "object notation") string.
 *
 * @compat
 *    Output seems identical to PECL versions. "Only" 20x slower than PECL version.
 * @bugs
 *    Doesn't take care with unicode too much - leaves UTF-8 sequences alone.
 *
 * @param  $var mixed  PHP variable/array/object
 * @return string      transformed into JSON equivalent
 */
if (!function_exists("json_encode")) {
   function json_encode($var, /*emu_args*/$obj=FALSE) {
   
      #-- prepare JSON string
      $json = "";
      
      #-- add array entries
      if (is_array($var) || ($obj=is_object($var))) {

         #-- check if array is associative
         if (!$obj) foreach ((array)$var as $i=>$v) {
            if (!is_int($i)) {
               $obj = 1;
               break;
            }
         }

         #-- concat invidual entries
         foreach ((array)$var as $i=>$v) {
            $json .= ($json ? "," : "")    // comma separators
                   . ($obj ? ("\"$i\":") : "")   // assoc prefix
                   . (json_encode($v));    // value
         }

         #-- enclose into braces or brackets
         $json = $obj ? "{".$json."}" : "[".$json."]";
      }

      #-- strings need some care
      elseif (is_string($var)) {
         if (!utf8_decode($var)) {
            $var = utf8_encode($var);
         }
         $var = str_replace(array("\"", "\\", "/", "\b", "\f", "\n", "\r", "\t"), array("\\\"", "\\\\", "\\/", "\\b", "\\f", "\\n", "\\r", "\\t"), $var);
         $json = '"' . $var . '"';
         //@COMPAT: for fully-fully-compliance   $var = preg_replace("/[\000-\037]/", "", $var);
      }

      #-- basic types
      elseif (is_bool($var)) {
         $json = $var ? "true" : "false";
      }
      elseif ($var === NULL) {
         $json = "null";
      }
      elseif (is_int($var) || is_float($var)) {
         $json = "$var";
      }

      #-- something went wrong
      else {
         trigger_error("json_encode: don't know what a '" .gettype($var). "' is.", E_USER_ERROR);
      }
      
      #-- done
      return($json);
   }
}



/**
 * Parses a JSON (JavaScript value expression) string into a PHP variable
 * (array or object).
 *
 * @compat
 *    Behaves similar to PECL version, but is less quiet on errors.
 *    Now even decodes unicode \uXXXX string escapes into UTF-8.
 *    "Only" 27 times slower than native function.
 * @bugs
 *    Might parse some misformed representations, when other implementations
 *    would scream error or explode.
 * @code
 *    This is state machine spaghetti code. Needs the extranous parameters to
 *    process subarrays, etc. When it recursively calls itself, $n is the
 *    current position, and $waitfor a string with possible end-tokens.
 *
 * @param   $json string   JSON encoded values
 * @param   $assoc bool    (optional) if outer shell should be decoded as object always
 * @return  mixed          parsed into PHP variable/array/object
 */
if (!function_exists("json_decode")) {
   function json_decode($json, $assoc=FALSE, /*emu_args*/$n=0,$state=0,$waitfor=0) {

      #-- result var
      $val = NULL;
      static $lang_eq = array("true" => TRUE, "false" => FALSE, "null" => NULL);
      static $str_eq = array("n"=>"\012", "r"=>"\015", "\\"=>"\\", '"'=>'"', "f"=>"\f", "b"=>"\b", "t"=>"\t", "/"=>"/");

      #-- flat char-wise parsing
      for (/*n*/; $n<strlen($json); /*n*/) {
         $c = $json[$n];

         #-= in-string
         if ($state==='"') {

            if ($c == '\\') {
               $c = $json[++$n];
               // simple C escapes
               if (isset($str_eq[$c])) {
                  $val .= $str_eq[$c];
               }

               // here we transform \uXXXX Unicode (always 4 nibbles) references to UTF-8
               elseif ($c == "u") {
                  // read just 16bit (therefore value can't be negative)
                  $hex = hexdec( substr($json, $n+1, 4) );
                  $n += 4;
                  // Unicode ranges
                  if ($hex < 0x80) {    // plain ASCII character
                     $val .= chr($hex);
                  }
                  elseif ($hex < 0x800) {   // 110xxxxx 10xxxxxx 
                     $val .= chr(0xC0 + $hex>>6) . chr(0x80 + $hex&63);
                  }
                  elseif ($hex <= 0xFFFF) { // 1110xxxx 10xxxxxx 10xxxxxx 
                     $val .= chr(0xE0 + $hex>>12) . chr(0x80 + ($hex>>6)&63) . chr(0x80 + $hex&63);
                  }
                  // other ranges, like 0x1FFFFF=0xF0, 0x3FFFFFF=0xF8 and 0x7FFFFFFF=0xFC do not apply
               }

               // no escape, just a redundant backslash
               //@COMPAT: we could throw an exception here
               else {
                  $val .= "\\" . $c;
               }
            }

            // end of string
            elseif ($c == '"') {
               $state = 0;
            }

            // yeeha! a single character found!!!!1!
            else/*if (ord($c) >= 32)*/ { //@COMPAT: specialchars check - but native json doesn't do it?
               $val .= $c;
            }
         }

         #-> end of sub-call (array/object)
         elseif ($waitfor && (strpos($waitfor, $c) !== false)) {
            return array($val, $n);  // return current value and state
         }
         
         #-= in-array
         elseif ($state===']') {
            list($v, $n) = json_decode($json, 0, $n, 0, ",]");
            $val[] = $v;
            if ($json[$n] == "]") { return array($val, $n); }
         }

         #-= in-object
         elseif ($state==='}') {
            list($i, $n) = json_decode($json, 0, $n, 0, ":");   // this allowed non-string indicies
            list($v, $n) = json_decode($json, 0, $n+1, 0, ",}");
            $val[$i] = $v;
            if ($json[$n] == "}") { return array($val, $n); }
         }

         #-- looking for next item (0)
         else {
         
            #-> whitespace
            if (preg_match("/\s/", $c)) {
               // skip
            }

            #-> string begin
            elseif ($c == '"') {
               $state = '"';
            }

            #-> object
            elseif ($c == "{") {
               list($val, $n) = json_decode($json, $assoc, $n+1, '}', "}");
               if ($val && $n && !$assoc) {
                  $obj = new stdClass();
                  foreach ($val as $i=>$v) {
                     $obj->{$i} = $v;
                  }
                  $val = $obj;
                  unset($obj);
               }
            }
            #-> array
            elseif ($c == "[") {
               list($val, $n) = json_decode($json, $assoc, $n+1, ']', "]");
            }

            #-> comment
            elseif (($c == "/") && ($json[$n+1]=="*")) {
               // just find end, skip over
               ($n = strpos($json, "*/", $n+1)) or ($n = strlen($json));
            }

            #-> numbers
            elseif (preg_match("#^(-?\d+(?:\.\d+)?)(?:[eE]([-+]?\d+))?#", substr($json, $n), $uu)) {
               $val = $uu[1];
               $n += strlen($uu[0]) - 1;
               if (strpos($val, ".")) {  // float
                  $val = (float)$val;
               }
               elseif ($val[0] == "0") {  // oct
                  $val = octdec($val);
               }
               else {
                  $val = (int)$val;
               }
               // exponent?
               if (isset($uu[2])) {
                  $val *= pow(10, (int)$uu[2]);
               }
            }

            #-> boolean or null
            elseif (preg_match("#^(true|false|null)\b#", substr($json, $n), $uu)) {
               $val = $lang_eq[$uu[1]];
               $n += strlen($uu[1]) - 1;
            }

            #-- parsing error
            else {
               // PHPs native json_decode() breaks here usually and QUIETLY
              trigger_error("json_decode: error parsing '$c' at position $n", E_USER_WARNING);
               return $waitfor ? array(NULL, 1<<30) : NULL;
            }

         }//state
         
         #-- next char
         if ($n === NULL) { return NULL; }
         $n++;
      }//for

      #-- final result
      return ($val);
   }
}




/**
 * @stub
 * @cannot-reimplement
 *
 */
if (!function_exists("preg_last_error")) {
   if (!defined("PREG_NO_ERROR")) { define("PREG_NO_ERROR", 0); }
   if (!defined("PREG_INTERNAL_ERROR")) { define("PREG_INTERNAL_ERROR", 1); }
   if (!defined("PREG_BACKTRACK_LIMIT_ERROR")) { define("PREG_BACKTRACK_LIMIT_ERROR", 2); }
   if (!defined("PREG_RECURSION_LIMIT_ERROR")) { define("PREG_RECURSION_LIMIT_ERROR", 3); }
   if (!defined("PREG_BAD_UTF8_ERROR")) { define("PREG_BAD_UTF8_ERROR", 4); }
   function preg_last_error() {
      return PREG_NO_ERROR;
   }
}




/**
 * @stub
 *
 * Returns associative array with last error message.
 *
 */
if (!function_exists("error_get_last")) {
   function error_get_last() {
      return array(
         "type" => 0,
         "message" => $GLOBALS[php_errormsg],
         "file" => "unknonw",
         "line" => 0,
      );
   }
}




/**
 * @flag quirky, exec
 * @realmode
 *
 * Change owner of a symlink filename.
 *
 */
if (!function_exists("lchown")) {
   function lchown($fn, $user) {
      if (PHP_OS != "Linux") {
         return false;
      }
      $user = escapeshellcmd($user);
      $fn = escapeshellcmd($fn);
      exec("chown -h '$user' '$fn'", $uu, $state);
      return($state);
   }
}



/**
 * @flag quirky, exec
 * @realmode
 *
 * Change group of a symlink filename.
 *
 */
if (!function_exists("lchgrp")) {
   function lchgrp($fn, $group) {
      return lchown($fn, ":$group");
   }
}














/**
 *                                   ------------------------------ 5.1 ---
 * @group 5_1
 * @since 5.1
 *
 * Additions in PHP 5.1
 * - most functions here appeared in -rc1 already
 * - and were backported to 4.4 series?
 *
 * @emulated
 *    property_exists
 *    time_sleep_until
 *    fputcsv
 *    strptime
 *    ENT_COMPAT
 *    ENT_QUOTES
 *    ENT_NOQUOTES
 *    htmlspecialchars_decode
 *    PHP_INT_SIZE
 *    PHP_INT_MAX
 *    M_SQRTPI
 *    M_LNPI
 *    M_EULER
 *    M_SQRT3
 *
 * @missing
 *    strptime
 *
 * @unimplementable
 *    ...
 *
 */



/**
 * Constants for future 64-bit integer support.
 *
 */
if (!defined("PHP_INT_SIZE")) { define("PHP_INT_SIZE", 4); }
if (!defined("PHP_INT_MAX")) { define("PHP_INT_MAX", 2147483647); }



/**
 * @flag bugfix
 * @see #33895
 *
 * Missing constants in 5.1, originally appeared in 4.0.
 */
if (!defined("M_SQRTPI")) { define("M_SQRTPI", 1.7724538509055); }
if (!defined("M_LNPI")) { define("M_LNPI", 1.1447298858494); }
if (!defined("M_EULER")) { define("M_EULER", 0.57721566490153); }
if (!defined("M_SQRT3")) { define("M_SQRT3", 1.7320508075689); }




/**
 * removes entities &lt; &gt; &amp; and eventually &quot; from HTML string
 *
 */
if (!function_exists("htmlspecialchars_decode")) {
   if (!defined("ENT_COMPAT")) { define("ENT_COMPAT", 2); }
   if (!defined("ENT_QUOTES")) { define("ENT_QUOTES", 3); }
   if (!defined("ENT_NOQUOTES")) { define("ENT_NOQUOTES", 0); }
   function htmlspecialchars_decode($string, $quotes=2) {
      $d = $quotes & ENT_COMPAT;
      $s = $quotes & ENT_QUOTES;
      return str_replace(
         array("&lt;", "&gt;", ($s ? "&quot;" : "&.-;"), ($d ? "&#039;" : "&.-;"), "&amp;"),
         array("<",    ">",    "'",                      "\"",                     "&"),
         $string
      );
   }
}



/**
 * @flag needs5
 *
 * Checks for existence of object property, should return TRUE even for NULL values.
 *
 * @compat
 *    no test for edge cases
 */
if (!function_exists("property_exists")) {
   function property_exists($obj, $propname) {
      if (is_object($obj)) {
         $props = get_object_vars($obj);
      }
      elseif (class_exists($obj)) {
         $props = get_class_vars($obj);
      }
      if (!@$props) {
         return false;
      }
      return @array_walk($props, "strtolower") && in_array(strtolower($propname), $props);
   }
}



/**
 * halt execution, until given timestamp
 *
 */
if (!function_exists("time_sleep_until")) {
   function time_sleep_until($t) {
      $delay = $t - time();
      if ($delay < 0) {
         trigger_error("time_sleep_until: timestamp in the past", E_USER_WARNING);
         return false;
      }
      else {
         sleep((int)$delay);
         usleep(($delay - floor($delay)) * 1000000);
         return true;
      }
   }
}



/**
 * @untested
 *
 * Writes an array as CSV text line into opened filehandle.
 *
 */
if (!function_exists("fputcsv")) {
   function fputcsv($fp, $fields, $delim=",", $encl='"') {
      $line = "";
      foreach ((array)$fields as $str) {
         $line .= ($line ? $delim : "")
                . $encl
                . str_replace(array('\\', $encl), array('\\\\'. '\\'.$encl), $str)
                . $encl;
      }
      fwrite($fp, $line."\n");
   }
}



/**
 * @stub
 * @untested
 * @flag basic
 *
 * @compat
 *    only implements a few basic regular expression lookups
 *    no idea how to handle all of it
 */
if (!function_exists("strptime")) {
   function strptime($str, $format) {
      static $expand = array(
         "%D" => "%m/%d/%y",
         "%T" => "%H:%M:%S",
      );
      static $map_r = array(
          "%S"=>"tm_sec",
          "%M"=>"tm_min",
          "%H"=>"tm_hour",
          "%d"=>"tm_mday",
          "%m"=>"tm_mon",
          "%Y"=>"tm_year",
          "%y"=>"tm_year",
          "%W"=>"tm_wday",
          "%D"=>"tm_yday",
          "%u"=>"unparsed",
      );
      static $names = array(
         "Jan" => 1, "Feb" => 2, "Mar" => 3, "Apr" => 4, "May" => 5, "Jun" => 6,
         "Jul" => 7, "Aug" => 8, "Sep" => 9, "Oct" => 10, "Nov" => 11, "Dec" => 12,
         "Sun" => 0, "Mon" => 1, "Tue" => 2, "Wed" => 3, "Thu" => 4, "Fri" => 5, "Sat" => 6,
      );

      #-- transform $format into extraction regex
      $format = str_replace(array_keys($expand), array_values($expand), $format);
      $preg = preg_replace("/(%\w)/", "(\w+)", preg_quote($format));

      #-- record the positions of all STRFCMD-placeholders
      preg_match_all("/(%\w)/", $format, $positions);
      $positions = $positions[1];
      
      #-- get individual values
      if (preg_match("#$preg#", "$str", $extracted)) {

         #-- get values
         foreach ($positions as $pos=>$strfc) {
            $v = $extracted[$pos + 1];

            #-- add
            if ($n = $map_r[$strfc]) {
               $vals[$n] = ($v > 0) ? (int)$v : $v;
            }
            else {
               $vals["unparsed"] .= $v . " ";
            }
         }
         
         #-- fixup some entries
         $vals["tm_wday"] = $names[ substr($vals["tm_wday"], 0, 3) ];
         if ($vals["tm_year"] >= 1900) {
            $tm_year -= 1900;
         }
         elseif ($vals["tm_year"] > 0) {
            $vals["tm_year"] += 100;
         }
         if ($vals["tm_mon"]) {
            $vals["tm_mon"] -= 1;
         }
         else {
            $vals["tm_mon"] = $names[ substr($vals["tm_mon"], 0, 3) ] - 1;
         }
         
         #-- calculate wday
         // ... (mktime)
      }
      return isset($vals) ? $vals : false;
   }
}










/**
 *                                   --------------------------- FUTURE ---
 * @group FUTURE
 * @since future
 *
 * Following functions aren't implemented in current PHP versions allthough
 * they are logical and required counterparts to existing features or exist
 * in the according external library.
 *
 * @emulated
 *    gzdecode
 *    ob_get_headers
 *    xmlentities
 *
 * @missing
 *    ...
 *
 * @unimplementable
 *    ... 
 *
 */




/**
 * @since future
 *
 * inflates a string enriched with gzip headers
 *  - this is the logical counterpart to gzencode(), but don't tell anyone!
 */
if (!function_exists("gzdecode")) {
   function gzdecode($data, $maxlen=NULL) {

      #-- decode header
      $len = strlen($data);
      if ($len < 20) {
         return;
      }
      $head = substr($data, 0, 10);
      $head = unpack("n1id/C1cm/C1flg/V1mtime/C1xfl/C1os", $head);
      list($ID, $CM, $FLG, $MTIME, $XFL, $OS) = array_values($head);
      $FTEXT = 1<<0;
      $FHCRC = 1<<1;
      $FEXTRA = 1<<2;
      $FNAME = 1<<3;
      $FCOMMENT = 1<<4;
      $head = unpack("V1crc/V1isize", substr($data, $len-8, 8));
      list($CRC32, $ISIZE) = array_values($head);

      #-- check gzip stream identifier
      if ($ID != 0x1f8b) {
         trigger_error("gzdecode: not in gzip format", E_USER_WARNING);
         return;
      }
      #-- check for deflate algorithm
      if ($CM != 8) {
         trigger_error("gzdecode: cannot decode anything but deflated streams", E_USER_WARNING);
         return;
      }

      #-- start of data, skip bonus fields
      $s = 10;
      if ($FLG & $FEXTRA) {
         $s += $XFL;
      }
      if ($FLG & $FNAME) {
         $s = strpos($data, "\000", $s) + 1;
      }
      if ($FLG & $FCOMMENT) {
         $s = strpos($data, "\000", $s) + 1;
      }
      if ($FLG & $FHCRC) {
         $s += 2;  // cannot check
      }
      
      #-- get data, uncompress
      $data = substr($data, $s, $len-$s);
      if ($maxlen) {
         $data = gzinflate($data, $maxlen);
         return($data);  // no checks(?!)
      }
      else {
         $data = gzinflate($data);
      }
      
      #-- check+fin
      $chk = crc32($data);
      if ($CRC32 != $chk) {
         trigger_error("gzdecode: checksum failed (real$chk != comp$CRC32)", E_USER_WARNING);
      }
      elseif ($ISIZE != strlen($data)) {
         trigger_error("gzdecode: stream size mismatch", E_USER_WARNING);
      }
      else {
         return($data);
      }
   }
}



/**
 * get all ob_ soaked headers(),
 * CANNOT be emulated, because output buffering functions
 * already swallow up any sent http header
 *
 */
if (!function_exists("ob_get_headers")) {
   function ob_get_headers() {
      return (array)NULL;
   }
}



/**
 * encodes required named XML entities, like htmlentities(),
 * but does not re-encode numeric &#xxxx; character references
 *  - could screw up scripts which then implement this themselves
 *  - doesn't fix bogus or invalid numeric entities
 *
 * @param string
 * @return string
 */
if (!function_exists("xmlentities")) {
   function xmlentities($str) {
      return strtr($str, array(
        "&#"=>"&#", "&"=>"&amp;", "'"=>"&apos;",
        "<"=>"&lt;", ">"=>"&gt;", "\""=>"&quot;", 
      ));
   }
}





/**
 *                                   ------------------------------ 5.0 ---
 * @group 5_0
 * @since 5.0
 *
 * PHP 5.0 introduces the Zend Engine 2 with new object-orientation features
 * which cannot be reimplemented/defined for PHP4. The additional procedures
 * and functions however can.
 *
 * @emulated
 *    stripos
 *    strripos
 *    str_ireplace
 *    get_headers
 *    headers_list
 *    fprintf
 *    vfprintf
 *    str_split
 *    http_build_query
 *    convert_uuencode
 *    convert_uudecode
 *    scandir
 *    idate
 *    time_nanosleep
 *    strpbrk
 *    get_declared_interfaces
 *    array_combine
 *    array_walk_recursive
 *    substr_compare
 *    spl_classes
 *    class_parents
 *    session_commit
 *    dns_check_record
 *    dns_get_mx
 *    setrawcookie
 *    file_put_contents
 *    COUNT_NORMAL
 *    COUNT_RECURSIVE
 *    count_recursive
 *    FILE_USE_INCLUDE_PATH
 *    FILE_IGNORE_NEW_LINES
 *    FILE_SKIP_EMPTY_LINES
 *    FILE_APPEND
 *    FILE_NO_DEFAULT_CONTEXT
 *    E_STRICT
 *
 * @missing
 *    proc_nice
 *    dns_get_record
 *    date_sunrise - undoc.
 *    date_sunset - undoc.
 *    PHP_CONFIG_FILE_SCAN_DIR
 *    clone
 *
 * @unimplementable
 *    set_exception_handler
 *    restore_exception_handler
 *    debug_print_backtrace
 *    debug_backtrace
 *    class_implements
 *    proc_terminate
 *    proc_get_status
 *    range        - new param
 *    microtime    - new param
 *
 */
 
 


#-- constant: end of line
if (!defined("PHP_EOL")) { define("PHP_EOL", ( (DIRECTORY_SEPARATOR == "\\") ? "\015\012" : (strncmp(PHP_OS, "D", 1) ? "\012" : "\015") )  ); } # "D" for Darwin



/**
 * case-insensitive string search function,
 * - finds position of first occourence of a string c-i
 * - parameters identical to strpos()
 */
if (!function_exists("stripos")) {
   function stripos($haystack, $needle, $offset=NULL) {
   
      #-- simply lowercase args
      $haystack = strtolower($haystack);
      $needle = strtolower($needle);
      
      #-- search
      $pos = strpos($haystack, $needle, $offset);
      return($pos);
   }
}




/**
 * case-insensitive string search function
 * - but this one starts from the end of string (right to left)
 * - offset can be negative or positive
 * 
 */
if (!function_exists("strripos")) {
   function strripos($haystack, $needle, $offset=NULL) {

      #-- lowercase incoming strings
      $haystack = strtolower($haystack);
      $needle = strtolower($needle);

      #-- [-]$offset tells to ignore a few string bytes,
      #   we simply cut a bit from the right
      if (isset($offset) && ($offset < 0)) {
         $haystack = substr($haystack, 0, strlen($haystack) - 1);
      }

      #-- let PHP do it
      $pos = strrpos($haystack, $needle);

      #-- [+]$offset => ignore left haystack bytes
      if (isset($offset) && ($offset > 0) && ($pos > $offset)) {
         $pos = false;
      }

      #-- result      
      return($pos);
   }
}


/**
 * case-insensitive version of str_replace
 * 
 */
if (!function_exists("str_ireplace")) {
   function str_ireplace($search, $replace, $subject, $count=NULL) {

      #-- call ourselves recursively, if parameters are arrays/lists 
      if (is_array($search)) {
         $replace = array_values($replace);
         foreach (array_values($search) as $i=>$srch) {
            $subject = str_ireplace($srch, $replace[$i], $subject);
         }
      }
      
      #-- sluice replacement strings through the Perl-regex module
      #   (faster than doing it by hand)
      else {
         $replace = addcslashes($replace, "$\\");
         $search = "{" . preg_quote($search) . "}i";
         $subject = preg_replace($search, $replace, $subject);
      }

      #-- result
      return($subject);
   }
}


/**
 * performs a http HEAD request
 * 
 */
if (!function_exists("get_headers")) {
   function get_headers($url, $parse=0) {
   
      #-- extract URL parts ($host, $port, $path, ...)
      $c = parse_url($url);
      extract($c);
      if (!isset($port)) { 
         $port = 80;
      }
      
      #-- try to open TCP connection      
      $f = fsockopen($host, $port, $errno, $errstr, $timeout=15);
      if (!$f) {
         return;
      }

      #-- send request header
      socket_set_blocking($f, true);
      fwrite($f, "HEAD $path HTTP/1.0\015\012"
               . "Host: $host\015\012"
               . "Connection: close\015\012"
               . "Accept: */*, xml/*\015\012"
               . "User-Agent: ".trim(ini_get("user_agent"))."\015\012"
               . "\015\012");

      #-- read incoming lines
      $ls = array();
      while ( !feof($f) && ($line = trim(fgets($f, 1<<16))) ) {
         
         #-- read header names to make result an hash (names in array index)
         if ($parse) {
            if ($l = strpos($line, ":")) {
               $name = substr($line, 0, $l);
               $value = trim(substr($line, $l + 1));
               #-- merge headers
               if (isset($ls[$name])) {
                  $ls[$name] .= ", $value";
               }
               else {
                  $ls[$name] = $value;
               }
            }
            #-- HTTP response status header as result[0]
            else {
               $ls[] = $line;
            }
         }
         
         #-- unparsed header list (numeric indices)
         else {
            $ls[] = $line;
         }
      }

      #-- close TCP connection and give result
      fclose($f);
      return($ls);
   }
}


/**
 * @stub
 * list of already/potentially sent HTTP responsee headers(),
 * CANNOT be implemented (except for Apache module maybe)
 * 
 */
if (!function_exists("headers_list")) {
   function headers_list() {
      trigger_error("headers_list(): not supported by this PHP version", E_USER_WARNING);
      return (array)NULL;
   }
}


/**
 * write formatted string to stream/file,
 * arbitrary numer of arguments
 * 
 */
if (!function_exists("fprintf")) {
   function fprintf(/*...*/) {
      $args = func_get_args();
      $stream = array_shift($args);
      return fwrite($stream, call_user_func_array("sprintf", $args));
   }
}


/**
 * write formatted string to stream, args array
 * 
 */
if (!function_exists("vfprintf")) {
   function vfprintf($stream, $format, $args=NULL) {
      return fwrite($stream, vsprintf($format, $args));
   }
}


/**
 * splits a string in evenly sized chunks
 * 
 * @return array
 */
if (!function_exists("str_split")) {
   function str_split($str, $chunk=1) {
      $r = array();
      
      #-- return back as one chunk completely, if size chosen too low
      if ($chunk < 1) {
         $r[] = $str;
      }
      
      #-- add substrings to result array until subject strings end reached
      else {
         $len = strlen($str);
         for ($n=0; $n<$len; $n+=$chunk) {
            $r[] = substr($str, $n, $chunk);
         }
      }
      return($r);
   }
}


/**
 * constructs a QUERY_STRING (application/x-www-form-urlencoded format, non-raw)
 * from a nested array/hash with name=>value pairs
 * - only first two args are part of the original API - rest used for recursion
 *
 * @param  mixed  $data           variable data for query string
 * @param  string $int_prefix     (optional)
 * @param  string $subarray_pfix  (optional)
 * @param integer $level  
 * @return mixed
 */
if (!function_exists("http_build_query")) {
   function http_build_query($data, $int_prefix="", $subarray_pfix="", $level=0) {
   
      #-- empty starting string
      $s = "";
      ($SEP = ini_get("arg_separator.output")) or ($SEP = "&");
      
      #-- traverse hash/array/list entries 
      foreach ($data as $index=>$value) {
         
         #-- add sub_prefix for subarrays (happens for recursed innovocation)
         if ($subarray_pfix) {
            if ($level) {
               $index = "[" . $index . "]";
            }
            $index =  $subarray_pfix . $index;
         }
         #-- add user-specified prefix for integer-indices
         elseif (is_int($index) && strlen($int_prefix)) {
            $index = $int_prefix . $index;
         }
         
         #-- recurse for sub-arrays
         if (is_array($value)) {
            $s .= http_build_query($value, "", $index, $level + 1);
         }
         else {   // or just literal URL parameter
            $s .= $SEP . $index . "=" . urlencode($value);
         }
      }
      
      #-- remove redundant "&" from first round (-not checked above to simplifiy loop)
      if (!$subarray_pfix) {
         $s = substr($s, strlen($SEP));
      }

      #-- return result / to previous array level and iteration
      return($s);
   }
}


/**
 * transform into 3to4 uuencode
 * - this is the bare encoding, not the uu file format
 * 
 * @param  string
 * @return string
 */
if (!function_exists("convert_uuencode")) {
   function convert_uuencode($data) {

      #-- init vars
      $out = "";
      $line = "";
      $len = strlen($data);
#      $data .= "\252\252\252";   // PHP and uuencode(1) use some special garbage??, looks like "\000"* and "`\n`" simply appended

      #-- canvass source string
      for ($n=0; $n<$len; ) {
      
         #-- make 24-bit integer from first three bytes
         $x = (ord($data[$n++]) << 16)
            + (ord($data[$n++]) <<  8)
            + (ord($data[$n++]) <<  0);
            
         #-- disperse that into 4 ascii characters
         $line .= chr( 32 + (($x >> 18) & 0x3f) )
                . chr( 32 + (($x >> 12) & 0x3f) )
                . chr( 32 + (($x >>  6) & 0x3f) )
                . chr( 32 + (($x >>  0) & 0x3f) );
                
         #-- cut lines, inject count prefix before each
         if (($n % 45) == 0) {
            $out .= chr(32 + 45) . "$line\n";
            $line = "";
         }
      }

      #-- throw last line, +length prefix
      if ($trail = ($len % 45)) {
         $out .= chr(32 + $trail) . "$line\n";
      }

      // uuencode(5) doesn't tell so, but spaces are replaced with the ` char in most implementations
      $out = strtr("$out \n", " ", "`");
      return($out);
   }
}


/**
 * decodes uuencoded() data again
 *
 * @param  string $data  
 * @return string
 */
if (!function_exists("convert_uudecode")) {
   function convert_uudecode($data) {

      #-- prepare
      $out = "";
      $data = strtr($data, "`", " ");
      
      #-- go through lines
      foreach(explode("\n", ltrim($data)) as $line) {
         if (!strlen($line)) {
            break;  // end reached
         }
         
         #-- current line length prefix
         unset($num);
         $num = ord($line{0}) - 32;
         if (($num <= 0) || ($num > 62)) {  // 62 is the maximum line length
            break;          // according to uuencode(5), so we stop here too
         }
         $line = substr($line, 1);
         
         #-- prepare to decode 4-char chunks
         $add = "";
         for ($n=0; strlen($add)<$num; ) {
         
            #-- merge 24 bit integer from the 4 ascii characters (6 bit each)
            $x = ((ord($line[$n++]) - 32) << 18)
               + ((ord($line[$n++]) - 32) << 12)  // were saner with "& 0x3f"
               + ((ord($line[$n++]) - 32) <<  6)
               + ((ord($line[$n++]) - 32) <<  0);
               
            #-- reconstruct the 3 original data chars
            $add .= chr( ($x >> 16) & 0xff )
                  . chr( ($x >>  8) & 0xff )
                  . chr( ($x >>  0) & 0xff );
         }

         #-- cut any trailing garbage (last two decoded chars may be wrong)
         $out .= substr($add, 0, $num);
         $line = "";
      }

      return($out);
   }
}


/**
 * return array of filenames in a given directory
 * (only works for local files)
 *
 * @param  string $dirname  
 * @param  bool   $desc  
 * @return array
 */
if (!function_exists("scandir")) {
   function scandir($dirname, $desc=0) {
   
      #-- check for file:// protocol, others aren't handled
      if (strpos($dirname, "file://") === 0) {
         $dirname = substr($dirname, 7);
         if (strpos($dirname, "localh") === 0) {
            $dirname = substr($dirname, strpos($dirname, "/"));
         }
      }
      
      #-- directory reading handle
      if ($dh = opendir($dirname)) {
         $ls = array();
         while ($fn = readdir($dh)) {
            $ls[] = $fn;  // add to array
         }
         closedir($dh);
         
         #-- sort filenames
         if ($desc) {
            rsort($ls);
         }
         else {
            sort($ls);
         }
         return $ls;
      }

      #-- failure
      return false;
   }
}


/**
 * like date(), but returns an integer for given one-letter format parameter
 *
 * @param  string  $formatchar
 * @param  integer $timestamp
 * @return integer
 */
if (!function_exists("idate")) {
   function idate($formatchar, $timestamp=NULL) {
   
      #-- reject non-simple type parameters
      if (strlen($formatchar) != 1) {
         return false;
      }
      
      #-- get current time, if not given
      if (!isset($timestamp)) {
         $timestamp = time();
      }
      
      #-- get and turn into integer
      $str = date($formatchar, $timestamp);
      return (int)$str;
   }
}



/**
 * combined sleep() and usleep() 
 * 
 */
if (!function_exists("time_nanosleep")) {
   function time_nanosleep($sec, $nano) {
      sleep($sec);
      usleep($nano);
   }
}




/**
 * search first occourence of any of the given chars, returns rest of haystack
 * (char_list must be a string for compatibility with the real PHP func)
 *
 * @param  string $haystack  
 * @param  string $char_list  
 * @return integer
 */
if (!function_exists("strpbrk")) {
   function strpbrk($haystack, $char_list) {
   
      #-- prepare
      $len = strlen($char_list);
      $min = strlen($haystack);
      
      #-- check with every symbol from $char_list
      for ($n = 0; $n < $len; $n++) {
         $l = strpos($haystack, $char_list{$n});
         
         #-- get left-most occourence
         if (($l !== false) && ($l < $min)) {
            $min = $l;
         }
      }
      
      #-- result
      if ($min) {
         return(substr($haystack, $min));
      }
      else {
         return(false);
      }
   }
}



/**
 * logo image activation URL query strings (gaga feature)
 * 
 */
if (!function_exists("php_real_logo_guid")) {
   function php_real_logo_guid() { return php_logo_guid(); }
   function php_egg_logo_guid() { return zend_logo_guid(); }
}


/**
 * no need to implement this
 * (there aren't interfaces in PHP4 anyhow)
 * 
 */
if (!function_exists("get_declared_interfaces")) {
   function get_declared_interfaces() {
      trigger_error("get_declared_interfaces(): Current script won't run reliably with PHP4.", E_USER_WARNING);
      return( (array)NULL );
   }
}



/**
 * creates an array from lists of $keys and $values
 * (both should have same number of entries)
 *
 * @param  array $keys  
 * @param  array $values  
 * @return array
 */
if (!function_exists("array_combine")) {
   function array_combine($keys, $values) {
   
      #-- convert input arrays into lists
      $keys = array_values($keys);
      $values = array_values($values);
      $r = array();
      
      #-- one from each
      foreach ($values as $i=>$val) {
         if ($key = $keys[$i]) {
            $r[$key] = $val;
         }
         else {
            $r[] = $val;   // useless, PHP would have long aborted here
         }
      }
      return($r);
   }
}


/**
 * apply userfunction to each array element (descending recursively)
 * use it like:  array_walk_recursive($_POST, "stripslashes");
 * - $callback can be static function name or object/method, class/method
 *
 * @param  array  $input  
 * @param  string $callback  
 * @param  array  $userdata  (optional)
 * @return array
 */
if (!function_exists("array_walk_recursive")) {
   function array_walk_recursive(&$input, $callback, $userdata=NULL) {
      #-- each entry
      foreach ($input as $key=>$value) {

         #-- recurse for sub-arrays
         if (is_array($value)) {
            array_walk_recursive($input[$key], $callback, $userdata);
         }

         #-- $callback handles scalars
         else {
            call_user_func_array($callback, array(&$input[$key], $key, $userdata) );
         }
      }

      // no return value
   }
}


/**
 * complicated wrapper around substr() and and strncmp()
 *
 * @param  string  $haystack  
 * @param  string  $needle  
 * @param  integer $offset  
 * @param  integer $len  
 * @param  integer $ci  
 * @return mixed
 */
if (!function_exists("substr_compare")) {
   function substr_compare($haystack, $needle, $offset=0, $len=0, $ci=0) {

      #-- check params   
      if ($len <= 0) {   // not well documented
         $len = strlen($needle);
         if (!$len) { return(0); }
      }
      #-- length exception
      if ($len + $offset >= strlen($haystack)) {
         trigger_error("substr_compare: given length exceeds main_str", E_USER_WARNING);
         return(false);
      }

      #-- cut
      if ($offset) {
         $haystack = substr($haystack, $offset, $len);
      }
      #-- case-insensitivity
      if ($ci) {
         $haystack = strtolower($haystack);
         $needle = strtolower($needle);
      }

      #-- do
      return(strncmp($haystack, $needle, $len));
   }
}


/**
 * stub, returns empty list as usual;
 * you must load "ext/spl.php" beforehand to get this
 * 
 */
if (!function_exists("spl_classes")) {
   function spl_classes() {
      trigger_error("spl_classes(): not built into this PHP version");
      return (array)NULL;
   }
}



/**
 * gets you list of class names the given objects class was derived from, slow
 *
 * @param  object $obj  
 * @return object
 */
if (!function_exists("class_parents")) {
   function class_parents($obj) {
   
      #-- first get full list
      $all = get_declared_classes();
      $r = array();
      
      #-- filter out
      foreach ($all as $potential_parent) {
         if (is_subclass_of($obj, $potential_parent)) {
            $r[$potential_parent] = $potential_parent;
         }
      }
      return($r);
   }
}


/**
 * an alias
 * 
 */
if (!function_exists("session_commit") && function_exists("session_write_close")) {
   function session_commit() {
      // simple
      session_write_close();
   }
}


/**
 * aliases
 *
 * @param  mixed $host  
 * @param  mixed $type  (optional)
 * @return mixed
 */
if (!function_exists("dns_check_record")) {
   function dns_check_record($host, $type=NULL) {
      // synonym to
      return checkdnsrr($host, $type);
   }
}
if (!function_exists("dns_get_mx")) {
   function dns_get_mx($host, $mx) {
      $args = func_get_args();
      // simple alias - except the optional, but referenced third parameter
      if ($args[2]) {
         $w = & $args[2];
      }
      else {
         $w = false;
      }
      return getmxrr($host, $mx, $w);
   }
}


/**
 * setrawcookie(),
 * can this be emulated 100% exactly?
 *
 * @param  string $name 
 * @param  mixed  $value
 * @param  mixed  $expire
 * @param  mixed  $path
 * @param  mixed  $domain
 * @param integer $secure
 * @return string
 */
if (!function_exists("setrawcookie")) {
   // we output everything directly as HTTP header(), PHP doesn't seem
   // to manage an internal cookie list anyhow
   function setrawcookie($name, $value=NULL, $expire=NULL, $path=NULL, $domain=NULL, $secure=0) {
      if (isset($value) && strpbrk($value, ",; \r\t\n\f\014\013")) {
         trigger_error("setrawcookie: value may not contain any of ',; \r\n' and some other control chars; thrown away", E_USER_WARNING);
      }
      else {
         $h = "Set-Cookie: $name=$value"
            . ($expire ? "; expires=" . gmstrftime("%a, %d-%b-%y %H:%M:%S %Z", $expire) : "")
            . ($path ? "; path=$path": "")
            . ($domain ? "; domain=$domain" : "")
            . ($secure ? "; secure" : "");
         header($h);
      }
   }
}


/**
 * write-at-once file access (counterpart to file_get_contents)
 *
 * @param  integer $filename
 * @param  mixed   $data  
 * @param  integer $flags 
 * @param  mixed   $resource
 * @return integer
 */
if (!function_exists("file_put_contents")) {
   function file_put_contents($filename, $data, $flags=0, $resource=NULL) {

      #-- prepare
      $mode = ($flags & FILE_APPEND ? "a" : "w" ) ."b";
      $incl = $flags & FILE_USE_INCLUDE_PATH;
      $length = strlen($data);
//      $resource && trigger_error("EMULATED file_put_contents does not support \$resource parameter.", E_USER_ERROR);
      
      #-- data
      if (is_array($data) || is_object($data)) {
         $data = implode("", (array)$data);
      }

      #-- open for writing
      $f = fopen($filename, $mode, $incl);
      if ($f) {
      
         // locking
         if (($flags & LOCK_EX) && !flock($f, LOCK_EX)) {
            return fclose($f) && false;
         }

         // write
         $written = fwrite($f, $data);
         fclose($f);
         
         #-- only report success, if completely saved
         return($length == $written);
      }
   }
}


/**
 * file-related constants
 *
 */
if (!defined("FILE_USE_INCLUDE_PATH")) { define("FILE_USE_INCLUDE_PATH", 1); }
if (!defined("FILE_IGNORE_NEW_LINES")) { define("FILE_IGNORE_NEW_LINES", 2); }
if (!defined("FILE_SKIP_EMPTY_LINES")) { define("FILE_SKIP_EMPTY_LINES", 4); }
if (!defined("FILE_APPEND")) { define("FILE_APPEND", 8); }
if (!defined("FILE_NO_DEFAULT_CONTEXT")) { define("FILE_NO_DEFAULT_CONTEXT", 16); }



#-- more new constants for 5.0
if (!defined("E_STRICT")) { define("E_STRICT", 2048); }  // _STRICT is a special case of _NOTICE (_DEBUG)
# PHP_CONFIG_FILE_SCAN_DIR



#-- array count_recursive()
if (!defined("COUNT_NORMAL")) { define("COUNT_NORMAL", 0); }      // count($array, 0);
if (!defined("COUNT_RECURSIVE")) { define("COUNT_RECURSIVE", 1); }    // use count_recursive()



/**
 * @since never
 * @nonstandard
 * 
 * we introduce a new function, because we cannot emulate the
 * newly introduced second parameter to count()
 * 
 * @param  array $array 
 * @param  integer $mode
 * @return integer
 */
if (!function_exists("count_recursive")) {
   function count_recursive($array, $mode=1) {
      if (!$mode) {
         return(count($array));
      }
      else {
         $c = count($array);
         foreach ($array as $sub) {
            if (is_array($sub)) {
               $c += count_recursive($sub);
            }
         }
         return($c);
      }
   }
}





/**
 *                                   ------------------------------ 4.4 ---
 * @group 4_4
 * @since 4.4
 *
 * PHP 4.4 is a bugfix and backporting version created after PHP 5. It went
 * mostly unchanged, but changes a few language semantics (e.g. references).
 *
 * @emulated
 *    PHP_INT_SIZE
 *    PHP_INT_MAX
 *    SORT_LOCALE_STRING
 *
 */

if (!defined("PHP_INT_SIZE")) { define("PHP_INT_SIZE", 4); }
if (!defined("PHP_INT_MAX")) { define("PHP_INT_MAX", 2147483647); }
if (!defined("SORT_LOCALE_STRING")) { define("SORT_LOCALE_STRING", 5); }






/**
 *                                   ------------------------------ 4.3 ---
 * @group 4_3
 * @since 4.3
 *
 *  Additions in 4.3 version of PHP interpreter.
 *
 * @emulated
 *    file_get_contents
 *    array_key_exists
 *    array_intersect_assoc
 *    array_diff_assoc
 *    html_entity_decode
 *    str_word_count
 *    str_shuffle
 *    get_include_path
 *    set_include_path
 *    restore_include_path
 *    fnmatch
 *    FNM_PATHNAME
 *    FNM_NOESCAPE
 *    FNM_PERIOD
 *    FNM_LEADING_DIR
 *    FNM_CASEFOLD
 *    FNM_EXTMATCH
 *    glob
 *    GLOB_MARK
 *    GLOB_NOSORT
 *    GLOB_NOCHECK
 *    GLOB_NOESCAPE
 *    GLOB_BRACE
 *    GLOB_ONLYDIR
 *    GLOB_NOCASE
 *    GLOB_DOTS
 *    __FUNCTION__
 *    PATH_SEPARATOR
 *    PHP_SHLIB_SUFFIX
 *    PHP_SAPI
 *    PHP_PREFIX
 *
 * @missing
 *    sha1_file
 *    sha1 - too much code, and has been reimplemented elsewhere
 *
 * @unimplementable
 *    money_format
 *
 */


/**
 * simplified file read-at-once function
 *
 * @param  string  $filename  
 * @param  integer $use_include_path  (optional)
 * @return string
 */
if (!function_exists("file_get_contents")) {
   function file_get_contents($filename, $use_include_path=1) {

      #-- open file, let fopen() report error
      $f = fopen($filename, "rb", $use_include_path);
      if (!$f) { return; }

      #-- read max 2MB
      $content = fread($f, 1<<21);
      fclose($f);
      return($content);
   }
}



/**
 * shell-like filename matching (* and ? globbing characters)
 *
 * @param  string $pattern  glob-pattern with *s and ?s
 * @param  string $fn       filename to match it against (without path)
 * @param integer $flags    (optional)
 * @return bool
 */
if (!function_exists("fnmatch")) {

   #-- associated constants
   if (!defined("FNM_PATHNAME")) { define("FNM_PATHNAME", 1<<0); }  // no wildcard ever matches a "/"
   if (!defined("FNM_NOESCAPE")) { define("FNM_NOESCAPE", 1<<1); }  // backslash can't escape meta chars
   if (!defined("FNM_PERIOD")) { define("FNM_PERIOD",   1<<2); }  // leading dot must be given explicit
   if (!defined("FNM_LEADING_DIR")) { define("FNM_LEADING_DIR", 1<<3); }  // not in PHP
   if (!defined("FNM_CASEFOLD")) { define("FNM_CASEFOLD", 0x50); }  // match case-insensitive
   if (!defined("FNM_EXTMATCH")) { define("FNM_EXTMATCH", 1<<5); }  // not in PHP
   
   #-- implementation
   function fnmatch($pattern, $fn, $flags=0x0000) {
      
      #-- 'hidden' files
      if ($flags & FNM_PERIOD) {
         if (($fn[0] == ".") && ($pattern[0] != ".")) {
            return(false);    // abort early
         }
      }

      #-- case-insensitivity
      $rxci = "";
      if ($flags & FNM_CASEFOLD) {
         $rxci = "i";
      }
      #-- handline of pathname separators (/)
      $wild = ".";
      if ($flags & FNM_PATHNAME) {
         $wild = "[^/".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."]";
      }

      #-- check for cached regular expressions
      static $cmp = array();
      if (isset($cmp["$pattern+$flags"])) {
         $rx = $cmp["$pattern+$flags"];
      }

      #-- convert filename globs into regex
      else {
         $rx = preg_quote($pattern);
         $rx = strtr($rx, array(
            "\\*"=>"$wild*?", "\\?"=>"$wild", "\\["=>"[", "\\]"=>"]",
         ));
         $rx = "{^" . $rx . "$}" . $rxci;
         
         #-- cache
         if (count($cmp) >= 50) {
            $cmp = array();   // free
         }
         $cmp["$pattern+$flags"] = $rx;
      }
      
      #-- compare
      return(preg_match($rx, $fn));
   }
}


/**
 * file search and name matching (with shell patterns)
 *
 * @param  string  $pattern   search pattern and path ../* string
 * @param  integer $flags (optional)
 * @return array
 */
if (!function_exists("glob")) {

   #-- introduced constants
   if (!defined("GLOB_MARK")) { define("GLOB_MARK", 1<<0); }
   if (!defined("GLOB_NOSORT")) { define("GLOB_NOSORT", 1<<1); }
   if (!defined("GLOB_NOCHECK")) { define("GLOB_NOCHECK", 1<<2); }
   if (!defined("GLOB_NOESCAPE")) { define("GLOB_NOESCAPE", 1<<3); }
   if (!defined("GLOB_BRACE")) { define("GLOB_BRACE", 1<<4); }
   if (!defined("GLOB_ONLYDIR")) { define("GLOB_ONLYDIR", 1<<5); }
   if (!defined("GLOB_NOCASE")) { define("GLOB_NOCASE", 1<<6); }
   if (!defined("GLOB_DOTS")) { define("GLOB_DOTS", 1<<7); }
   // unlikely to work under Win(?), without replacing the explode() with
   // a preg_split() incorporating the native DIRECTORY_SEPARATOR as well

   #-- implementation
   function glob($pattern, $flags=0x0000) {
      $ls = array();
      $rxci = ($flags & GLOB_NOCASE) ? "i" : "";
#echo "\n=> glob($pattern)...\n";
      
      #-- transform glob pattern into regular expression
      #   (similar to fnmatch() but still different enough to require a second func)
      if ($pattern) {

         #-- look at each directory/fn spec part separately
         $parts2 = explode("/", $pattern);
         $pat = preg_quote($pattern);
         $pat = strtr($pat, array("\\*"=>".*?", "\\?"=>".?"));
         if ($flags ^ GLOB_NOESCAPE) {
            // uh, oh, ouuch - the above is unclean enough...
         }
         if ($flags ^ GLOB_BRACE) {
            $pat = preg_replace("/\{(.+?)\}/e", 'strtr("[$1]", ",", "")', $pat);
         }
         $parts = explode("/", $pat);
#echo "parts == ".implode(" // ", $parts) . "\n";
         $lasti = count($parts) - 1;
         $dn = "";
         foreach ($parts as $i=>$p) {

            #-- basedir included (yet no pattern matching necessary)
            if (!strpos($p, "*?") && (strpos($p, ".?")===false)) {
               $dn .= $parts2[$i] . ($i!=$lasti ? "/" : "");
#echo "skip:$i, cause no pattern matching char found -> only a basedir spec\n";
               continue;
            }
            
            #-- start reading dir + match filenames against current pattern
            if ($dh = opendir($dn ?$dn:'.')) {
               $with_dot = ($p[1]==".") || ($flags & GLOB_DOTS);
#echo "part:$i:$p\n";
#echo "reading dir \"$dn\"\n";
               while ($fn = readdir($dh)) {
                  if (preg_match("\007^$p$\007$rxci", $fn)) {

                     #-- skip over 'hidden' files
                     if (($fn[0] == ".") && !$with_dot) {
                        continue;
                     }

                     #-- add filename only if last glob/pattern part
                     if ($i==$lasti) {
                        if (is_dir("$dn$fn")) {
                           if ($flags & GLOB_ONLYDIR) {
                              continue;
                           }
                           if ($flags & GLOB_MARK) {
                              $fn .= "/";
                           }
                        }
#echo "adding '$fn' for dn=$dn to list\n";
                        $ls[] = "$dn$fn";
                     }

                     #-- initiate a subsearch, merge result list in
                     elseif (is_dir("$dn$fn")) {
                        // add reamaining search patterns to current basedir
                        $remaind = implode("/", array_slice($parts2, $i+1));
                        $ls = array_merge($ls, glob("$dn$fn/$remaind", $flags));
                     }
                  }
               }
               closedir($dh);

               #-- prevent scanning a 2nd part/dir in same glob() instance:
               break;  
            }

            #-- given dirname doesn't exist
            else {
               return($ls);
            }

         }// foreach $parts
      }

      #-- return result list
      if (!$ls && ($flags & GLOB_NOCHECK)) {
         $ls[] = $pattern;
      }
      if ($flags ^ GLOB_NOSORT) {
         sort($ls);
      }
#print_r($ls);
#echo "<=\n";
      return($ls);
   }
} //@FIX: fully comment, remove debugging code (- as soon as it works ;)



/**
 * redundant alias for isset()
 * 
 */
if (!function_exists("array_key_exists")) {
   function array_key_exists($key, $search) {
      return isset($search[$key]);
   }
}


/**
 * who could need that?
 * 
 */
if (!function_exists("array_intersect_assoc")) {
   function array_intersect_assoc( /*array, array, array...*/ ) {

      #-- parameters, prepare
      $in = func_get_args();
      $cmax = count($in);
      $whatsleftover = array();
      
      #-- walk through each array pair
      #   (take first as checklist)
      foreach ($in[0] as $i => $v) {
         for ($c = 1; $c < $cmax; $c++) {
            #-- remove entry, as soon as it isn't present
            #   in one of the other arrays
            if (!isset($in[$c][$i]) || (@$in[$c][$i] !== $v)) {
               continue 2;
            }
         }
         #-- it was found in all other arrays
         $whatsleftover[$i] = $v;
      }
      return $whatsleftover;
   }
}


/**
 * the opposite of the above
 * 
 */
if (!function_exists("array_diff_assoc")) {
   function array_diff_assoc( /*array, array, array...*/ ) {

      #-- params
      $in = func_get_args();
      $diff = array();
      
      #-- compare each array with primary/first
      foreach ($in[0] as $i=>$v) {
         for ($c=1; $c<count($in); $c++) {
            #-- skip as soon as it matches with entry in another array
            if (isset($in[$c][$i]) && ($in[$c][$i] == $v)) {
               continue 2;
            }
         }
         #-- else
         $diff[$i] = $v;
      }
      return $diff;
   }
}


/**
 * opposite of htmlentities
 * 
 */
if (!function_exists("html_entity_decode")) {
   function html_entity_decode($string, $quote_style=ENT_COMPAT, $charset="ISO-8859-1") {
      //@FIX: we fall short on anything other than Latin-1
      $y = array_flip(get_html_translation_table(HTML_ENTITIES, $quote_style));
      return strtr($string, $y);
   }
}


/**
 * extracts single words from a string
 * 
 */
if (!function_exists("str_word_count")) {
   function str_word_count($string, $result=0) {
   
      #-- let someone else do the work
      preg_match_all('/([\w](?:[-\'\w]?[\w]+)*)/', $string, $uu);

      #-- return full word list
      if ($result == 1) {
         return($uu[1]);
      }
      
      #-- array() of $pos=>$word entries
      elseif ($result >= 2) {
         $r = array();
         $l = 0;
         foreach ($uu[1] as $word) {
            $l = strpos($string, $word, $l);
            $r[$l] = $word;
            $l += strlen($word);  // speed up next search
         }
         return($r);
      }

      #-- only count
      else {
         return(count($uu[1]));
      }
   }
}


/**
 * creates a permutation of the given strings characters
 * (let's hope the random number generator was alread initialized)
 * 
 */
if (!function_exists("str_shuffle")) {
   function str_shuffle($str) {
      $r = "";

      #-- cut string down with every iteration
      while (strlen($str)) {
         $n = strlen($str) - 1;
         if ($n) {
            $n = rand(0, $n);   // glibcs` rand is ok since 2.1 at least
         }
         
         #-- cut out elected char, add to result string
         $r .= $str{$n};
         $str = substr($str, 0, $n) . substr($str, $n + 1);
      }
      return($r);
   }
}


/**
 * simple shorthands
 * 
 */
if (!function_exists("get_include_path")) {
   function get_include_path() {
      return(get_cfg_var("include_path"));
   }
   function set_include_path($new) {
      return ini_set("include_path", $new);
   }
   function restore_include_path() {
      ini_restore("include_path");
   }
}


#-- constants for 4.3
   if (!defined("PATH_SEPARATOR")) { define("PATH_SEPARATOR", ((DIRECTORY_SEPARATOR=='\\') ? ';' :':')); }
   if (!defined("PHP_SHLIB_SUFFIX")) { define("PHP_SHLIB_SUFFIX", ((DIRECTORY_SEPARATOR=='\\') ? 'dll' :'so')); }
   if (!defined("PHP_SAPI")) { define("PHP_SAPI", php_sapi_name()); }
   if (!defined("__FUNCTION__")) { define("__FUNCTION__", NULL); }   // empty string would signalize main()


#-- not identical to what PHP reports (it seems to `which` for itself)
if (!defined("PHP_PREFIX") && isset($_ENV["_"])) { define("PHP_PREFIX", substr($_ENV["_"], 0, strpos($_ENV["_"], "bin/"))); }






/**
 *                                   ------------------------------ 4.2 ---
 * @group 4_2
 * @since 4.2
 *
 *
 *  Functions added in PHP 4.2 interpreters.
 *
 *
 * @emulated
 *   str_rot13
 *   array_change_key_case
 *   array_fill
 *   array_chunk
 *   md5_file
 *   is_a
 *   fmod
 *   floatval
 *   is_infinite
 *   is_nan
 *   is_finite
 *   var_export
 *   strcoll
 * @missing
 *   ...
 *
 *   almost complete!?
 *
 *
 */


/**
 * shy away from this function - it was broken in all PHP4.2 releases,
 * and our emulation here won't change that
 *
 * @param  string $str  
 * @return string
 */
if (!function_exists("str_rot13")) {
   function str_rot13($str) {
      static $from = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
      static $to = "NOPQRSTUVWXYZABCDEFGHIJKLMnopqrstuvwxyzabcdefghijklm";
      return strtr($str, $from, $to);
   }
}


/**
 * changes case of textual index keys
 *
 * @param  array $array  
 * @param  int   $case
 * @return array
 */
if (!function_exists("array_change_key_case")) {
   
   #-- introduced constants
   if (!defined("CASE_LOWER")) { define("CASE_LOWER", 0); }
   if (!defined("CASE_UPPER")) { define("CASE_UPPER", 1); }
   
   #-- implementation
   function array_change_key_case($array, $case=CASE_LOWER) {
   
      #-- loop through
      foreach ($array as $i=>$v) {
         #-- do anything for strings only
         if (is_string($i)) {
            unset($array[$i]);
            $i = ($case==CASE_LOWER) ? strtolower($i) : strtoupper($i);
            $array[$i] = $v;
         }
         // non-recursive      
      }
      return($array);
   }
}


/**
 * create fixed-length array made up of $value data
 * 
 */
if (!function_exists("array_fill")) {
   function array_fill($start_index, $num, $value) {

      #-- params
      $r = array();
      $i = $start_index;
      $end = $num + $start_index;
      
      #-- append
      for (; $i < $end; $i++)
      {
         $r[$i] = $value;
      }
      return($r);
   }
}


/**
 * split an array into evenly sized parts
 * 
 */
if (!function_exists("array_chunk")) {
   function array_chunk($input, $size, $preserve_keys=false) {
   
      #-- array for chunked output
      $r = array();
      $n = -1;  // chunk index
      
      #-- enum input array blocks
      foreach ($input as $i=>$v) {
      
         #-- new chunk
         if (($n < 0) || (count($r[$n]) == $size)) {
            $n++;
            $r[$n] = array();
         }
         
         #-- add input value into current [$n] chunk
         if ($preserve_keys) {
            $r[$n][$i] = $v;
         }
         else {
            $r[$n][] = $v;
         }
      }
      return($r);
   }
}


/**
 * convenience wrapper
 * 
 */
if (!function_exists("md5_file")) {
   function md5_file($filename, $raw_output=false) {

      #-- read file, apply hash function
      $data = file_get_contents($filename, "rb");
      $r = md5($data);
      $data = NULL;
         
      #-- transform? and return
      if ($raw_output) {
         $r = pack("H*", $r);
      }
      return $r;
   }
}


/**
 * object type checking
 * 
 */
if (!function_exists("is_a")) {
   function is_a($obj, $classname) {
   
      #-- lowercase everything for comparison
      $classnaqme = strtolower($classname);
      $obj_class =  strtolower(get_class($obj));
      
      #-- two possible checks
      return ($obj_class == $classname) or is_subclass_of($obj, $classname);
   }
}


/**
 * floating point modulo
 * 
 */
if (!function_exists("fmod")) {
   function fmod($x, $y) {
      $r = $x / $y;
      $r -= (int)$r;
      $r *= $y;
      return($r);
   }
}


/**
 * makes float variable from string
 *
 * @param  string
 * @return float
 */
if (!function_exists("floatval")) {
   function floatval($str) {
      $str = ltrim($str);
      return (float)$str;
   }
}


/**
 * floats
 *
 */
if (!function_exists("is_infinite")) {

   #-- constants as-is
   if (!defined("NAN")) { define("NAN", "NAN"); }
   if (!defined("INF")) { define("INF", "INF"); }   // there is also "-INF"
   
   #-- simple checks
   function is_infinite($f) {
      $s = (string)$f;
      return(  ($s=="INF") || ($s=="-INF")  );
   }
   function is_nan($f) {
      $s = (string)$f;
      return(  $s=="NAN"  );
   }
   function is_finite($f) {
      $s = (string)$f;
      return(  !strpos($s, "N")  );
   }
}


/**
 * throws value-instantiation PHP-code for given variable
 *
 * @compat
 *    output differentiates from native PHP version,
 *    but functions identically
 *
 * @param  mixed $var  
 * @param  mixed $return  (optional) false
 * @param  string $indent  (optional) ""
 * @param  string $output  (optional) ""
 * @return mixed
 */
if (!function_exists("var_export")) {
   function var_export($var, $return=false, $indent="", $output="") {

      #-- output as in-class variable definitions
      if (is_object($var)) {
         $output = "class " . get_class($var) . " {\n";
         foreach (((array)$var) as $id=>$var) {
            $output .= "  var \$$id = " . var_export($var, true) . ";\n";
         }
         $output .= "}";
      }
      
      #-- array constructor
      elseif (is_array($var)) {
         foreach ($var as $id=>$next) {
            if ($output) $output .= ",\n";
                    else $output = "array(\n";
            $output .= $indent . '  '
                    . (is_numeric($id) ? $id : '"'.addslashes($id).'"')
                    . ' => ' . var_export($next, true, "$indent  ");
         }
         if (empty($output)) $output = "array(";
         $output .= "\n{$indent})";
       #if ($indent == "") $output .= ";";
      }
      
      #-- literals
      elseif (is_numeric($var)) {
         $output = "$var";
      }
      elseif (is_bool($var)) {
         $output = $var ? "true" : "false";
      }
      else {
         $output = "'" . preg_replace("/([\\\\\'])/", '\\\\$1', $var) . "'";
      }

      #-- done
      if ($return) {
         return($output);
      }
      else {
         print($output);
      }
   }
}


/**
 * @stub
 * @since existed since PHP 4.0.5, but under Win32 first since 4.3.2
 * 
 * strcmp() variant that respects locale setting,
 *
 * @param  string $str1  
 * @param  string $str2  
 * @return string
 */
if (!function_exists("strcoll")) {
   function strcoll($str1, $str2) {
      return strcmp($str1, $str2);
   }
}





/**
 *                                   ------------------------------ 4.1 ---
 * @group 4_1
 * @since 4.1
 *
 *
 * See also "ext/math41.php" for some more (rarely used mathematical funcs).
 *
 *
 * @emulated
 *   diskfreespace
 *   disktotalspace
 *   vprintf
 *   vsprintf
 *   import_request_variables
 *   hypot
 *   log1p
 *   expm1
 *   sinh
 *   cosh
 *   tanh
 *   asinh
 *   acosh
 *   atanh
 *   mhash
 *   mhash_count
 *   mhash_get_hash_name
 *   mhash_get_block_size
 * @missing
 *   nl_langinfo - unimpl?
 *   getmygid
 *   version_compare
 *
 */




/**
 * aliases (an earlier fallen attempt to unify PHP function names)
 * 
 */
if (!function_exists("diskfreespace")) {
   function diskfreespace() {
      return disk_free_sapce();
   }
   function disktotalspace() {
      return disk_total_sapce();
   }
}


/**
 * variable count of arguments (in array list) printf variant
 *
 * @param  string $format  
 * @param  mixed  $args
 * @output
 */
if (!function_exists("vprintf")) {
   function vprintf($format, $args=NULL) {
      call_user_func_array("fprintf", get_func_args());
   }
}


/**
 * same as above, but doesn't output directly and returns formatted string
 *
 * @param  string $format
 * @param  mixed  $args
 * @return string
 */
if (!function_exists("vsprintf")) {
   function vsprintf($format, $args=NULL) {
      $args = array_merge(array($format), array_values((array)$args));
      return call_user_func_array("sprintf", $args);
   }
}


/**
 * @extended
 *
 * can be used to simulate a register_globals=on environment
 *
 * @param  string $types   order of GET,POST,COOKIE variables
 * @param  string $pfix    prefix for imported variable names
 * @global $GLOBALS
 */
if (!function_exists("import_request_variables")) {
   function import_request_variables($types="GPC", $pfix="") {
      
      #-- associate abbreviations to global var names
      $alias = array(
         "G" => "_GET",
         "P" => "_POST",
         "C" => "_COOKIE",
         "S" => "_SERVER",   // non-standard
         "E" => "_ENV",      // non-standard
      );

      #-- alias long names (PHP < 4.0.6)    //@FIXME: does that belong here?
      if (!isset($_REQUEST)) {
         $_GET = & $HTTP_GET_VARS;
         $_POST = & $HTTP_POST_VARS;
         $_COOKIE = & $HTTP_COOKIE_VARS;
      }
      
      #-- copy
      for ($i=0; $i<strlen($types); $i++) {
         if ($FROM = $alias[strtoupper($c)]) {
            foreach ($$FROM as $key=>$val) {
               if (!isset($GLOBALS[$pfix.$key])) {
                  $GLOBALS[$pfix . $key] = $val;
               }
            }
         }
      }
      // done
   }
}


// a few mathematical functions follow
// (wether we should really emulate them is a different question)

#-- me has no idea what this function means
if (!function_exists("hypot")) {
   function hypot($num1, $num2) {
      return sqrt($num1*$num1 + $num2*$num2);  // as per PHP manual ;)
   }
}

#-- more accurate logarithm func, but we cannot simulate it
#   (too much work, too slow in PHP)
if (!function_exists("log1p")) {
   function log1p($x) {
      return(  log(1+$x)  );
   }
   #-- same story for:
   function expm1($x) {
      return(  exp($x)-1  );
   }
}

#-- as per PHP manual
if (!function_exists("sinh")) {
   function sinh($f) {
      return(  (exp($f) - exp(-$f)) / 2  );
   }
   function cosh($f) {
      return(  (exp($f) + exp(-$f)) / 2  );
   }
   function tanh($f) {
      return(  sinh($f) / cosh($f)  );   // ok, that one makes sense again :)
   }
}

#-- these look a bit more complicated
if (!function_exists("asinh")) {
   function asinh($x) {
      return(  log($x + sqrt($x*$x+1))  );
   }
   function acosh($x) {
      return(  log($x + sqrt($x*$x-1))  );
   }
   function atanh($x) {
      return(  log1p( 2*$x / (1-$x) ) / 2  );
   }
}




/**
 * HMAC from RFC2104, but see also PHP_Compat or Crypt_HMAC
 *
 * @param  string $hashtype  which encoding functions to use
 * @param  string $text      plaintext to hash
 * @param  string $key       key data
 * @return string            hash
 */
if (!function_exists("mhash")) {

   #-- constants
   if (!defined("MHASH_CRC32")) { define("MHASH_CRC32", 0); }
   if (!defined("MHASH_MD5")) { define("MHASH_MD5", 1); }       // RFC1321
   if (!defined("MHASH_SHA1")) { define("MHASH_SHA1", 2); }      // RFC3174
   if (!defined("MHASH_TIGER")) { define("MHASH_TIGER", 7); }
   if (!defined("MHASH_MD4")) { define("MHASH_MD4", 16); }      // RFC1320
   if (!defined("MHASH_SHA256")) { define("MHASH_SHA256", 17); }
   if (!defined("MHASH_ADLER32")) { define("MHASH_ADLER32", 18); }
   
   #-- implementation
   function mhash($hashtype, $text, $key) {
   
      #-- hash function
      if (!($func = mhash_get_hash_name($hashtype)) || !function_exists($func)) {
         return trigger_error("mhash: cannot use hash algorithm #$hashtype/$func", E_USER_ERROR);
      }
      if (!$key) {
         trigger_error("mhash: called without key", E_USER_WARNING);
      }
      
      #-- params
      $bsize = mhash_get_block_size($hashtype);   // fixed size, 64

      #-- pad key
      if (strlen($key) > $bsize) {  // hash key, when it's too long
         $key = $func($key); 
         $key = pack("H*", $key);   // binarify
      }
      $key = str_pad($key, $bsize, "\0");  // fill up with NULs (1)
      
      #-- prepare inner and outer padding stream
      $ipad = str_pad("", $bsize, "6");   // %36
      $opad = str_pad("", $bsize, "\\");  // %5C
      
      #-- call hash func    // php can XOR strings for us
      $dgst = pack("H*",  $func(  ($key ^ $ipad)  .  $text  ));  // (2,3,4)
      $dgst = pack("H*",  $func(  ($key ^ $opad)  .  $dgst  ));  // (5,6,7)
      return($dgst);
   }
   
   #-- return which hash functions are implemented
   function mhash_count() {
      return(MHASH_SHA1);
   }
   
   #-- map numeric identifier to hash function name
   function mhash_get_hash_name($i) {
      static $hash_funcs = array(
          MHASH_CRC32 => "crc32",   // would need dechex()ing in main func?
          MHASH_MD5 => "md5",
          MHASH_SHA1 => "sha1",
      );
      return(strtoupper($hash_funcs[$i]));
   }
   
   #-- static value
   function mhash_get_block_size($i) {
      return(64);
   }
}





/**
 *
 * @group REMOVED_STUFF
 * @since unknown
 * @until unknown
 *
 *
 * @emulated
 *    ...
 *
 * @missing
 *    leak
 *
 */





/**
 *
 * group PRE_4_1
 * since 4.0
 * since 3.0
 *
 *
 * @emulated
 *    ...
 *
 *
 * No need to implement anything below that, because such old versions
 * will be incompatbile anyhow (- none of the newer superglobals known).
 *
 * but see also "ext/old"
 *
 */


?>