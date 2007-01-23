<html>

<head>

    <title>{$title}</title>

</head>

<body>
    {if $currentUser}
        <p>
            {$currentUser.name} | <a href="{$YD_SELF_SCRIPT}?action=logout">{t w="logout"}</a>
        </p>
    {/if}

    ##content##

    <small>{$YD_FW_POWERED_BY}</small>

</body>

</html>