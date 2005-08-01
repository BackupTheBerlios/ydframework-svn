{include file="__mng_header.tpl"}

<p class="title">{t w="a_admin_home"} &raquo; {t w="latest_items"}</p>

{if $items}
    <table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th colspan="3" class="adminRowLG">&raquo; <a href="items.php"><b>{t w="a_items"}</b></a></td>
        <th class="adminRowLGR">
            <a href="items.php?do=edit"><img src="images/icon_add.gif" border="0" /></a>
            <a href="items.php?do=edit"><b>{t w="add_item"}</b></a>
        </td>
    </tr>
    <tr>
        <th class="adminRowL" width="17%">{t w="date"}</th>
        <th class="adminRowL" width="15%">{t w="name"}</th>
        <th class="adminRowL" width="26%">{t w="title"}</th>
        <th class="adminRowR" width="22%">{t w="actions"}</th>
    </tr>
    {$browsebar}
    {foreach from=$items item="item"}
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{$item.created|date:'%Y/%m/%d %H:%M'}</td>
            <td class="adminRowL">{$item.user_name}</td>
            <td class="adminRowL">{$item.title}</td>
            <td class="adminRowR">
                <a href="items.php?do=edit&id={$item.id}">{t w="edit"}</a>
                |
                <a href="items_gallery.php?id={$item.id}">{if $item.num_images > 0}{$item|@text_num_images:false}{else}<span class="disabled">0 {t w="images" lower=true}</span>{/if}</a>
                |
                <a href="items.php?do=delete&id={$item.id}"
                 onClick="return YDConfirmDelete( '{$item.title|addslashes}' );">{t w="delete"}</a>
            </td>
        </tr>
    {/foreach}
    {$browsebar}
    </table>
{/if}

<p class="title">&nbsp;<br/>{t w="a_admin_home"} &raquo; {t w="overview"}</p>

<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th class="adminRowLG">&raquo; <a href="serverinfo.php"><b>{t w="a_server_info"}</b></a></td>
        <th class="adminRowLGR"><a href="serverinfo.php"><img src="images/more_details.gif" border="0" /></a></td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL" width="300">{t w="username"}</td>
        <td class="adminRowL" width="400">{$user.name|lower}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="server"}</td>
        <td class="adminRowL">{$smarty.server.SERVER_NAME}{if $smarty.server.SERVER_PORT != '80'}:{$smarty.server.SERVER_PORT}{/if}</td>
    </tr>
</table>

<table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <th class="adminRowLG">&raquo; <a href="stats.php"><b>{t w="a_statistics"}</b></a></td>
        <th class="adminRowLGR"><a href="stats.php"><img src="images/more_details.gif" border="0" /></a></td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL" width="300">{t w="totalItems"}</td>
        <td class="adminRowL" width="400" colspan="2">{$totalItems} {t w="items"}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="totalComments"}</td>
        <td class="adminRowL" colspan="2">{$totalComments} {t w="comments" lower=true}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="total_hits"}</td>
        <td class="adminRowL" colspan="2">{$totalHits} {t w="hits"}</td>
    </tr>
    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
        <td class="adminRowL">{t w="avg_hits_per_day"}</td>
        <td class="adminRowL" colspan="2">{$avg_hitsaday} {t w="hits"}</td>
    </tr>
</table>

{include file="__mng_footer.tpl"}
