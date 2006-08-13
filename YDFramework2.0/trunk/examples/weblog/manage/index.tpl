{include file="__mng_header.tpl"}

<p class="title">{t w="a_admin_home"} &raquo; {t w="latest_items"}</p>

{if $items}
    <table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th colspan="3" class="adminRowLG">&raquo; <a href="items.php"><b>{t w="a_items"}</b></a></th>
        <th class="adminRowLGR">
            <a href="items.php?do=edit"><img src="images/icon_add.gif" border="0" /></a>
            <a href="items.php?do=edit"><b>{t w="add_item"}</b></a>
        </th>
    </tr>
    <tr>
        <th class="adminRowL" width="17%">{t w="date"}</th>
        <th class="adminRowL" width="15%">{t w="author"}</th>
        <th class="adminRowL" width="26%">{t w="title"}</th>
        <th class="adminRowR" width="22%">{t w="actions"}</th>
    </tr>
    {foreach from=$items item="item"}
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);" {if $item.is_draft eq '1'}style="color: gray"{/if}>
            <td class="adminRowL">
                {if $item.is_draft eq '1'}<i>{/if}
                {$item.created|date:'%Y/%m/%d %H:%M'}
                {if $item.is_draft eq '1'}</i>{/if}
            </td>
            <td class="adminRowL">
                {if $item.is_draft eq '1'}<i>{/if}
                {$item.user_name}
                {if $item.is_draft eq '1'}</i>{/if}
            </td>
            <td class="adminRowL">
                {if $item.is_draft eq '1'}<i>{/if}
                <a href="items.php?do=edit&id={$item.id}">{$item.title}</a>
                <br/>
                {if $item.allow_comments eq '0'}
                    {t w="closed"}{if $item.is_draft eq '1'}, {/if}
                {/if}
                {if $item.is_draft eq '1'}{t w="draft"}{/if}
                {if $item.is_draft eq '1'}</i>{/if}
            </td>
            <td class="adminRowR">
                <a href="../item.php?id={$item.id}" target="_blank">{t w="view"}</a>
                |
                <a href="items.php?do=edit&id={$item.id}">{t w="edit"}</a>
                |
                <a href="items.php?do=delete&id={$item.id}"
                 onClick="return YDConfirmDelete( '{$item.title|addslashes}' );">{t w="delete"}</a>
                <br/>
                <a href="comments.php?id={$item.id}">{if $item.num_comments > 0}{$item|@text_num_comments:false}{else}<span class="disabled">0 {t w="comments" lower=true}</span>{/if}</a> |
                <a href="items_gallery.php?id={$item.id}">{if $item.num_images > 0}{$item|@text_num_images:false}{else}<span class="disabled">0 {t w="images" lower=true}</span>{/if}</a>
            </td>
        </tr>
    {/foreach}
    </table>
{/if}

<p class="title">&nbsp;<br/>{t w="a_admin_home"} &raquo; {t w="overview"}</p>

<table width="700" cellspacing="0" cellpadding="0" border="0">
<tr><td width="350" align="left" valign="top">

    <table width="340" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowLG">&raquo; <a href="serverinfo.php"><b>{t w="a_server_info"}</b></a></th>
            <th class="adminRowLGR" colspan="2"><a href="serverinfo.php"><img src="images/more_details.gif" border="0" /></a></th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="username"}</td>
            <td class="adminRowL" width="400" colspan="2">{$user.name|lower}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="server"}</td>
            <td class="adminRowL" colspan="2">{$smarty.server.SERVER_NAME}{if $smarty.server.SERVER_PORT != '80'}:{$smarty.server.SERVER_PORT}{/if}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="installed_version"}</td>
            <td class="adminRowL" colspan="2">{$YD_FW_REVISION}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="300">{t w="installed_since"}</td>
            <td class="adminRowL" width="400" colspan="2">{$installDate|date:"%d %B %Y"|lower}</td>
        </tr>
    </table>
</td>
<td width="350" align="right" valign="top">
    <table width="340" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowLG">&raquo; <a href="stats.php"><b>{t w="a_statistics"}</b></a></th>
            <th class="adminRowLGR"><a href="stats.php"><img src="images/more_details.gif" border="0" /></a></th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="num_days_online"}</td>
            <td class="adminRowL" colspan="2">{$daysOnline} {t w="days"}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="totalItems"}</td>
            <td class="adminRowL" colspan="2">{$totalItems} {t w="items"}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="totalComments"}</td>
            <td class="adminRowL" colspan="2">{$totalComments} {t w="comments" lower=true}</td>
        </tr>
        {if $keep_stats}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{t w="total_hits"}</td>
                <td class="adminRowL" colspan="2">{$totalHits} {t w="hits"}</td>
            </tr>
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{t w="avg_hits_per_day"}</td>
                <td class="adminRowL" colspan="2">{$avg_hitsaday} {t w="hits"}</td>
            </tr>
        {/if}
    </table>
</td></tr>
</table>

{include file="__mng_footer.tpl"}
