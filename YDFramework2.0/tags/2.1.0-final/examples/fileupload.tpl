<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    <h3>Upload test page</h3>

    {if $formValid}

        <p>The file <b>{$filename} in {$path}</b>
        ({$filesize|fmtfilesize})
        was uploaded successfully!</p>
        
        <p>Extension is '{$ext}'</p>

    {else}

        {$form_html}

    {/if}

    <p>
        <a href="{$YD_SELF_SCRIPT}">try again</a>
        |
        <a href="index.php">other samples</a>
    </p>

</body>

</html>
