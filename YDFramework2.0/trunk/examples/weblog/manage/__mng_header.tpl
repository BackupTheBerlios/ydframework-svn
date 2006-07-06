<html>

<head>

    <title>{t w="admin"} &raquo; {$weblog_title}</title>

    <link rel="stylesheet" type="text/css" href="manage.css" />

    <script language="JavaScript" src="../tiny_mce/tiny_mce_gzip.php"></script>

    <script language="JavaScript">
    <!--

        {literal}function YDConfirmDelete( name ) {{/literal}
            return confirm( '{t w="confirm_delete"}\n\n"' + name + '"?');
        {literal}}{/literal}

        {literal}function YDConfirmAction( name ) {{/literal}
            return confirm( '{t w="confirm_action"}\n\n"' + name + '"' );
        {literal}}{/literal}

        {literal}function YDRowMouseOver( obj ) {{/literal}
            obj.bgColor = '#EDF3FE';
        {literal}}{/literal}

        {literal}function YDRowMouseOut( obj ) {{/literal}
            obj.bgColor = '#FFFFFF';
        {literal}}{/literal}

        {literal}tinyMCE.init({
            mode : "textareas",
            theme : "advanced",
            language: "{/literal}{$weblog_language}{literal}",
            plugins : "imgselector,fullscreen",
            theme_advanced_buttons1 : "bold,italic,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,undo,redo,separator,link,unlink,code,fullscreen,separator,imgselector",
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_path_location : ""
        });{/literal}

    //-->
    </script>

</head>

<body marginwidth="0" marginheight="0" bottommargin="0" leftmargin="0" rightmargin="0" topmargin="0"
 bgcolor="#FFFFFF" background="images/mng_background.gif">

    <table width="100%" cellspacing="0" cellpadding="18" border="0">
    <tr>
        <td class="topRow">
            <p class="titleTop">{t w="admin"} &raquo; {$weblog_title}</p>
        </td>
        <td class="topRowR">
            <p class="textTop">{$YD_FW_NAMEVERS}</p>
        </td>
    </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="18" border="0">
    <tr>
        <td class="langRowL">
            <p class="langRow">
                {if $user.name}
                    <a href="index.php?do=logout" class="langRow">{t w="logoff"}</a> |
                {/if}
                <a href="../index.php" class="langRow" target="_blank">{t w="view_site"}</a>
            </p>
        </td>
        <td class="langRowR">
            <p class="langRow">
                {if $user.name}
                    {t w="user"}: <b>{$user.name|lower}</b> |
                {/if}
                {t w="server" lower="yes"}:
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
                        &raquo; <a href="dbbackup.php">{t w="a_db_backup"}</a><br/>
                        &raquo; <a href="cache.php">{t w="a_cleanup_cache"}</a><br/>
                    </td>
                </tr>
                <tr>
                    <td class="adminRowL">
                        <b>{t w="h_diagtools"}</b><br/>
                        &raquo; <a href="autoupdate.php">{t w="a_version_info"}</a><br/>
                        &raquo; <a href="serverinfo.php">{t w="a_server_info"}</a><br/>
                    </td>
                </tr>
                {if $keep_stats or $google_analytics}
                    <tr>
                        <td class="adminRowL">
                            <b>{t w="h_statistics"}</b><br/>
                            {if $keep_stats}
                                &raquo; <a href="stats.php">{t w="a_statistics"}</a><br/>
                            {/if}
                            {if $google_analytics}
                                &raquo; <a href="https://www.google.com/analytics/home/" target="_blank">{t w="cfg_google_analytics"}</a><br/>
                            {/if}
                        </td>
                    </tr>
                {/if}
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
