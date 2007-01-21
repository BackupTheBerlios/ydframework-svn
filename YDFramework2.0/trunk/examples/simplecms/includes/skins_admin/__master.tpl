<html>

<head>

    <title>{$title}</title>

    {literal}<style>
        td { vertical-align: top; }
    </style>{/literal}

</head>

<body>

    <table width="100%" border="1">
    {if $currentUser}
        <tr>
            <td colspan="2">
                {$currentUser.name} | <a href="{$YD_SELF_SCRIPT}?module=admin&action=logout">{t w="logout"}</a>
            </td>
        </tr>
    {/if}
    <tr>
        <td width="240">
            {if $currentUser}
                {foreach from=$adminMenu item="menuItem"}
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
                {/foreach}
            {/if}
        </td>
        <td>
            ##content##
        </td>
    </tr>
    </table>

</body>

</html>