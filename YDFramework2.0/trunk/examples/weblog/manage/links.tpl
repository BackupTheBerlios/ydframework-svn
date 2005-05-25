{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_links"}</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $links->pages}
            <tr>
                <td class="adminRowL" colspan="1">
                    <p class="subline">{t w="total"}: {$links->totalRows}</p>
                </td>
                <td class="adminRowR" colspan="2">
                    <p class="subline">
                    &laquo;
                    {if ! $links->isFirstPage}
                        <a href="{$links->getPreviousUrl()}" class="subline">{t w="previous"}</a>
                    {else}
                        {t w="previous"}
                    {/if}
                    |
                    {foreach from=$links->pages item="page"}
                        {if $page == $links->page}
                            <b>{$page}</b>
                        {else}
                            <a href="{$links->getPageUrl($page)}" class="subline">{$page}</a>
                        {/if}
                    {/foreach}
                    |
                    {if ! $links->isLastPage}
                        <a href="{$links->getNextUrl()}" class="subline">{t w="next"}</a>
                    {else}
                        {t w="next"}
                    {/if}
                    &raquo;
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    <p><a href="{$YD_SELF_SCRIPT}?do=edit">{t w="add_link"}</a></p>

    {if $links}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="30%">{t w="link_title"}</th>
            <th class="adminRowL" width="40%">{t w="link_url"}</th>
            <th class="adminRowR" width="10%">{t w="num_visits"}</th>
            <th class="adminRowR" width="20%">{t w="actions"}</th>
        </tr>
        {foreach from=$links->set item="link"}
            <tr>
                <td class="adminRowL">{$link.title}</td>
                <td class="adminRowL"><a href="{$link.url}" target="_blank">{$link.url}</a></td>
                <td class="adminRowR">{$link.num_visits}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$link.id}">{t w="edit"}</a> |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$link.id}"
                     onClick="return YDConfirmDelete( '{$link.title}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        </table>
    {else}
        <p>{t w="no_links_found"}</p>
    {/if}

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">
                    {if $form.title.value == ''}
                        {t w="add_link"}
                    {else}
                        {t w="change_link_desc"} {$link.title}
                    {/if}
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.title.label_html}</td>
                <td class="adminRowC" width="400">{$form.title.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.url.label_html}</td>
                <td class="adminRowC">{$form.url.html}</td>
            </tr>
        </table>
        {$form.id.html}
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
