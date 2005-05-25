{include file="__std_header.tpl"}

<div id="content" class="narrowcolumn">

    {if $page}

        <h2 class="pagetitle">{$page.title}</h2>

        <p>
            <div class="entry">{$page.body|bbcode}</div>
        </p>

        <p>
            <small>
                {t w="created_on"} {$page.created|date} {t w="by"} <a href="mailto:{$page.user_email}">{$page.user_name}</a>
                {if $page.modified}
                    <br/>
                    {t w="last_modified_on"} {$page.modified|date}.
                {/if}
            </small>
        </p>

    {else}

        <h2 class="center">{t w="not_found"}</h2>
        <p class="center">{t w="sorry_not_found"}</p>

    {/if}

</div>

{include file="__std_sidebar.tpl"}

{include file="__std_footer.tpl"}
