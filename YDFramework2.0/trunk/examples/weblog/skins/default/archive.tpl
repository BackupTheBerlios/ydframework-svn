{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $items}

        {foreach from=$items key="yearmonth" item="items2"}
            <h2 class="pagetitle">{$yearmonth}</h2>
            {if $items2}
                <p>{foreach from=$items2 item="item"}
                    <small>{$item.created|date:'%d %B'|lower} - </small> <a href="{$item|@link_item}">{$item.title}</a>
                    {if $item.num_comments != '0' or $item.num_images != '0'}
                        <span class="postmetadata">({$item|@text_num_images:false}{if $item.num_comments != '0' and $item.num_images != '0'}, {/if}{$item|@text_num_comments:false})</span>
                    {/if}
                    <br/>
                {/foreach}</p>
            {/if}
        {/foreach}

    {else}

        <h2 class="center">{t w="not_found"}</h2>
        <p class="center">{t w="sorry_not_found"}</p>

    {/if}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
