<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    <h3>IP authentication check</h3>

    <p>Your IP number is: {$smarty.server.REMOTE_ADDR}</p>

    {if $allowed}
        <p style="color: green;">You are allowed to see this page.</p>
    {else}
        <p style="color: red;">You are NOT allowed to see this page.</p>
    {/if}

    <p>Tip: try accessing this page once with your computername as the hostname,
    and see the difference if you use localhost.</p>

    {if $YD_ACTION == 'test'}
        <p><a href="{$YD_SELF_SCRIPT}">default action</a></p>
    {else}
        <p><a href="{$YD_SELF_SCRIPT}?do=test">test action</a></p>
    {/if}

    <p><a href="index.php">other samples</a></p>

</body>

</html>
