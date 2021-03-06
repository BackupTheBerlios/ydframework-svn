<div id="sidebar">

    <ul>

        <li>
            <h2><a href="{$weblog_link}" title="{$weblog_title}">Home</a></h2>
        </li>

        {if $pages}
            <li>
                <h2>{t w="pages"}</h2>
                <ul>
                    {foreach from=$pages item="page"}
                        <li><a href="{$page|@link_page}" title="{t w="page"}: {$page.title}">{$page.title}</a></li>
                    {/foreach}
                </ul>
            </li>
        {/if}

        <li>
            <h2>{t w="archives"}</h2>
            <ul>
                <li><a href="{$weblog_link_archive}" title="{t w="archives"}">{t w="archives"}</a></li>
                <li><a href="{$weblog_link_archive_gallery}" title="{t w="archives_gallery"}">{t w="archives_gallery"}</a></li>
            </ul>
        </li>

        {if $categories}
            <li>
                <h2>{t w="categories"}</h2>
                <ul>
                    {foreach from=$categories item="category"}
                        <li><a href="{$category|@link_category}"
                            title="{t w="view_posts_filed_under"} {$category.title}">{$category.title}</a>
                            {if $category.num_items != "0"}({$category.num_items}){/if}
                        </li>
                    {/foreach}
                </ul>
            </li>
        {/if}

        {if $links}
            <li>
                <h2>{t w="links"}</h2>
                <ul>
                    {foreach from=$links item="link"}
                        <li><a href="{$link|@link_link}" title="{t w="link_to"} {$link.url}" target="_blank">{$link.title}</a></li>
                    {/foreach}
                </ul>
            </li>
        {/if}

        <li>
            <h2>{if $user.name}{$user.name}{else}{t w="admin"}{/if}</h2>
            <ul>
                {if $user.name}
                    <li><a href="manage/index.php">{t w="a_admin_home"}</a></li>
                    <li><a href="manage/index.php?do=logout">{t w="a_logoff"}</a></li>
                {else}
                    <li><a href="manage/index.php">{t w="login"}</a></li>
                {/if}
            </ul>
        </li>

    </ul>

</div>
