<?php
/**
 * Smarty {html_radio} function plugin
 *
 * Type:     function
 * Name:     radio
 * Purpose:  Creates a radio button
 * Input:
 *         - name = the name of the radio button
 *         - value = optional value for the checkbox
 *         - checked = boolean - whether the box is checked or not
 * Author:   Paul Lockaby <paul@paullockaby.com>
 */
function tpl_function_html_radio($params, &$tpl) {
	extract($params);

	if (!isset($name) || empty($name)) {
		$tpl->trigger_error("html_radio: missing 'name' parameter");
		return;
	}

	$html_result = "";
	$html_result = "<INPUT TYPE=\"RADIO\" NAME=\"$name\"";
	if (isset($checked) && $value == $checked)
		$html_result .= " CHECKED";
	if (isset($value))
		$html_result .= " VALUE=\"$value\"";
	$html_result .= ">";

	return $html_result;
}
?>