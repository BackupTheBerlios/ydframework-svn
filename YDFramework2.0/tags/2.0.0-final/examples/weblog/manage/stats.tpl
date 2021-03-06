{include file="__mng_header.tpl"}

{if $YD_ACTION == 'default'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="a_statistics"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="general_stats"}</th>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL" width="350">{t w="installed_since"}</td>
            <td class="adminRowL" width="350" colspan="2">{$installDate|date:"%d %B %Y"|lower}</td>
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
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="total_hits"}</td>
            <td class="adminRowL" colspan="2">{$totalHits} {t w="hits"}</td>
        </tr>
        <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
            <td class="adminRowL">{t w="avg_hits_per_day"}</td>
            <td class="adminRowL" colspan="2">{$avg_hitsaday} {t w="hits"}</td>
        </tr>
    </table>

    <table width="700" cellspacing="0" cellpadding="0" border="0">

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th class="adminRowLG">&raquo; {t w="hits_last_6_months"}</th>
            <th colspan="2" class="adminRowLGR">
                <a href="{$YD_SELF_SCRIPT}?do=showMonths"><img src="images/more_details.gif" border="0" /></a>
                <a href="{$YD_SELF_SCRIPT}?do=showMonths"><b>{t w="all_months"}</b></a>
            </th>
        </tr>
        {foreach from=$last6Months item="last6Month"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" width="200">{$last6Month.yearmonth}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$last6Month.hits_pct}</td>
                <td class="adminRowR" width="100">{$last6Month.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th class="adminRowLG">&raquo; {t w="hits_last_7_days"}</th>
            <th colspan="2" class="adminRowLGR">
                <a href="{$YD_SELF_SCRIPT}?do=showDays"><img src="images/more_details.gif" border="0" /></a>
                <a href="{$YD_SELF_SCRIPT}?do=showDays"><b>{t w="all_days"}</b></a>
            </th>
        </tr>
        {foreach from=$last7Days item="last7Day"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$last7Day.date}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$last7Day.hits_pct}</td>
                <td class="adminRowR">{$last7Day.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th class="adminRowLG">&raquo; {t w="top_10_urls"}</th>
            <th colspan="2" class="adminRowLGR">
                <a href="{$YD_SELF_SCRIPT}?do=showUrls"><img src="images/more_details.gif" border="0" /></a>
                <a href="{$YD_SELF_SCRIPT}?do=showUrls"><b>{t w="all_urls"}</b></a>
            </th>
        </tr>
        {foreach from=$top10Urls item="top10Url"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">
                    <a href="../{$top10Url.uri}" target="_blank">{$top10Url.uri}</a>
                </td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$top10Url.hits_pct}</td>
                <td class="adminRowR">{$top10Url.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th class="adminRowLG">&raquo; {t w="top_10_commenters"}</th>
            <th colspan="2" class="adminRowLGR">
                <a href="{$YD_SELF_SCRIPT}?do=showCommenters"><img src="images/more_details.gif" border="0" /></a>
                <a href="{$YD_SELF_SCRIPT}?do=showCommenters"><b>{t w="all_commenters"}</b></a>
            </th>
        </tr>
        {foreach from=$commentStats item="commenter"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$commenter.username}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$commenter.hits_pct}</td>
                <td class="adminRowR">{$commenter.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="web_browsers"}</th>
        </tr>
        {foreach from=$browserStats item="browserStat"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$browserStat.browser}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$browserStat.hits_pct}</td>
                <td class="adminRowR" width="100">{$browserStat.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}

        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <th colspan="3" class="adminRowLG">&raquo; {t w="operating_systems"}</th>
        </tr>
        {foreach from=$osStats item="osStat"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$osStat.platform}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$osStat.hits_pct}</td>
                <td class="adminRowR" width="100">{$osStat.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}

    </table>

{/if}

{if $YD_ACTION == 'showmonths'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_months"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="2" class="adminRowLG">&raquo; {t w="all_months"}</th>
            <th class="adminRowLGR">&raquo; <a href="stats.php"><b>{t w="back"}</b></a></th>
        </tr>
        {foreach from=$months item="month"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" width="200">{$month.yearmonth}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$month.hits_pct}</td>
                <td class="adminRowR" width="100">{$month.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}
    </table>

{/if}

{if $YD_ACTION == 'showdays'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_days"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="2" class="adminRowLG">&raquo; {t w="all_days"}</th>
            <th class="adminRowLGR">&raquo; <a href="stats.php"><b>{t w="back"}</b></a></th>
        </tr>
        {foreach from=$days item="day"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" width="200">{$day.date}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$day.hits_pct}</td>
                <td class="adminRowR" width="100">{$day.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}
    </table>

{/if}

{if $YD_ACTION == 'showurls'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_urls"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="2" class="adminRowLG">&raquo; {t w="all_urls"}</th>
             <th class="adminRowLGR">&raquo; <a href="stats.php"><b>{t w="back"}</b></a></th>
       </tr>
        {foreach from=$urls item="url"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" width="200"><a href="../{$url.uri}" target="_blank">{$url.uri}</a>   </td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$url.hits_pct}</td>
                <td class="adminRowR" width="100">{$url.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}
    </table>

{/if}

{if $YD_ACTION == 'showcommenters'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_commenters"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="2" class="adminRowLG">&raquo; {t w="all_commenters"}</th>
            <th class="adminRowLGR">&raquo; <a href="stats.php"><b>{t w="back"}</b></a></th>
        </tr>
        {foreach from=$commenters item="commenter"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" width="200">{$commenter.username}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$commenter.hits_pct}</td>
                <td class="adminRowR" width="100">{$commenter.hits}</td>
            </tr>
        {foreachelse}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL" colspan="3">{t w="nothing_found"}</td>
            </tr>
        {/foreach}
    </table>

{/if}

{include file="__mng_footer.tpl"}
