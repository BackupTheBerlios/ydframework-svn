<?php
/**
 * Smarty {html_checkbox} function plugin
 *
 * Type:     function
 * Name:     textbox
 * Purpose:  Creates a checkbox
 * Input:
 *         - name = the name of the checkbox
 *         - value = optional value for the checkbox
 *         - checked = boolean - whether the box is checked or not
 * Author:   Paul Lockaby <paul@paullockaby.com>
 */
function tpl_function_html_checkbox($params, &$tpl) {
	extract($params);

	if (!isset($name) || empty($name)) {
		$tpl->trigger_error("html_checkbox: missing 'name' parameter");
		return;
	}

	$html_result = "";
	$html_result = "<INPUT TYPE=\"CHECKBOX\" NAME=\"" . $name . "\"";
	if ((isset($checked) && $value == $checked) || ($checked == "true" || $checked == "yes" || $checked == "on"))
		$html_result .= " CHECKED";
	if (isset($value))
		$html_result .= " VALUE=\"$value\"";
	$html_result .= ">";

	return $html_result;
}
?>