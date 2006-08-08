<?php

include_once dirname( __FILE__ ) . '/../YDWeblog_init.php';

if ( ! isset( $_GET['lang'] ) ) {
    trigger_error( 'Missing language to check. Add "lang" var to the query string.', E_USER_ERROR );
}

$file = 'language_' . $_GET['lang'] . '.php';

if ( ! file_exists( dirname( __FILE__ ) . '/' . $file ) ) {
    trigger_error( 'Language file "' . $file . '" doesn\'t exist.', E_USER_ERROR );
}

include 'language_en.php';

$en = $GLOBALS['t'];
unset( $GLOBALS['t'] );

include $file;

echo '<html>';
echo '<body>';
echo '<h2>MISSING TRANSLATIONS</h2>';
echo '<dl>';

foreach ( $en as $key => $str ) {
    if ( ! isset( $GLOBALS['t'][ $key ] ) ) {
        echo '<dt style="font-face: Verdana, Arial; font-size: 11pt"><b>' . $key . '</b></dt>';
        echo '<dd style="font-face: Verdana, Arial; font-size: 11pt; padding-bottom: 8px">' . $str . '</dd>';
    }
}

echo '</dl>';
echo '<h2>DEPRECATED TRANSLATIONS</h2>';
echo '<dl>';

foreach ( $GLOBALS['t'] as $key => $str ) {
    if ( ! isset( $en[ $key ] ) ) {
        echo '<dt style="font-face: Verdana, Arial; font-size: 11pt"><b>' . $key . '</b></dt>';
        echo '<dd style="font-face: Verdana, Arial; font-size: 11pt; padding-bottom: 8px">' . $str . '</dd>';
    }
}

echo '</dl>';
echo '</body>';
echo '</html>';

?>
