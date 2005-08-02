{include file="__mng_header.tpl"}

<p class="title">{t w="a_admin_home"} &raquo; {t w="a_version_info"}</p>

<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th class="adminRowLG" colspan="2">&raquo; {t w="version_check"}</th>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL" width="50%">{t w="installed_version"}</td>
        <td class="adminRowL" width="50%">{$YD_FW_REVISION}</td>
    </tr>
    {if $changelog}
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="50%">{t w="development_version"}</td>
            <td class="adminRowL" width="50%">{$changelog.0.revision}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="version_check"}</td>
            <td class="adminRowL">
                {if $changelog.0.revision == $YD_FW_REVISION}
                    <font color="green">{t w="msg_correct_version"}</font>
                {else}
                    <a href="{$YD_FW_HOMEPAGE}" target="_blank"><font color="red">{t w="msg_install_update"}</font></a>
                {/if}
            </td>
        </tr>
    {else}
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" colspan="2">{t w="error_version_check"}</td>
        </tr>
    {/if}
</table>

{if $changelog}
    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th class="adminRowLG" colspan="4">&raquo; {t w="development_changelog"}</th>
        </tr>
        {foreach from=$changelog item="clitem"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" width="10%">{$clitem.revision}</td>
                <td class="adminRowL" width="15%">{$clitem.author}</td>
                <td class="adminRowL" width="15%">{$clitem.date}</td>
                <td class="adminRowL" width="60%">{$clitem.msg}</td>
            </tr>
        {/foreach}
    </table>
{/if}

{include file="__mng_footer.tpl"}
