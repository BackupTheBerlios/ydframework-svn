{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $item.older_id or $item.newer_id}
        <div class="screenonly">
            <div class="navigation">
                {if $item.older_id}
                    <div class="alignleft">&laquo; <a href="{$item.older_id|link_item}">{t w="older_post"}</a></div>
                {/if}
                {if $item.newer_id}
                    <div class="alignright"><a href="{$item.newer_id|link_item}">{t w="newer_post"}</a> &raquo;</div>
                {/if}
            </div>
        </div>
    {/if}

    <div class="post">

        <h2 id="post-{$item.id}">
            <a href="{$item|@link_item}" rel="bookmark" title="PermaLink to {$item.title}">{$item.title}</a>
        </h2>

        <div class="entrytext">

            <p>{$item.body|bbcode}</p>

            <a name="more"></a>

            <p>{$item.body_more|bbcode}</p>

            <p class="postmetadata alt">
                <small>
                    {t w="posted_under"} {$item.created|date|lower} {t w="by"} <a href="mailto:{$item.user_email|escape:'hexentity'}">{$item.user_name}</a>
                    {t w="filed_under"} <a href="{$item.category_id|link_category}">{$item.category}</a>.
                    {if $user.name}
                        <br/>
                        <a href="manage/items.php?do=edit&id={$item.id}" target="_blank">{t w="edit"}</a>
                        | <a href="manage/items.php?do=delete&id={$item.id}"
                             onClick="return YDConfirmDelete( '{$item.title|addslashes}' );">{t w="delete"}</a>
                    {/if}
                </small>
            </p>

        </div>

        {if $item.images}
            <a name="images"></a>
            <h3>{$item|@text_num_images} {t w="with"} &#8220;{$item.title}&#8221;</h3>
            <p><table width="450" border="0" cellspacing="0" cellpadding="0">
            {foreach from=$item.images_as_table item="image_row"}
            <tr>
                {foreach from=$image_row item="image"}
                    <td width="33%" align="center" style="vertical-align: middle;">
                        {if $image}
                            <a href="{$uploads_dir}/{$image->relative_path}" rel="lightbox[{$item.id}]" 
                             title="{$image->full_description_html}"><img src="{$image|link_thumb}" alt="{$image->title}"
                             width="{$image->relative_path_m_obj->getWidth()}" height="{$image->relative_path_m_obj->getHeight()}"></a>
                            <br/>
                            <a href="{$uploads_dir}/{$image->relative_path}" rel="lightbox[{$item.id}]"
                             title="{$image->full_description_html}">{$image->title}</a>
                             <br/>&nbsp;
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                {/foreach}
            </tr>
            {/foreach}
            </table></p>
        {/if}

    </div>

    <a name="related"></a>
    {if $related_items}
        <h3>{t w="related_items"} &#8220;{$item.title}&#8221;</h3>
        <ul>
            {foreach from=$related_items item="rel_item"}
                <li>
                    {$rel_item.created|date:'%d %B %Y'|lower}:
                    <a href="{$rel_item|@link_item}">{$rel_item.title}</a>
                    {if $rel_item.num_comments != '0' or $rel_item.num_images != '0'}
                        <span class="postmetadata">({$rel_item|@text_num_images:false}{if $rel_item.num_comments != '0' and $rel_item.num_images != '0'}, {/if}{$rel_item|@text_num_comments:false})</span>
                    {/if}
                </li>
            {/foreach}
        </ul>
    {/if}

    <a name="comment"></a>
    {if $comments}
        <h3>{$item|@text_num_comments:true} {t w="to"} &#8220;{$item.title}&#8221;</h3>
        <ol class="commentlist">
            {foreach from=$comments item="comment"}
                <li class="{cycle values="alt,"}" id="comment-{$comment.id}">
                    <a name="comment-{$comment.id}"></a>
                    <cite>
                        {if $comment.userwebsite}
                            <a href="{$comment.userwebsite}" rel="external nofollow" target="_blank">{$comment.username}</a>
                        {else}
                            {$comment.username}
                        {/if}
                    </cite> {t w="says"}:<br />
                    <small class="commentmetadata">
                        <a href="#comment-{$comment.id}" title="">{$comment.created|date:"%A, %d %b %Y @ %H:%M"|lower}</a>
                        {if $user.name}
                            | <a href="manage/comments.php?do=edit&id={$comment.id}" target="_blank">{t w="edit"}</a>
                            | <a href="manage/comments.php?do=delete&id={$comment.id}"
                                 onClick="return YDConfirmDelete( '{$item.title|addslashes}' );">{t w="delete"}</a>
                            | <a href="manage/comments.php?do=mark_as_spam&id={$comment.id}">{t w="mark_as_spam"}</a>
                        {/if}
                    </small>
                    <p>{$comment.comment|bbcode}</p>
                </li>
            {/foreach}
        </ol>
    {/if}

    <a name="respond"></a>
    <h3 id="respond">{t w="leave_comment"}</h3>

    {$comments_form.tag}

        {if $comments_form.errors_unique_messages}
            <p class="postmetadata alt" style="color: red; text-align: left;">
                <b>{t w="error_comment"}<br/></b>
                {foreach from=$comments_form.errors_unique_messages item="error"}
                    {$error}<br/>
                {/foreach}
            </p>
        {/if}

        {$comments_form.item_id.html}

        <p>{$comments_form.username.html} <small>{$comments_form.username.label_html}</small></p>
        <p>{$comments_form.useremail.html} <small>{$comments_form.useremail.label_html}</small></p>
        <p>{$comments_form.userwebsite.html} <small>{$comments_form.userwebsite.label_html}</small></p>
        <p><small>{$comments_form.security_code.label_html}</small><br/>{$comments_form.security_code.html}</p>

        <p>{$comments_form.comment.html}</p>

        <p>{$comments_form.cmdSubmit.html}</p>

    {$comments_form.endtag}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
