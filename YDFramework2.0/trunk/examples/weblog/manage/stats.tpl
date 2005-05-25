{include file="__mng_header.tpl"}

{if $YD_ACTION == 'default'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="a_statistics"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="3" class="adminRowL">{t w="general_stats"}</th>
        </tr>
        <tr>
            <td class="adminRowL" width="350">{t w="installed_since"}</td>
            <td class="adminRowL" width="350" colspan="2">{$installDate|date:"%d %B %Y"|lower}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="num_days_online"}</td>
            <td class="adminRowL" colspan="2">{$daysOnline} {t w="days"}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="totalItems"}</td>
            <td class="adminRowL" colspan="2">{$totalItems} {t w="items"}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="totalComments"}</td>
            <td class="adminRowL" colspan="2">{$totalComments} {t w="comments" lower=true}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="total_hits"}</td>
            <td class="adminRowL" colspan="2">{$totalHits} {t w="hits"}</td>
        </tr>
        <tr>
            <td class="adminRowL">{t w="avg_hits_per_day"}</td>
            <td class="adminRowL" colspan="2">{$avg_hitsaday} {t w="hits"}</td>
        </tr>
    </table>

    <table width="700" cellspacing="0" cellpadding="0" border="0">

        <tr>
            <th colspan="3" class="adminRowL">
                &nbsp;<br/>{t w="hits_last_6_months"} (<a href="{$YD_SELF_SCRIPT}?do=showMonths"><b>{t w="all_months"}</b></a>)
            </th>
        </tr>
        {foreach from=$last6Months item="last6Month"}
            <tr>
                <td class="adminRowL" width="200">{$last6Month.yearmonth}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$last6Month.hits_pct}</td>
                <td class="adminRowR" width="100">{$last6Month.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}

        <tr>
            <th colspan="3" class="adminRowL">
                &nbsp;<br/>{t w="hits_last_7_days"} (<a href="{$YD_SELF_SCRIPT}?do=showDays"><b>{t w="all_days"}</b></a>)
            </th>
        </tr>
        {foreach from=$last7Days item="last7Day"}
            <tr>
                <td class="adminRowL">{$last7Day.date}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$last7Day.hits_pct}</td>
                <td class="adminRowR">{$last7Day.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}

        <tr>
            <th colspan="3" class="adminRowL">
                &nbsp;<br/>{t w="top_10_urls"} (<a href="{$YD_SELF_SCRIPT}?do=showUrls"><b>{t w="all_urls"}</b></a>)
            </th>
        </tr>
        {foreach from=$top10Urls item="top10Url"}
            <tr>
                <td class="adminRowL">
                    <a href="../{$top10Url.uri}" target="_blank">{$top10Url.uri}</a>
                </td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$top10Url.hits_pct}</td>
                <td class="adminRowR">{$top10Url.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}

        <tr>
            <th colspan="3" class="adminRowL">&nbsp;<br/>{t w="web_browsers"}</th>
        </tr>
        {foreach from=$browserStats item="browserStat"}
            <tr>
                <td class="adminRowL">{$browserStat.browser}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$browserStat.hits_pct}</td>
                <td class="adminRowR" width="100">{$browserStat.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}

        <tr>
            <th colspan="3" class="adminRowL">&nbsp;<br/>{t w="operating_systems"}</th>
        </tr>
        {foreach from=$osStats item="osStat"}
            <tr>
                <td class="adminRowL">{$osStat.platform}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$osStat.hits_pct}</td>
                <td class="adminRowR" width="100">{$osStat.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}

    </table>

{/if}

{if $YD_ACTION == 'showmonths'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_months"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="3" class="adminRowL">{t w="all_months"}</th>
        </tr>
        {foreach from=$months item="month"}
            <tr>
                <td class="adminRowL" width="200">{$month.yearmonth}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$month.hits_pct}</td>
                <td class="adminRowR" width="100">{$month.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}
    </table>

{/if}

{if $YD_ACTION == 'showdays'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_days"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="3" class="adminRowL">{t w="all_days"}</th>
        </tr>
        {foreach from=$days item="day"}
            <tr>
                <td class="adminRowL" width="200">{$day.date}</td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$day.hits_pct}</td>
                <td class="adminRowR" width="100">{$day.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}
    </table>

{/if}

{if $YD_ACTION == 'showurls'}

    <p class="title">{t w="h_statistics"} &raquo; {t w="all_urls"}</p>

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th colspan="3" class="adminRowL">{t w="all_urls"}</th>
        </tr>
        {foreach from=$urls item="url"}
            <tr>
                <td class="adminRowL" width="200"><a href="{$url.uri}" target="_blank">{$url.uri}</a>   </td>
                <td class="adminRowL" style="vertical-align: middle;">{graph width=$url.hits_pct}</td>
                <td class="adminRowR" width="100">{$url.hits}</td>
            </tr>
        {foreachelse}
            <tr><td class="adminRowL" colspan="3">{t w="nothing_found"}</td></tr>
        {/foreach}
    </table>

{/if}

{include file="__mng_footer.tpl"}
