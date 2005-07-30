{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $item.older_id or $item.newer_id}
        <div class="navigation">
            {if $item.older_id}
                <div class="alignleft">&laquo; <a href="{$item.older_id|link_item}">{t w="older_post"}</a></div>
            {/if}
            {if $item.newer_id}
                <div class="alignright"><a href="{$item.newer_id|link_item}">{t w="newer_post"}</a> &raquo;</div>
            {/if}
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
                    {t w="can_leave_comment"}, {t w="or"}
                    <a href="{$item|@link_item}">{t w="trackback"}</a> {t w="from_your_site"}
                </small>
            </p>

        </div>

        {if $item.images}
            <a name="images"></a>
            <h3>{$item|@text_num_images} {t w="with"} &#8220;{$item.title}&#8221;</h3>
            <p><table width="450" border=0 cellspacing=0 cellpadding=0>
            {foreach from=$item.images_as_table item="image_row"}
            <tr>
                {foreach from=$image_row item="image"}
                    <td width="33%" align="center" style="vertical-align: middle;" height="100">
                        {if $image}
                            <a href="item_gallery.php?id={$image->relative_path}"><img src="{$image|link_thumb}" alt="{$image->getBaseName()}"></a>
                        {else}
                            &nbsp;
                        {/if}
                    </td>
                {/foreach}
            <tr/>
            <tr>
                {foreach from=$image_row item="image"}
                    <td width="33%" align="center">
                        {if $image}
                            <p><a href="item_gallery.php?id={$image->relative_path}">{$image->getBasenameNoExt()}</a><br/>&nbsp;</p>
                        {else}
                            &nbsp;
                        {/if}
                     </td>
                {/foreach}
            <tr/>
            {/foreach}
            </table></p>
        {/if}

    </div>

    <a name="comment"></a>
    {if $comments}
        <h3>{$item|@text_num_comments:true} {t w="to"} &#8220;{$item.title}&#8221;</h3>
        <ol class="commentlist">
            {foreach from=$comments item="comment"}
                <li class="{cycle values="alt,"}" id="comment-{$comment.id}">
                    <a name="comment-{$comment.id}" />
                    <cite>
                        {if $comment.userwebsite}
                            <a href="{$comment.userwebsite}" rel="external nofollow" target="_blank">{$comment.username}</a>
                        {else}
                            {$comment.username}
                        {/if}
                    </cite> {t w="says"}:<br />
                    <small class="commentmetadata">
                        <a href="#comment-{$comment.id}" title="">{$comment.created|date:"%A, %d %b %Y @ %H:%M"|lower}</a>
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

        <p>{$comments_form.comment.html}</p>

        <p>{$comments_form.cmdSubmit.html}</p>

    {$comments_form.endtag}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
