<?php
/**
 * Smarty-Light {html_input} function plugin
 *
 * Type:     function
 * Name:     html_input
 * Purpose:  Creates an input text or password box
 * Input:
 *         - name = the name of the textbox
 *         - password = boolean - if set, this box will be a
 *                      password box
 *         - value = optional default value for the input box
 *         - size = optional size for the input box
 *         - length = optional maxlength for the input box
 * Author:   Paul Lockaby <paul@paullockaby.com>
 */
function tpl_function_html_input($params, &$tpl) {
	extract($params);

	if (!isset($name) || empty($name)) {
		$tpl->trigger_error("html_input: missing 'name' parameter");
		return;
	}

	$html_result = "";

	$html_result = "<INPUT TYPE=";
	if ($password == true)
		$html_result .= "\"PASSWORD\"";
	else
		$html_result .= "\"TEXT\"";
	if (isset($size))
		$html_result .= " SIZE=\"$size\"";
	if (isset($length))
		$html_result .= " MAXLENGTH=\"$length\"";
	
	if (isset($value))
		$html_result .= " VALUE=\"".htmlspecialchars($value)."\"";
	else
		$html_result .= " VALUE=\"\"";
	$html_result .= " NAME=\"$name\">";
	
	return $html_result;
}
?>