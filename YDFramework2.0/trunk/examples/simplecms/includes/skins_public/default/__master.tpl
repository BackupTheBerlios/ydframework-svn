<html>

<head>

    <title>{$title}</title>

</head>

<body>
    {if $currentScope eq YD_SIMPLECMS_SCOPE_PUBLIC}
        <a href="admin.php" class="langRow">administer site</a>
    {/if}
    {if $currentUser}
        <p>
            {$currentUser.name} | <a href="{$YD_SELF_SCRIPT}?action=logout">{t w="logout"}</a>
        </p>
    {/if}

    {$content}

    <small>{$YD_FW_POWERED_BY}</small>

</body>

</html>