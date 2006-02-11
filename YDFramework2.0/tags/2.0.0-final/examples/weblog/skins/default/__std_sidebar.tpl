<div id="sidebar">

    <ul>

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
            <h2>{t w="admin"}</h2>
            <ul>
                <li><a href="manage/index.php">{t w="login"}</a></li>
            </ul>
        </li>

    </ul>

</div>
