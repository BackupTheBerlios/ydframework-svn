{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $items}

        <h2 class="pagetitle">{t w="archive_for_the"} '{$category.title}' {t w="category" lower=true}</h2>

        {foreach from=$items item="item"}
            <p>
                <div class="entry">
                    <a href="{$item|@link_item}">{$item.title}</a>
                    {if $item.num_comments != '0' or $item.num_images != '0'}
                        <span class="postmetadata">({$item|@text_num_images:false}{if $item.num_comments != '0' and $item.num_images != '0'}, {/if}{$item|@text_num_comments:false})</span>
                    {/if}
                    <br/>
                    {$item.body|bbcode|strip_tags|truncate:70}
                </div>
                <small>{$item.created|date} {t w="by"} <a href="mailto:{$item.user_email|escape:'hexentity'}">{$item.user_name}</a></small>
            </p>
        {/foreach}

    {else}

        <h2 class="center">{t w="not_found"}</h2>
        <p class="center">{t w="sorry_not_found"}</p>

    {/if}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
