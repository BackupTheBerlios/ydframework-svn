<?php

$PHPTHUMB_CONFIG['cache_directory'] = '';
if (phpthumb_functions::version_compare_replacement(phpversion(), '4.3.2', '>=') && !defined('memory_get_usage')) {
	$PHPTHUMB_CONFIG['max_source_pixels'] = 0;
} else {
	$PHPTHUMB_CONFIG['max_source_pixels'] = round(max(intval(ini_get('memory_limit')), intval(get_cfg_var('memory_limit'))) * 1048576 * 0.20);
}

$PHPTHUMB_CONFIG['imagemagick_path'] = null;

$PHPTHUMB_CONFIG['output_format']    = 'jpeg';
$PHPTHUMB_CONFIG['output_maxwidth']  = 0;
$PHPTHUMB_CONFIG['output_maxheight'] = 0;

$PHPTHUMB_CONFIG['error_message_image_default'] = '';
$PHPTHUMB_CONFIG['error_bgcolor']               = 'CCCCFF';
$PHPTHUMB_CONFIG['error_textcolor']             = 'FF0000';
$PHPTHUMB_CONFIG['error_fontsize']              = 1;

$PHPTHUMB_CONFIG['nohotlink_enabled']       = true;
$PHPTHUMB_CONFIG['nohotlink_valid_domains'] = array(@$_SERVER['HTTP_HOST']);
$PHPTHUMB_CONFIG['nohotlink_erase_image']   = true;
$PHPTHUMB_CONFIG['nohotlink_fill_hexcolor'] = 'CCCCCC';
$PHPTHUMB_CONFIG['nohotlink_text_hexcolor'] = 'FF0000';
$PHPTHUMB_CONFIG['nohotlink_text_message']  = 'Hotlinking is not allowed!';
$PHPTHUMB_CONFIG['nohotlink_text_fontsize'] = 3;

$PHPTHUMB_CONFIG['border_hexcolor']     = '000000';
$PHPTHUMB_CONFIG['background_hexcolor'] = 'FFFFFF';


$PHPTHUMB_CONFIG['use_exif_thumbnail_for_speed'] = true;

$PHPTHUMB_CONFIG['output_allow_enlarging'] = (isset($_REQUEST['aoe']) ? (bool) $_REQUEST['aoe'] : false);

?>