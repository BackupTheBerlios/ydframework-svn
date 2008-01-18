<?php
/*
   api: php
   type: class
   title: PDO
   priority: never
   status: unfinished
   
   TODO.
   
   PDO is a standardized SQL wrapper for PHP. It's meant to replace the
   plethora of mysql_/pgsql_ functions and former SQL wrappers PEARDB,
   MDB2, ADODB, ... and become what DBI is for Perl.
   
   However, PDO cannot be emulated fully, not on PHP4 especially, because
   the SPL and meta object semantics won't work there. Eventually a subset
   can be reimplemented however. (Would be more useful than ADO and PEARDB
   at least.)
   
   We'll only implement here:
   - PDO_MYSQL
*/


#-- stub
if (!function_exists()) {
   function pdo_drivers() {
      return array();
   }
}


#-- PDO
if (!class_exists("pdo")) {
   class PDO {
   }
}

?>