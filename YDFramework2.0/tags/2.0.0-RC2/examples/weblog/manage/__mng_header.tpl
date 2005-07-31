<html>

<head>

    <title>{t w="admin"} &raquo; {$weblog_title}</title>

    <link rel="stylesheet" type="text/css" href="manage.css" />

    <script language="JavaScript">
    <!--

        {literal}function YDConfirmDelete( img ) {{/literal}
            return confirm( '{t w="confirm_delete"}\n\n"' + img + '"?');
        {literal}}{/literal}

    //-->
    </script>

</head>

<body marginwidth="0" marginheight="0" bottommargin="0" leftmargin="0" rightmargin="0" topmargin="0"
 bgcolor="#FFFFFF" background="images/mng_background.gif">

    <table width="100%" cellspacing="0" cellpadding="18" border="0">
    <tr>
        <td class="topRow" colspan="2">
            <p class="title">{t w="admin"} &raquo; {$weblog_title}</p>
        </td>
    </tr>
    <tr>
        <td class="langRowL">
            <p class="langRow">
                {if $user.name}
                    <a href="index.php?do=logout" class="langRow">{t w="logoff"}</a> |
                    <a href="../index.php" class="langRow" target="_blank">{t w="view_site"}</a>
                {/if}
            </p>
        </td>
        <td class="langRowR">
            <p class="langRow">
                {if $user.name}
                    {t w="user"}: <b>{$user.name|lower}</b> |
                {/if}
                {t w="server"}:
                <a href="http://{$smarty.server.SERVER_NAME}{if $smarty.server.SERVER_PORT != '80'}:{$smarty.server.SERVER_PORT}{/if}" class="langRow" target="_blank"><b>{$smarty.server.SERVER_NAME}{if $smarty.server.SERVER_PORT != '80'}:{$smarty.server.SERVER_PORT}{/if}</b></a>
            </p>
        </td>
    </tr>
    <tr>
        <td class="leftCol">
            {if $user.name}
                <table width="200" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_shortcuts"}</b><br/>
                        &raquo; <a href="index.php">{t w="a_admin_home"}</a><br/>
                        &raquo; <a href="../index.php" target="_blank">{t w="a_view_site"}</a><br/>
                    </td>
                </tr>
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_contents"}</b><br/>
                        &raquo; <a href="items.php">{t w="a_items"}</a><br/>
                        &raquo; <a href="comments.php">{t w="a_comments"}</a><br/>
                        &raquo; <a href="pages.php">{t w="a_pages"}</a><br/>
                        &raquo; <a href="links.php">{t w="a_links"}</a><br/>
                        &raquo; <a href="images.php">{t w="a_images"}</a><br/>
                        &raquo; <a href="categories.php">{t w="a_categories"}</a><br/>
                    </td>
                </tr>
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_global_settings"}</b><br/>
                        &raquo; <a href="users.php">{t w="a_users"}</a><br/>
                        &raquo; <a href="config.php">{t w="a_settings"}</a><br/>
                    </td>
                </tr>
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_maintenance"}</b><br/>
                        &raquo; <a href="cache.php">{t w="a_cleanup_cache"}</a><br/>
                        &raquo; <a href="serverinfo.php">{t w="a_server_info"}</a><br/>
                    </td>
                </tr>
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_statistics"}</b><br/>
                        &raquo; <a href="stats.php">{t w="a_statistics"}</a><br/>
                    </td>
                </tr>
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_logoff"}</b><br/>
                        &raquo; <a href="index.php?do=logout">{t w="a_logoff"}</a><br/>
                    </td>
                </tr>
                </table>
            {/if}
        </td>
        <td align="left" valign="top">

            <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td>
