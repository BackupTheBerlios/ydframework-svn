<?php
/*
   These functions emulate the "character type" extension, which is
   present in PHP first since version 4.3 per default. In this variant
   only ASCII and Latin-1 characters are being handled. The first part
   is eventually faster.
*/


#-- regex variants
if (!function_exists("ctype_alnum")) {
   function ctype_alnum($text) {
      return preg_match("/^[A-Za-z\d\300-\377]+$/", $text);
   }
   function ctype_alpha($text) {
      return preg_match("/^[a-zA-Z\300-\377]+$/", $text);
   }
   function ctype_digit($text) {
      return preg_match("/^\d+$/", $text);
   }
   function ctype_xdigit($text) {
      return preg_match("/^[a-fA-F0-9]+$/", $text);
   }
   function ctype_cntrl($text) {
      return preg_match("/^[\000-\037]+$/", $text);
   }
   function ctype_space($text) {
      return preg_match("/^\s+$/", $text);
   }
   function ctype_upper($text) {
      return preg_match("/^[A-Z\300-\337]+$/", $text);
   }
   function ctype_lower($text) {
      return preg_match("/^[a-z\340-\377]+$/", $text);
   }
   function ctype_graph($text) {
      return preg_match("/^[\041-\176\241-\377]+$/", $text);
   }
   function ctype_punct($text) {
      return preg_match("/^[^0-9A-Za-z\000-\040\177-\240\300-\377]+$/", $text);
   }
   function ctype_print($text) {
      return ctype_punct($text) && ctype_graph($text);
   }

}


/***<old>

#-- simple char-by-char comparisions
if (!function_exists("ctype_alnum")) {


   #-- true if string is made of letters and digits only
   function ctype_alnum($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and (
                ($c>=65) && ($c<=90)    // A-Z
             or ($c>=97) && ($c<=122)   // a-z
             or ($c>=48) && ($c<=59)    // 0-9
             or ($c>=192)          // Latin-1 letters
         );
      }
      return($r);
   }


   #-- only letters in given string
   function ctype_alpha($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and (
                ($c>=65) && ($c<=90)    // A-Z
             or ($c>=97) && ($c<=122)   // a-z
             or ($c>=192)          // Latin-1 letters
         );
      }
      return($r);
   }


   #-- only numbers in string
   function ctype_digit($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and ($c>=48) && ($c<=59);   // 0-9
      }
      return($r);
   }


   #-- hexadecimal numbers only
   function ctype_xdigit($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and (
                ($c>=48) && ($c<=59)    // 0-9
             or ($c>=65) && ($c<=70)    // A-F
             or ($c>=97) && ($c<=102)   // a-f
         );
      }
      return($r);
   }


   #-- hexadecimal numbers only
   function ctype_cntrl($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and ($c < 32);
      }
      return($r);
   }


   #-- hexadecimal numbers only
   function ctype_space($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = $text{$i};
         $r = $r and (
              ($c == " ") or ($c == "\240")
           or ($c == "\n") or ($c == "\r")
           or ($c == "\t") or ($c == "\f")
         );
      }
      return($r);
   }


   #-- all-uppercase
   function ctype_upper($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and (
                ($c>=65) && ($c<=90)    // A-Z
             or ($c>=192) && ($c<=223)  // Latin-1 letters
         );
      }
      return($r);
   }


   #-- all-lowercase
   function ctype_lower($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and (
                ($c>=97) && ($c<=122)   // a-z
             or ($c>=224) && ($c<=255)  // Latin-1 letters
         );
      }
      return($r);
   }


   #-- everything except spaces that produces a valid printable output
   #   (this probably excludes contral chars as well)
   function ctype_graph($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r
           and ($c>=33)
           and ($c!=160)
           and (($c<=126) or ($c>=161));
      }
      return($r);
   }


   #-- everything printable, but no spaces+letters+digits
   function ctype_punct($text) {
      $r = true;
      for ($i=0; $i<strlen($text); $i++) {
         $c = ord($text{$i});
         $r = $r and (
                ($c>=33) && ($c<=47)   // !../
             or ($c>=58) && ($c<=64)   // :..@
             or ($c>=91) && ($c<=96)   // [..`
             or ($c>=123) && ($c<=126) // {..~
             or ($c>=161) && ($c<=191) // Latin-1 everything else
         );
      }
      return($r);
   }



//   - no idea what this means exactly
//
//   function ctype_print($text) {
//   }


}


</old>***/

?>