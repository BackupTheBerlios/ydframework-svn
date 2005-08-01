{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_comments"}
{if $item}&raquo; {$item.title}{/if}
</p>

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
        {if $comments->pages}
            <tr>
                <td class="adminRowR" colspan="4">
                    <p class="subline">
                    {if ! $comments->isFirstPage}
                        <a href="{$comments->getPreviousUrl()}" class="subline">&laquo;</a>
                    {else}
                        &laquo;
                    {/if}
                    |
                    {foreach from=$comments->pages item="page"}
                        {if $page == $comments->page}
                            <b>{$page}</b>
                        {else}
                            <a href="{$comments->getPageUrl($page)}" class="subline">{$page}</a>
                        {/if}
                    {/foreach}
                    |
                    {if ! $comments->isLastPage}
                        <a href="{$comments->getNextUrl()}" class="subline">&raquo;</a>
                    {else}
                        &raquo;
                    {/if}
                    </p>
                </td>
            </tr>
        {/if}
    {/capture}

    <table width="700" cellspacing="0" cellpadding="0" border="0">
    {if $item}
        <tr>
            <th class="adminRowLG" colspan="7">
                &raquo; <a href="items.php?do=edit&id={$item.id}" style="font-weight: bold">{t w="change_item_desc"}</a>
                ({$item.title})
            </th>
        </tr>

        <tr><td colspan="7">&nbsp;</td></tr>
    {/if}
    <tr>
        <th class="adminRowLG" colspan="7">
            &raquo; {t w="a_comments"}
        </th>
    </tr>
    {if $comments->set}
        <tr>
            <th class="adminRowL" width="17%">{t w="date"}</th>
            <th class="adminRowL" width="15%">{t w="author"}</th>
            <th class="adminRowL" width="30%">{t w="parent"}</th>
            <th class="adminRowR" width="18%">{t w="actions"}</th>
        </tr>
        {$browsebar}
        {foreach from=$comments->set item="comment"}
            <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);">
                <td class="adminRowL">{$comment.created|date:'%Y/%m/%d %H:%M'}</td>
                <td class="adminRowL">{$comment.username}</td>
                <td class="adminRowL">{$comment.comment|bbcode|strip_tags|truncate}</td>
                <td class="adminRowR">
                    <a href="../item.php?&id={$comment.item_id}#comment-{$comment.id}" target="_blank">{t w="view"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=edit&id={$comment.id}">{t w="edit"}</a>
                    |
                    <a href="{$YD_SELF_SCRIPT}?do=delete&id={$comment.id}"
                     onClick="return YDConfirmDelete( '{$comment.comment|bbcode|strip_tags|strip|truncate|addslashes}' );">{t w="delete"}</a>
                </td>
            </tr>
        {/foreach}
        {$browsebar}
        <tr>
            <td class="adminRowLNB" colspan="4">
                <p class="subline">{t w="total"}: {$comments->totalRows}</p>
            </td>
        </tr>
    {else}
        <tr>
            <td class="adminRowL" colspan="7">{t w="no_comments_found"}</td>
        </tr>
    {/if}

    {if $item}
        <tr><td colspan="7">&nbsp;</td></tr>
        <tr>
            <th class="adminRowLG" colspan="7">
                &raquo; <a href="items_gallery.php?id={$item.id}" style="font-weight: bold;">{t w="gallery"}</a>
                ({$item|@text_num_images:true})
            </th>
        </tr>
    {/if}
    </table>

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowLG">&raquo; {t w="change_comment_desc"}</th>
                <th class="adminRowLGR">&raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a></th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{t w="item"}</td>
                <td class="adminRowL" width="400"><a href="items.php?do=edit&id={$comment.item_id}"><b>{$comment.item_title}</b></a></td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{t w="posted_from"}</td>
                <td class="adminRowL" width="400">{$comment.userip}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.username.label_html}</td>
                <td class="adminRowC">{$form.username.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.useremail.label_html}</td>
                <td class="adminRowC">{$form.useremail.html}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.userwebsite.label_html}</td>
                <td class="adminRowC">{$form.userwebsite.html}</td>
            </tr>
            <tr>
                <td class="adminRowL" colspan="2">
                    {$form.comment.label_html}
                    <br/>
                    {$form.comment.html}
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
