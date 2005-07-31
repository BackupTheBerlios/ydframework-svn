{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $items}

        {foreach from=$items item="item"}
            <div class="post">
                <h2 id="post-{$item.id}">
                    <a href="{$item|@link_item}" rel="bookmark" title="PermaLink to {$item.title}">{$item.title}</a>
                </h2>
                <small>
                    {$item.created|date|lower} {t w="by"} <a href="mailto:{$item.user_email|escape:'hexentity'}">{$item.user_name|lower}</a>
                    {if $user.name}
                        | <a href="manage/items.php?do=edit&id={$item.id}" target="_blank">{t w="edit"}</a>
                        | <a href="manage/items.php?do=delete&id={$item.id}"
                             onClick="return YDConfirmDelete( '{$item.title|addslashes}' );">{t w="delete"}</a>
                    {/if}
                </small>
                <div class="entry">
                    {$item.body|bbcode}
                    {if $item.body_more}<a href="{$item|@link_item}#more">&raquo; {t w="more"}</a>{/if}
                </div>
                <p class="postmetadata">
                    {t w="posted_in"} <a href="{$item.category_id|link_category}">{$item.category}</a> <strong>|</strong>
                    {if $item.num_images != '0'}
                        <a href="{$item|@link_item_images}">{$item|@text_num_images}</a> <strong>|</strong>
                    {/if}
                    <a href="{$item|@link_item_comment}">{$item|@text_num_comments:true} &#187;</a>
                </p>
            </div>
        {/foreach}

        {if $old_items}
            <div class="post">
                <h2>{t w="older_items"}</h2>
                <ul>
                    {foreach from=$old_items item="old_item"}
                        <li>
                            {$old_item.created|date:'%d %B %Y'|lower}:
                            <a href="{$old_item|@link_item}">{$old_item.title}</a>
                            {if $old_item.num_comments != '0' or $old_item.num_images != '0'}
                                <span class="postmetadata">({$old_item|@text_num_images:false}{if $old_item.num_comments != '0' and $old_item.num_images != '0'}, {/if}{$old_item|@text_num_comments:false})</span>
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}

        <div class="navigation">
            <div class="alignleft"></div>
            <div class="alignright"></div>
        </div>

    {else}

        <h2 class="center">{t w="not_found"}</h2>
        <p class="center">{t w="sorry_not_found"}</p>

    {/if}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
