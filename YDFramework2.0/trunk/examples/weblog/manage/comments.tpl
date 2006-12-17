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

    <table width="700" cellspacing="0" cellpadding="0" border="0">
    {if $item}
        <tr>
            <th class="adminRowLG" colspan="7">
                &raquo; <a href="items.php?do=edit&id={$item.id}" style="font-weight: bold">{t w="change_item_desc"}</a>
                ({$item.title})
                &nbsp;
                <a href="../item.php?id={$item.id}" target="_blank"><img src="images/more_details.gif" border="0" /></a>
            </th>
        </tr>

        <tr><td colspan="7">&nbsp;</td></tr>
    {/if}
    <tr>
        <th class="adminRowLG" colspan="8">
            {if $item}
                &raquo; {t w="a_comments"}
            {else}
                &raquo; <a href="{$YD_SELF_SCRIPT}">
                    {if $filter == 'no_spam'}<b>{/if}{t w="a_comments"}{if $filter == 'no_spam'}</b>{/if}</a>
                | <a href="{$YD_SELF_SCRIPT}?filter=spam">
                    {if $filter == 'spam'}<b>{/if}{t w="a_comments_spam"}{if $filter == 'spam'}</b>{/if}</a>
            {/if}
        </th>
    </tr>
    {if $comments->set}
        {if $filter == 'no_spam'}
            <tr>
                <th class="adminRowL" width="17%">{t w="date"}</th>
                <th class="adminRowL" width="15%">{t w="author"}</th>
                <th class="adminRowL" width="33%">{t w="comment"}</th>
                <th class="adminRowR" width="15%">{t w="actions"}</th>
            </tr>
            <tr><td class="adminRowR" colspan="5">{$comments->getBrowseBar()}</td></tr>
            {foreach from=$comments->set item="comment"}
                <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);" {if $comment.item_is_draft eq '1'}style="color: gray"{/if}>
                    <td class="adminRowL">
                        {if $comment.item_is_draft eq '1'}<i>{/if}
                        {$comment.created|date:'%Y/%m/%d %H:%M'}
                        {if $comment.item_is_draft eq '1'}</i>{/if}
                    </td>
                    <td class="adminRowL">
                        {if $comment.item_is_draft eq '1'}<i>{/if}
                        {$comment.username}
                        {if $comment.item_is_draft eq '1'}</i>{/if}
                    </td>
                    <td class="adminRowL">
                        {*{if $comment.item_is_draft eq '1'}<i>{/if}
                        <a href="{$YD_SELF_SCRIPT}?do=edit&id={$comment.id}">{$comment.comment|bbcode|strip_tags|truncate}</a>
                        {if $comment.item_title}
                            <br/>
                            {t w="item"}: {$comment.item_title}
                        {/if}
                        {if $comment.item_is_draft eq '1'}</i>{/if}*}
                        {if $comment.item_is_draft eq '1'}<i>{/if}
                        <a href="{$YD_SELF_SCRIPT}?do=edit&id={$comment.id}">{$comment.item_title}</a>
                        <br/>
                        {$comment.comment|bbcode|strip_tags|truncate}
                        {if $comment.item_is_draft eq '1'}</i>{/if}
                    </td>
                    <td class="adminRowR">
                        <a href="../item.php?id={$comment.item_id}#comment-{$comment.id}" target="_blank">{t w="view"}</a>
                        |
                        <a href="{$YD_SELF_SCRIPT}?do=edit&id={$comment.id}">{t w="edit"}</a>
                        <br/>
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$comment.id}"
                         onClick="return YDConfirmDelete( '{$comment.comment|bbcode|strip_tags|strip|truncate|addslashes}' );">{t w="delete"}</a>
                        |
                        {if $comment.is_spam}
                            <a href="{$YD_SELF_SCRIPT}?do=mark_as_not_spam&id={$comment.id}">{t w="mark_as_not_spam"}</a>
                        {else}
                            <a href="{$YD_SELF_SCRIPT}?do=mark_as_spam&id={$comment.id}">{t w="mark_as_spam"}</a>
                        {/if}
                        <br/>
                        {t w="weight"}: {$comment.comment|strlen}
                    </td>
                </tr>
            {/foreach}
            <tr><td class="adminRowR" colspan="5">{$comments->getBrowseBar()}</td></tr>
            <tr>
                <td class="adminRowLNB" colspan="5">
                    <p class="subline">{t w="total"}: {$comments->totalRows}</p>
                </td>
            </tr>
        {else}
            <tr><td class="adminRowL" colspan="5">
                <i>{t w="spam_delete_desc"}. <a href="{$YD_SELF_SCRIPT}?do=emptySpam"
                 onClick="return confirm( '{t w="confirm_empty_spam"}' );">{t w="empty_spam_now"}</a></i>
            </td></tr>
            <tr>
                <th class="adminRowL" width="25%">{t w="date"}</th>
                <th class="adminRowL" width="15%">{t w="weight"}</th>
                <th class="adminRowL" width="15%">{t w="author"}</th>
                <th class="adminRowL" width="15%">{t w="ip_number"}</th>
                <th class="adminRowR" width="30%">{t w="actions"}</th>
            </tr>
            <tr><td class="adminRowR" colspan="5">{$comments->getBrowseBar()}</td></tr>
            {foreach from=$comments->set item="comment"}
                <tr onMouseOver="YDRowMouseOver(this);" onMouseOut="YDRowMouseOut(this);" {if $comment.item_is_draft eq '1'}style="color: gray"{/if}>
                    <td class="adminRowL">
                        {$comment.created|date:'%Y/%m/%d %H:%M:%S'}
                    </td>
                    <td class="adminRowL">
                        {$comment.comment|strlen}
                    </td>
                    <td class="adminRowL">
                        {$comment.username}
                    </td>
                    <td class="adminRowL">
                        {$comment.userip}
                    </td>
                    <td class="adminRowR">
                        <a href="{$YD_SELF_SCRIPT}?do=edit&id={$comment.id}">{t w="view"}</a>
                        |
                        <a href="{$YD_SELF_SCRIPT}?do=delete&id={$comment.id}"
                         onClick="return YDConfirmDelete( '{$comment.comment|bbcode|strip_tags|strip|truncate|addslashes}' );">{t w="delete"}</a>
                        |
                        {if $comment.is_spam}
                            <a href="{$YD_SELF_SCRIPT}?do=mark_as_not_spam&id={$comment.id}">{t w="mark_as_not_spam"}</a>
                        {else}
                            <a href="{$YD_SELF_SCRIPT}?do=mark_as_spam&id={$comment.id}">{t w="mark_as_spam"}</a>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            <tr><td class="adminRowR" colspan="5">{$comments->getBrowseBar()}</td></tr>
            <tr>
                <td class="adminRowLNB" colspan="5">
                    <p class="subline">{t w="total"}: {$comments->totalRows}</p>
                </td>
            </tr>
        {/if}
    {else}
        <tr>
            <td class="adminRowL" colspan="7">
                {if $filter == 'no_spam'}
                    {t w="no_comments_found"}
                {else}
                    {t w="no_spam_comments_found"}
                {/if}
            </td>
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
                <th class="adminRowLGR">
                    {if $comment.is_spam}
                        &raquo; <a href="{$YD_SELF_SCRIPT}?filter=spam"><b>{t w="back"}</b></a>
                    {else}
                        &raquo; <a href="{$YD_SELF_SCRIPT}"><b>{t w="back"}</b></a>
                    {/if}
                </th>
            </tr>
            <tr>
                <td class="adminRowL" width="300">{t w="item"}</td>
                <td class="adminRowL" width="400"><a href="items.php?do=edit&id={$comment.item_id}"><b>{$comment.item_title}</b></a></td>
            </tr>
            <tr>
                <td class="adminRowL">{t w="posted_from"}</td>
                <td class="adminRowL">{$comment.userip}</td>
            </tr>
            <tr>
                <td class="adminRowL" width="300">User Agent</td>
                <td class="adminRowL" width="400">{$comment.useragent|default:'-'}</td>
            </tr>
            <tr>
                <td class="adminRowL">Request URI</td>
                <td class="adminRowL">{$comment.userrequrl|default:'-'}</td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.username.label_html}</td>
                <td class="adminRowL">
                    {if $comment.is_spam}
                        {$comment.username|default:'-'}
                    {else}
                        {$form.username.html}
                    {/if}
                </td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.useremail.label_html}</td>
                <td class="adminRowL">
                    {if $comment.is_spam}
                        {$comment.useremail|default:'-'}
                    {else}
                        {$form.useremail.html}
                    {/if}
                </td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.userwebsite.label_html}</td>
                <td class="adminRowL">
                    {if $comment.is_spam}
                        {$comment.userwebsite|default:'-'}
                    {else}
                        {$form.userwebsite.html}
                    {/if}
                </td>
            </tr>
            <tr>
                {if $comment.is_spam}
                    <td class="adminRowL">{$form.comment.label_html}</td>
                    <td class="adminRowL">
                        {$comment.comment|htmlentities|default:'-'}
                    </td>
                {else}
                    <td class="adminRowL" colspan="2">
                        {$form.comment.label_html}
                        <br/>
                        {$form.comment.html}
                    </td>
                {/if}
            </tr>
            <tr>
                <td class="adminRowL">{$form.created.label_html}</td>
                <td class="adminRowL">
                    {if $comment.is_spam}
                        {$comment.created|date:'%Y/%m/%d %H:%M:%S'}
                    {else}
                        {$form.created.html}
                    {/if}
                </td>
            </tr>
            <tr>
                <td class="adminRowL">{$form.is_spam.label_html}</td>
                <td class="adminRowL">
                    {if $comment.is_spam}
                        <a href="{$YD_SELF_SCRIPT}?do=mark_as_not_spam&id={$comment.id}">{t w="mark_as_not_spam"}</a>
                    {else}
                        {$form.is_spam.html}
                    {/if}
                </td>
            </tr>
            {if $comment.is_spam}
                <tr>
                    <td class="adminRowL">{t w="weight"}</td>
                    <td class="adminRowL">
                        {$comment.comment|strlen}
                    </td>
                </tr>
            {/if}
            <tr>
                <td class="adminRowL" colspan="2" style="border: 0px;">
                    {$form._cmdSubmit.html}
                    {$form._cmdDelete.html}
                </td>
            </tr>
        </table>
        {$form.id.html}
    {$form.endtag}
{/if}

{include file="__mng_footer.tpl"}
