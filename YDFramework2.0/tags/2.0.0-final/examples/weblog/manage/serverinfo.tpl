{include file="__mng_header.tpl"}

<p class="title">{t w="h_maintenance"} &raquo; {t w="a_server_info"}</p>

<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th colspan="3" class="adminRowLG">&raquo; {t w="a_server_info"}</th>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL" width="300">{t w="system"}</td>
        <td class="adminRowL" width="400">{$system}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="server_name"}</td>
        <td class="adminRowL">{$smarty.server.SERVER_NAME|default:'unknown'}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="server_address"}</td>
        <td class="adminRowL">{$smarty.server.SERVER_ADDR|default:'unknown'}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="server_port"}</td>
        <td class="adminRowL">{$smarty.server.SERVER_PORT|default:'80'}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="server_software"}</td>
        <td class="adminRowL">{$smarty.server.SERVER_SOFTWARE|default:'unknown'}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="operating_system"}</td>
        <td class="adminRowL">{$smarty.env.OS|default:$PHP_OS}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">MySQL {t w="version"}</td>
        <td class="adminRowL">MySQL {$mysql_version}</td>
    </tr>
</table>
<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <th colspan="3" class="adminRowLG">&raquo; {$YD_FW_NAME}</th>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL" width="300">{$YD_FW_NAME}</td>
        <td class="adminRowL" width="400">{$YD_FW_NAMEVERS}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">Smarty</td>
        <td class="adminRowL">Smarty {$smarty.version}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">phpMailer</td>
        <td class="adminRowL">phpMailer {$phpmailer_version}</td>
    </tr>
</table>
<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <th class="adminRowLG">&raquo; PHP</th>
        <th class="adminRowLGR">
            <a href="{$YD_SELF_SCRIPT}?do=phpinfo"><img src="images/more_details.gif" border="0" /></a>
            <a href="{$YD_SELF_SCRIPT}?do=phpinfo"><b>{t w="full_php_info"}</b></a>
        </th>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL" width="300"><p>PHP {t w="version"}</td>
        <td class="adminRowL" width="400"><p>{$php_version}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="server_api"}</td>
        <td class="adminRowL">{$server_api}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="loaded_modules"}</td>
        <td class="adminRowL">{$php_modules}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="registered_streams"}</td>
        <td class="adminRowL">{$registered_php_streams}</td>
    </tr>
</table>
<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <th class="adminRowLG" width="700">&raquo; PHP {t w="include_path"}</th>
    </tr>
    {foreach from=$includePath key="num" item="path"}
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" colspan="2">[{$num}] {$path}</td>
        </tr>
    {/foreach}
</table>

{include file="__mng_footer.tpl"}
