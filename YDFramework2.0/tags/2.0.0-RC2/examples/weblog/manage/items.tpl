{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_items"}</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $items->pages}
            <tr>
                <td class="adminRowR" colspan="4">
                    <p class="subline">
                    {if ! $items->isFirstPage}
                        <a href="{$items->getPreviousUrl()}" class="subline">&laquo;</a>
                    {else}
                        &laquo;
                    {/if}
                    |
                    {foreach from=$items->pages item="page"}
                        {if $page == $items->page}
                            <b>{$page}</b>
                        {else}
                            <a href="{$items->getPageUrl($page)}" class="subline">{$page}</a>
                        {/if}
                    {/foreach}
                    |
                    {if ! $items->isLastPage}
                        <a href="{$items->getNextUrl()}" class="subline">&raquo;</a>
                    {else}
                        &raquo;
                    {/if}
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    <p><a href="{$YD_SELF_SCRIPT}?do=edit">{t w="add_item"}</a></p>

    {if $items->set}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="17%">{t w="date"}</th>
            <th class="adminRowL" width="15%">{t w="author"}</th>
            <th class="adminRowL" width="26%">{t w="title"}</th>
            <th class="adminRowR" width="22%">{t w="actions"}</th>
        </tr>
        {$browsebar}
        {foreach from=$items->set item="item"}
            <tr>
                <td class="adminRowL">{$item.created|date:'%Y/%m/%d %H:%M'}</td>
                <td class="adminRowL">{$item.user_name}</td>
                <td class="adminRowL">{$item.title}</td>
                <td class="adminRowR">
                    <a href="../item.php?&id={$item.id}" target="_blank">{t w="view"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$item.id}">{t w="edit"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$item.id}"
                     onClick="return YDConfirmDelete( '{$item.title|addslashes}' );">{t w="delete"}</a>
                    <br/>
                    <a href="comments.php?id={$item.id}">{if $item.num_comments > 0}{$item|@text_num_comments:false}{else}<span class="disabled">0 {t w="comments" lower=true}</span>{/if}</a> |
                    <a href="items_gallery.php?id={$item.id}">{if $item.num_images > 0}{$item|@text_num_images:false}{else}<span class="disabled">0 {t w="images" lower=true}</span>{/if}</a>
                </td>
            </tr>
        {/foreach}
        {$browsebar}
        <tr>
            <td class="adminRowLNB" colspan="4">
                <p class="subline">{t w="total"}: {$items->totalRows}</p>
            </td>
        </tr>
        </table>
    {else}
        <p>{t w="no_items_found"}</p>
    {/if}

{/if}

{if $YD_ACTION == 'edit'}

    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG" colspan="3">
                    &raquo;
                    {if $form.title.value == ''}
                        {t w="add_item"}
                    {else}
                        {t w="change_item_desc"} ({$form.title.value})
                    {/if}
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.title.label_html}</td>
                <td class="adminRowC" width="400">{$form.title.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{$form.category_id.label_html}</td>
                <td class="adminRowC" width="400">{$form.category_id.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2">
                    {$form.body.label_html}
                    <br/>
                    {$form.body.html}
                </td>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2">
                    {$form.body_more.label_html}
                    <br/>
                    {$form.body_more.html}
                </td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.created.label_html}</td>
                <td class="adminRowL">{$form.created.html}</td>
            </tr>
            {if $form.title.value != ''}
                <tr><td colspan="7">&nbsp;</td></tr>
                <tr>
                    <th class="adminRowLG" colspan="3">
                        &raquo; <a href="comments.php?id={$item.id}" style="font-weight: bold;">{t w="a_comments"}</a>
                        ({$item|@text_num_comments:true})
                    </th>
                </tr>
                <tr><td colspan="7">&nbsp;</td></tr>
                <tr>
                    <th class="adminRowLG" colspan="3">
                        &raquo; <a href="items_gallery.php?id={$item.id}" style="font-weight: bold;">{t w="gallery"}</a>
                        ({$item|@text_num_images:true})
                    </th>
                </tr>
            {/if}
        </table>
        {$form.id.html}
        <p>{$form._cmdSubmit.html}</p>
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
