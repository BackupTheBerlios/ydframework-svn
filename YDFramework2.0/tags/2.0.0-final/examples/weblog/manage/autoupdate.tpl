{include file="__mng_header.tpl"}

<p class="title">{t w="a_admin_home"} &raquo; {t w="a_version_info"}</p>

<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th class="adminRowLG" colspan="2">&raquo; {t w="version_check"}</th>
    </tr>
    <tr>
        <td class="adminRowL"><iframe src="{$YD_SELF_SCRIPT}?do=check" width="100%" height="1600" scrolling="auto" frameborder="0" /></td>
    </tr>
</table>

{include file="__mng_footer.tpl"}
