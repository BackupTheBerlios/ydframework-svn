<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    {if $YD_ACTION == 'default'}
        <h3>Entry list</h3>
        <p><a href="{$YD_SELF_SCRIPT}?do=AddEntry">Add a new entry</a></p>

        {if $entries}
            {foreach from=$entries item="entry"}
                <p>
                <b>{$entry.title}</b>
                [ <a href="{$YD_SELF_SCRIPT}?do=DeleteEntry&id={$entry.id}">delete</a> ]
                <br>
                {$entry.body}
                </p>
            {/foreach}
        {else}
            <p>No entries were found.</p>
        {/if}
    {/if}

    {if $YD_ACTION == 'addentry'}
        <h3>Add entry</h3>

        {if $form.errors}
            <p style="color: red"><b>Errors during processing:</b>
            {foreach from=$form.errors item="error"}
                <br>{$error}
            {/foreach}
            </p>
        {/if}

        <form {$form.attributes}>
            <p>
                {$form.title.label}
                <br>
                {$form.title.html}
            </p>
            <p>
                {$form.body.label}
                <br>
                {$form.body.html}
            </p>
            <p>
                {$form.cmdSubmit.html}
            </p>
        </form>


    {/if}

    {if $YD_ACTION == 'deleteentry'}
        <h3>Delete entry</h3>
    {/if}

</body>

</html>
