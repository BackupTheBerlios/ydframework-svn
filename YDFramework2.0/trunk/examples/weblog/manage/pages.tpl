{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_pages"}</p>

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
        {if $pages->pages}
            <tr>
                <td class="adminRowR" colspan="4">
                    <p class="subline">
                    {if ! $pages->isFirstPage}
                        <a href="{$pages->getPreviousUrl()}" class="subline">&laquo;</a>
                    {else}
                        &laquo;
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
                        <a href="{$pages->getNextUrl()}" class="subline">&raquo;</a>
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
        <th colspan="3" class="adminRowLG">&raquo; {t w="a_pages"}</td>
        <th class="adminRowLGR">
            <a href="items.php?do=edit"><img src="images/icon_add.gif" border="0" /></a>
            <a href="items.php?do=edit"><b>{t w="add_page"}</b></a>
        </td>
    </tr>
    {if $pages}
        <tr>
            <th class="adminRowL" width="17%">{t w="date"}</th>
            <th class="adminRowL" width="15%">{t w="author"}</th>
            <th class="adminRowL" width="30%">{t w="title"}</th>
            <th class="adminRowR" width="18%">{t w="actions"}</th>
        </tr>
        {foreach from=$pages->set item="page"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$page.created|date:'%Y/%m/%d %H:%M'}</td>
                <td class="adminRowL">{$page.user_name}</td>
                <td class="adminRowL">{$page.title}</td>
                <td class="adminRowR">
                    <a href="../page.php?&id={$page.id}" target="_blank">{t w="view"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$page.id}">{t w="edit"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$page.id}"
                     onClick="return YDConfirmDelete( '{$page.title|addslashes}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        <tr>
            <td class="adminRowLNB" colspan="4">
                <p class="subline">{t w="total"}: {$pages->totalRows}</p>
            </td>
        </tr>
    {else}
        <tr>
            <td class="adminRowL" colspan="4">{t w="no_pages_found"}</td>
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
                        {t w="add_page"}
                    {else}
                        {t w="change_page_desc"}
                    {/if}
                </th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
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
        </table>
        {$form.id.html}
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
