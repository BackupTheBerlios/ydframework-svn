{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_links"}</p>

{if $form.errors}
    <p><table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowELG">{t w="err_general"}</th>
        </tr>
        <tr>
            <td class="adminRowEL">
                {foreach from=$form.errors item="error"}
                    {$error}<br/>
                {/foreach}
            </td>
        </tr>
    </table></p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $links->pages}
            <tr>
                <td class="adminRowR" colspan="4">
                    <p class="subline">
                    {if ! $links->isFirstPage}
                        <a href="{$links->getPreviousUrl()}" class="subline">&laquo;</a>
                    {else}
                        &laquo;
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
                        <a href="{$links->getNextUrl()}" class="subline">&raquo;</a>
                    {else}
                        &raquo;
                    {/if}
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    <table width="700" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <th colspan="3" class="adminRowLG">&raquo; {t w="a_links"}</th>
        <th class="adminRowLGR">
            <a href="{$YD_SELF_SCRIPT}?do=edit"><img src="images/icon_add.gif" border="0" /></a>
            <a href="{$YD_SELF_SCRIPT}?do=edit"><b>{t w="add_link"}</b></a>
        </th>
    </tr>
    {if $links}
        <tr>
            <th class="adminRowL" width="30%">{t w="link_title"}</th>
            <th class="adminRowL" width="40%">{t w="link_url"}</th>
            <th class="adminRowR" width="10%">{t w="num_visits"}</th>
            <th class="adminRowR" width="20%">{t w="actions"}</th>
        </tr>
        {foreach from=$links->set item="link"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$link.title}</td>
                <td class="adminRowL"><a href="{$link.url}" target="_blank">{$link.url}</a></td>
                <td class="adminRowR">{$link.num_visits}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$link.id}">{t w="edit"}</a> |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$link.id}"
                     onClick="return YDConfirmDelete( '{$link.title|addslashes}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        <tr>
            <td class="adminRowLNB" colspan="4">
                <p class="subline">{t w="total"}: {$links->totalRows}</p>
            </td>
        </tr>
    {else}
        <tr>
            <td class="adminRowL" colspan="4">{t w="no_links_found"}</td>
        </tr>
    {/if}
    </table>

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG">&raquo; 
                    {if $form.title.value == ''}
                        {t w="add_link"}
                    {else}
                        {t w="change_link_desc"} {$link.title}
                    {/if}
                </th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
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
