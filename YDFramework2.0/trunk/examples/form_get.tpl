<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    {if $YD_ACTION == 'default'}
        <p><a href="{$YD_SELF_SCRIPT}?do=form">Try the form</a></p>
    {/if}

    {if $YD_ACTION == 'form'}
        {$form}
    {/if}

    <p>
        <a href="{$YD_SELF_SCRIPT}">try again</a>
        |
        <a href="index.php">other samples</a>
    </p>

</body>

</html>
