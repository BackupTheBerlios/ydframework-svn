<?php
/**
 * Smarty {html_textbox} function plugin
 *
 * Type:     function
 * Name:     html_textbox
 * Purpose:  Creates a textbox
 * Input:
 *         - name = the name of the textbox
 *         - rows = optional number of rows in the textbox
 *         - columns = optional number of columns in the textbox
 *         - value = optional preset value to put in the textbox
 * Autho:    Paul Lockaby <paul@paullockaby.com>
 */
function tpl_function_html_textbox($params, &$tpl) {
	extract($params);

	if (!isset($name) || empty($name)) {
		$tpl->trigger_error("html_textbox: missing 'name' parameter");
		return;
	}

	$html_result = "";

	$html_result = "<TEXTAREA NAME=\"$name\"";
	if (isset($rows))
		$html_result .= " ROWS=\"$rows\"";
	if (isset($columns))
		$html_result .= " COLS=\"$columns\"";
	
	$html_result .= ">";

	if (isset($value))
		$html_result .= $value;

	$html_result .= "</TEXTAREA>";
	
	return $html_result;
}
?>