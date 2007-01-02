{include file="__mng_header.tpl"}

<p class="title">{t w="h_statistics"} &raquo; {t w="a_bad_behavior"}</p>

{if $YD_ACTION == 'default'}

    <table width="700" cellspacing="0" cellpadding="0" border="0">
        {if $requests}
            <tr>
                <th class="adminRowLG" colspan="7">
                    &raquo; {t w="total_bad_requests_stopped"}
                </th>
            </tr>
            <tr><td colspan="7" class="adminRowL">{$requests_count} {t w="requests"}</td></tr>
            {if $requests->set}
                <tr><td colspan="7">&nbsp;</td></tr>
                <tr>
                    <th class="adminRowLG" colspan="7">
                        &raquo; {t w="post_requests"}
                    </th>
                </tr>
                <tr>
                    <th class="adminRowL">{t w="date"}</th>
                    <th class="adminRowL">{t w="ip"}</th>
                    <th class="adminRowL">{t w="request_method"}</th>
                    <th class="adminRowL">{t w="url"}</th>
                    <th class="adminRowR">{t w="actions"}</th>
                </tr>
                <tr><td class="adminRowR" colspan="5">{$requests->getBrowseBar()}</td></tr>
                {foreach from=$requests->set item="request"}
                    <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                        <td class="adminRowL">{$request.date}</td>
                        <td class="adminRowL">{$request.ip}</td>
                        <td class="adminRowL">{$request.request_method}</td>
                        <td class="adminRowL">{$request.request_uri}</td>
                        <td class="adminRowR"><a href="{$YD_SELF_SCRIPT}?id={$request.id}">{t w="view"}</a></td>
                    </tr>
                {/foreach}
                <tr><td class="adminRowR" colspan="5">{$requests->getBrowseBar()}</td></tr>
                <tr>
                    <td class="adminRowLNB" colspan="5">
                        <p class="subline">{t w="total"}: {$requests->totalRows}</p>
                    </td>
                </tr>
            {/if}
        {else}
            <tr>
                <th class="adminRowLG">
                    &raquo; {t w="request_details"}: {$request.id}
                </th>
                <th class="adminRowLGR">
                    &raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a>
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="250">{t w="id"}</td>
                <td class="adminRowL" width="450">{$request.id}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="ip"}</td>
                <td class="adminRowL">{$request.ip}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="date"}</td>
                <td class="adminRowL">{$request.date}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="request_method"}</td>
                <td class="adminRowL">{$request.request_method}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="url"}</td>
                <td class="adminRowL">{$request.request_uri}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="server_protocol"}</td>
                <td class="adminRowL">{$request.server_protocol}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="http_headers"}</td>
                <td class="adminRowL">{$request.http_headers|nl2br}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="user_agent"}</td>
                <td class="adminRowL">{$request.user_agent}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="request_entity"}</td>
                <td class="adminRowL">{$request.request_entity|nl2br}</td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="key"}</td>
                <td class="adminRowL">{$request.key}</td>
            </tr>
        {/if}
    </table>

{/if}

{include file="__mng_footer.tpl"}
