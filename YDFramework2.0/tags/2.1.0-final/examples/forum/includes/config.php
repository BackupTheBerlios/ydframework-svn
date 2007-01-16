<?php
/*
 * Configuration file for the Friendly Freddy Forum
 * 
 */

/*
 * rights levels, should be remove with db groups and replaced by roles
 * */
define('LEVEL_ADMIN', 5);
define('LEVEL_MODERATOR', 3);
define('LEVEL_USER', 1);
define('LEVEL_ANONYMOUS', 0);

define('GROUP_SIMPLEUSER_ID', 1);
define('GROUP_INACTIVEUSER_ID', 4);


define('DB_HOST', 'localhost');
define('DB_BASE', 'fff_forum');
define('DB_USER', 'root');
define('DB_PASS', '');

define('DB_PREXIX', 'fff_');

define('SECONDS_BETWEEN_POSTS', 20); // number of seconds before the next allowed post

define('NUMBER_SUBJECT_PER_PAGES', 10); // number of subjects in one page of a forum

define('YD_INIT_FILE', dirname( __FILE__ ) . '/../../../YDFramework2/YDF2_init.php');

define('LOCALE', 'en');
?>
