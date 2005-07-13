{include file="__mng_header.tpl"}

<p class="title">{t w="h_contents"} &raquo; {t w="a_comments"}
{if $item}&raquo; {$item.title}{/if}
</p>

{if $form.errors}
    <p class="error">
        {foreach from=$form.errors item="error"}
            {$error}<br>
        {/foreach}
    </p>
{/if}

{if $YD_ACTION == 'default'}

    {capture assign="browsebar"}
        {if $comments->pages}
            <tr>
                <td class="adminRowL" colspan="1">
                    <p class="subline">{t w="total"}: {$comments->totalRows}</p>
                </td>
                <td class="adminRowR" colspan="3">
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

    {if $comments->set}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <th class="adminRowL" width="17%">{t w="date"}</th>
            <th class="adminRowL" width="15%">{t w="name"}</th>
            <th class="adminRowL" width="30%">{t w="parent"}</th>
            <th class="adminRowR" width="18%">{t w="actions"}</th>
        </tr>
        {$browsebar}
        {foreach from=$comments->set item="comment"}
            <tr>
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
        </table>
    {else}
        <p>{t w="no_comments_found"}</p>
    {/if}

{/if}

{if $YD_ACTION == 'edit'}
    {$form.tag}
        <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <th class="adminRowL" colspan="3">{t w="change_comment_desc"}</th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{t w="item"}</td>
                <td class="adminRowC" width="400"><a href="items.php?do=edit&id={$comment.item_id}"><b>{$comment.item_title}</b></a></td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{t w="posted_from"}</td>
                <td class="adminRowC" width="400">{$comment.userip}</td>
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
