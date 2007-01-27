<html>

<head>

    <title>{$YD_SIMPLECMS_NAME}</title>

    <link rel="stylesheet" type="text/css" href="resources/YDSimpleCMS.css" />

</head>

<body marginwidth="0" marginheight="0" bgcolor="#FFFFFF" bottommargin="0" leftmargin="0" rightmargin="0" topmargin="0">

    <table width="100%" height="100%" cellspacing="0" cellpadding="18" border="0">
    <tr>
        <td class="topRow" colspan="2">
            <p class="title">{$YD_SIMPLECMS_NAME}</p>
        </td>
    </tr>
    <tr>
        <td class="langRowL">
            <p class="langRow">
                &nbsp;
            </p>
        </td>
        <td class="langRowR">
            <p class="langRow">
                {if $currentUser}
                    {$currentUser.name}
                    [
                    <a href="{$YD_SELF_SCRIPT}?do=logout" class="langRow">{t w="logout"}</a>
                    |
                    <a href="index.php" class="langRow">{t w="view_site"}</a>
                    ]
                {else}
                    &nbsp;
                {/if}
            </p>
        </td>
    </tr>
    <tr>
        <td class="leftCol">
            {if $currentUser}
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                {foreach from=$adminMenu item="menuItem"}
                <tr>
                    <td class="adminRowL">
                        <p>
                            {if $menuItem.url}
                                <a href="{$menuItem.url}"><b>{$menuItem.title}</b></a>
                            {else}
                                <b>{$menuItem.title}</b>
                            {/if}
                            {foreach from=$menuItem.children item="menuSubItem"}
                                <br/>
                                {if $menuSubItem.url}
                                    <a href="{$menuSubItem.url}">{$menuSubItem.title}</a>
                                {else}
                                    {$menuSubItem.title}
                                {/if}
                            {/foreach}
                        </p>
                    </td>
                </tr>
                {/foreach}
                </table>
            {else}
                &nbsp;
            {/if}
        </td>
        <td align="left" valign="top">
            {$content}
            <p class="subline">
                &nbsp<br/>
                {t w="powered_by"} <a href="{$YD_FW_HOMEPAGE}" class="subline" target="_blank">{$YD_FW_NAME}</a>
                {t w="version"} {$YD_FW_VERSION}.
                <br/>
                {$YD_FW_COPYRIGHT}
            </p>
        </td>
    </tr>
    </table>

</body>

</html>