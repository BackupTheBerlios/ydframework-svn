<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    {if $YD_ACTION == 'test'}

        <h3>Testing {$YD_GET.id}</h3>

        {if $error}
            <p style="color: red"><b>ERROR: {$error}</b></p>
        {else}

            <p>Connected succesfully to database alias <b>{$YD_GET.id}</b>!</p>

            {if $result}
                <table border="1">
                <tr>
                    <td><b>Id</b></td>
                    <td><b>User</b></td>
                    <td><b>Host</b></td>
                    <td><b>db</b></td>
                    <td><b>Command</b></td>
                    <td><b>Time</b></td>
                    <td><b>State</b></td>
                    <td><b>Info</b></td>
                </tr>
                {foreach from=$result item="row"}
                    <tr>
                        <td>{$row.Id}</td>
                        <td>{$row.User}</td>
                        <td>{$row.Host}</td>
                        <td>{$row.db}</td>
                        <td>{$row.Command}</td>
                        <td>{$row.Time}</td>
                        <td>{$row.State}</td>
                        <td>{$row.Info}</td>
                    </tr>
                {/foreach}
                </table>
            {/if}

        {/if}

        <p>[
            <a href="{$YD_SELF_SCRIPT}">go back</a>
            |
            <a href="">other samples</a>
        ]</p>

    {else}

        <h3>Database aliasses</h3>

        {if $dbAliasses}
            <table border="1" width="650" cellpadding="3"  cellspacing="0">
            <tr>
                <td width="200"><b>Alias</b></td>
                <td width="400"><b>Database URL</b></td>
                <td width="50"><b>Actions</b></td>
            </tr>
            {foreach from=$dbAliasses item="dbUrl" key="dbAlias"}
            <tr>
                <td>{$dbAlias}</td>
                <td>{$dbUrl}</td>
                <td>[ <a href="{$YD_SELF_SCRIPT}?{$YD_ACTION_PARAM}=test&id={$dbAlias}">test</a> ]</td>
            </tr>
            {/foreach}
            </table>
        {else}
            <p>No database aliasses defined.</p>
        {/if}

        <p>[
            <a href="{$YD_SELF_SCRIPT}">refresh</a>
            |
            <a href="">other samples</a>
        ]</p>

    {/if}

</body>

</html>
