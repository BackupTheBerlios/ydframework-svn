{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_pages"}</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $pages->pages}
            <tr>
                <td class="adminRowL" colspan="1">
                    <p class="subline">{t w="total"}: {$pages->totalRows}</p>
                </td>
                <td class="adminRowR" colspan="3">
                    <p class="subline">
                    &laquo;
                    {if ! $pages->isFirstPage}
                        <a href="{$pages->getPreviousUrl()}" class="subline">{t w="previous"}</a>
                    {else}
                        {t w="previous"}
                    {/if}
                    |
                    {foreach from=$pages->pages item="page"}
                        {if $page == $pages->page}
                            <b>{$page}</b>
                        {else}
                            <a href="{$pages->getPageUrl($page)}" class="subline">{$page}</a>
                        {/if}
                    {/foreach}
                    |
                    {if ! $pages->isLastPage}
                        <a href="{$pages->getNextUrl()}" class="subline">{t w="next"}</a>
                    {else}
                        {t w="next"}
                    {/if}
                    &raquo;
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    <p><a href="{$YD_SELF_SCRIPT}?do=edit">{t w="add_page"}</a></p>
    {if $pages}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="17%">{t w="date"}</th>
            <th class="adminRowL" width="15%">{t w="name"}</th>
            <th class="adminRowL" width="35%">{t w="title"}</th>
            <th class="adminRowR" width="13%">{t w="actions"}</th>
        </tr>
        {foreach from=$pages->set item="page"}
            <tr>
                <td class="adminRowL">{$page.created|date:'%Y/%m/%d %H:%M'}</td>
                <td class="adminRowL">{$page.user_name}</td>
                <td class="adminRowL">{$page.title}</td>
                <td class="adminRowR">
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$page.id}">{t w="edit"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$page.id}"
                     onClick="return YDConfirmDelete( '{$page.title}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        </table>
    {else}
        <p>{t w="no_pages_found"}</p>
    {/if}

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">
                    {if $form.title.value == ''}
                        {t w="add_page"}
                    {else}
                        {t w="change_page_desc"}
                    {/if}
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.title.label_html}</td>
                <td class="adminRowC" width="400">{$form.title.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2">
                    {$form.body.label_html}
                    <br/>
                    {$form.body.html}
                </td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.created.label_html}</td>
                <td class="adminRowL">{$form.created.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.modified.label_html}</td>
                <td class="adminRowL">{$form.modified.html}</td>
            </tr>
        </table>
        {$form.id.html}
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
