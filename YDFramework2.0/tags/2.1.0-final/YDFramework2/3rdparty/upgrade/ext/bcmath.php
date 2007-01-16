<?php
/*
   Emulates mathematical functions with arbitrary precision (bcmath)
   using POSIX systems 'dc' utility or either GMP or PHPs bigint
   extension module as fallback (were faster, but wouldn't allow to
   set precisions).
*/


#-- BSD/Linux dc(1)
if (!function_exists("bcadd") && is_executable("/usr/bin/dc")) {

   #-- invokes commandline 'dc' utility (faster than with 'bc')
   #   (later version should use proc_open() for safer and faster bi-directional I/O)
   function dc___exec($calc, $scale=NULL) {
      global $bc___scale;

      #-- assemble dc expression
      $calc = str_replace(' -', ' _', $calc);  // convert minus signs for dc
      if (isset($scale) || ($scale = $bc___scale)) {
         $calc = ((int)$scale) . "k" . $calc;  // inject precision directive
      }
      $calc = escapeshellarg($calc);   // could be non-integer from elsewhere
      
      #-- prevent any command execution from within dc
      # (for speed reasons we don't assert parameters to be fully numeric)
      if (strpos($calc, "!")) { return; }

      #-- do
      return str_replace("\\\n", "", `/usr/bin/dc -e $calc`);
   }

   #-- global state variable
   $GLOBALS["bc___scale"] = 0;  //ini_get("bcmath.scale");  // =0
   function bcscale($scale=NULL) {
      $GLOBALS["bc___scale"] = $scale;
   }

   #-- wrapper calls
   function bcadd($a, $b, $scale=NULL) {
      return dc___exec(" $a $b +nq", $scale);
   }
   function bcsub($a, $b, $scale=NULL) {
      return dc___exec(" $a $b-nq", $scale);  // no space before '-' cmd!
   }
   function bcmul($a, $b, $scale=NULL) {
      return dc___exec(" $a $b *nq", $scale);
   }
   function bcdiv($a, $b, $scale=NULL) {
      return dc___exec(" $a $b /nq", $scale);
   }
   function bcmod($a, $b, $scale=0) {
      return dc___exec(" $a $b %nq", $scale);
   }
   function bcpow($a, $b, $scale=NULL) {
      return dc___exec(" $a $b ^nq", $scale);
   }
   function bcpowmod($x, $y, $mod, $scale=0) {
      return dc___exec(" $x $y $mod |nq", $scale);  // bc(1) wouldn't work
   }
   function bcsqrt($x, $scale=NULL) {
      return dc___exec(" $x vnq", $scale);
   }

   #-- looks slightly more complicated in dc notation
   function bccomp($a, $b, $scale=NULL) {
      bc___scaledown($a, $scale);
      bc___scaledown($b, $scale);
      return (int) dc_exec(" $a 1*sA $b 1*sB  lBlA[1nq]sX>X lBlA[_1nq]sX<X 0nq", $scale);
   }
   function bc___scaledown(&$a, $scale) {
      if (isset($scale) && ($dot = strpos($a, $dot))) {
         $a = substr($a, $dot + $scale) . "0";
      }
   }

}//shell version



#-- GMP
if (!function_exists("bcadd") && function_exists("gmp_strval")) {
   function bcadd($a, $b) {
      return gmp_strval(gmp_add($a, $b));
   }
   function bcsub($a, $b) {
      return gmp_strval(gmp_sub($a, $b));
   }
   function bcmul($a, $b) {
      return gmp_strval(gmp_mul($a, $b));
   }
   function bcdiv($a, $b, $precision=NULL) {
      $qr = gmp_div_qr($a, $b);
      $q = gmp_strval($qr[0]);
      $r = gmp_strval($qr[1]);
      if ((!$r) || ($precision===0)) {
         return($q);
      }
      else {
         if (isset($precision)) {
            $r = substr($r, 0, $precision);
         }
         return("$q.$r");
      }
   }
   function bcmod($a, $b) {
      return gmp_strval(gmp_mod($a, $b));
   }
   function bcpow($a, $b) {
      return gmp_strval(gmp_pow($a, $b));
   }
   function bcpowmod($x, $y, $mod) {
      return gmp_strval(gmp_powm($x, $y, $mod));
   }
   function bcsqrt($x) {
      return gmp_strval(gmp_sqrt($x));
   }
   function bccomp($a, $b) {
      return gmp_cmp($a, $b);
   }
   function bcscale($scale="IGNORED") {
      trigger_error("bcscale(): ignored", E_USER_ERROR);
   }
}//gmp emulation



#-- bigint
// @dl("php_big_int".PHP_SHLIB_SUFFIX))
if (!function_exists("bcadd") && function_exists("bi_serialize")) {
   function bcadd($a, $b) {
      return bi_to_str(bi_add($a, $b));
   }
   function bcsub($a, $b) {
      return bi_to_str(bi_sub($a, $b));
   }
   function bcmul($a, $b) {
      return bi_to_str(bi_mul($a, $b));
   }
   function bcdiv($a, $b) {
      return bi_to_str(bi_div($a, $b));
   }
   function bcmod($a, $b) {
      return bi_to_str(bi_mod($a, $b));
   }
   function bcpow($a, $b) {
      return bi_to_str(bi_pow($a, $b));
   }
   function bcpowmod($a, $b, $c) {
      return bi_to_str(bi_powmod($a, $b, $c));
   }
   function bcsqrt($a) {
      return bi_to_str(bi_sqrt($a));
   }
   function bccomp($a, $b) {
      return bi_cmp($a, $b);
   }
   function bcscale($scale="IGNORED") {
      trigger_error("bcscale(): ignored", E_USER_ERROR);
   }
}


?>