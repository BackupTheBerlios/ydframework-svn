<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    <h3>{$title}</h3>

    {if $formValid}
    
        Welcome to <b>{$form.name.value}</b>!

    {else}
    
        {if $form.errors}
            <p style="color: red"><b>Errors during processing:</b>
            {foreach from=$form.errors item="error"}
                <br>{$error}
            {/foreach}
            </p>
        {/if}

        <form {$form.attributes}>
            <p>
                {$form.name.label}
                <br>
                {$form.name.html}
            </p>
            <p>
                {$form.cmdSubmit.html}
            </p>
        </form>

    {/if}

    <p>[
        <a href="{$YD_SELF_SCRIPT}">try again</a>
        |
        <a href="">other samples</a>
    ]</p>

</body>

</html>
