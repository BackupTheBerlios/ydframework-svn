{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $items}

        {foreach from=$items item="item"}
            <div class="post">
                <h2 id="post-{$item.id}">
                    <a href="{$item|@link_item}" rel="bookmark" title="PermaLink to {$item.title}">{$item.title}</a>
                </h2>
                <small>{$item.created|date|lower} {t w="by"} <a href="mailto:{$item.user_email|escape:'hexentity'}">{$item.user_name|lower}</a></small>
                <p>
                    <table width="450" border="0" cellspacing="0" cellpadding="0">
                    {foreach from=$item.images_as_table item="image_row"}
                    <tr>
                        {foreach from=$image_row item="image"}
                            <td width="12%" align="center" valign="middle" height="60">
                                {if $image}
                                    <a href="{$uploads_dir}/{$image->relative_path}" rel="lightbox[{$item.id}]"
                                    title="{$item.title} &raquo; {$image->getBasenameNoExt()}"
                                    ><img src="{$image|link_thumb_small}" alt="{$image->getBaseName()}" width="{$image->relative_path_s_obj->getWidth()}" height="{$image->relative_path_s_obj->getHeight()}"></a>
                                {else}
                                    &nbsp;
                                {/if}
                            </td>
                        {/foreach}
                    </tr>
                    {/foreach}
                </table>
                </p>
                <p class="postmetadata">
                    {t w="posted_in"} <a href="{$item.category_id|link_category}">{$item.category}</a> <strong>|</strong>
                    {if $item.num_images != '0'}
                        <a href="{$item|@link_item_images}">{$item|@text_num_images}</a> <strong>|</strong>
                    {/if}
                    <a href="{$item|@link_item_comment}">{$item|@text_num_comments:true} &#187;</a>
                </p>
            </div>
        {/foreach}

    {else}

        <h2 class="center">{t w="not_found"}</h2>
        <p class="center">{t w="sorry_not_found"}</p>

    {/if}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
