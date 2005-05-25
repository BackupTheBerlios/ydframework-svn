<html>

<head>

    <title>{$YD_FW_NAME} Weblog - Installer</title>

    <link rel="stylesheet" type="text/css" href="manage/manage.css" />

</head>

<body marginwidth="0" marginheight="0" bottommargin="0" leftmargin="0" rightmargin="0" topmargin="0" bgcolor="#FFFFFF">

    <table width="100%" cellspacing="0" cellpadding="18" border="0">
    <tr>
        <td class="topRow" colspan="2">
            <p class="title">{$YD_FW_NAME} - Installer</p>
        </td>
    </tr>
    <tr>
        <td class="langRowL">
            <p class="langRow">
                server:
                <a href="http://{$smarty.server.SERVER_NAME}{if $smarty.server.SERVER_PORT != '80'}:{$smarty.server.SERVER_PORT}{/if}" class="langRow" target="_blank"><b>{$smarty.server.SERVER_NAME}{if $smarty.server.SERVER_PORT != '80'}:{$smarty.server.SERVER_PORT}{/if}</b></a>
            </p>
        </td>
    </tr>
    <tr>
        <td align="left" valign="top">

            <table width="700" cellspacing="0" cellpadding="0" border="0">
            <tr><td>

                {if $YD_ACTION == 'default'}

                    <p>Welcome to the {$YD_FW_NAME} Weblog application. Using this script, you can install this application on your webserver.</p>
                    
                    <p>If you see this screen, you correctly copied over the files needed for the installation.</p>
                    
                    <p>Please fill in the form below and then click on install to get the software installed.</p>

                    {if $form.errors}
                        <p class="error">
                            {foreach from=$form.errors item="error"}
                                {$error}<br>
                            {/foreach}
                        </p>
                    {/if}

                    {$form.tag}

                        <table width="700" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <th colspan="3" class="adminRowL">Database settings</td>
                            </tr>
                            <tr>
                                <td class="adminRowL" width="300">{$form.db_host.label_html}</td>
                                <td class="adminRowL" width="400">{$form.db_host.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.db_name.label_html}</td>
                                <td class="adminRowL">{$form.db_name.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.db_user.label_html}</td>
                                <td class="adminRowL">{$form.db_user.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.db_pass.label_html}</td>
                                <td class="adminRowL">{$form.db_pass.html}</td>
                            </tr>
                        </table>
                        <table width="700" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <th colspan="3" class="adminRowL">&nbsp;<br/>Weblog settings</td>
                            </tr>
                            <tr>
                                <td class="adminRowL" width="300">{$form.weblog_title.label_html}</td>
                                <td class="adminRowL" width="400">{$form.weblog_title.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.weblog_description.label_html}</td>
                                <td class="adminRowL">{$form.weblog_description.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.weblog_skin.label_html}</td>
                                <td class="adminRowL">{$form.weblog_skin.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.weblog_language.label_html}</td>
                                <td class="adminRowL">{$form.weblog_language.html}</td>
                            </tr>
                        </table>
                        <table width="700" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <th class="adminRowL" colspan="3">&nbsp;<br/>Initial user</th>
                            </tr>
                            <tr>
                                <td class="adminRowL" width="300">{$form.name.label_html}</td>
                                <td class="adminRowC" width="400">{$form.name.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.email.label_html}</td>
                                <td class="adminRowL">{$form.email.html}</td>
                            </tr>
                            <tr>
                                <td class="adminRowL">{$form.password.label_html}</td>
                                <td class="adminRowL">{$form.password.html}</td>
                            </tr>
                        </table>
                        <p>{$form._cmdSubmit.html}</p>
                    {$form.endtag}

                {/if}

                {if $YD_ACTION == 'finish'}

                    <p class="title">Congratulations!</p>

                    <p>You successfully installed the {$YD_FW_NAME} Weblog application.</p>

                    <p><a href="index.php" target="_blank">View your weblog</a></p>
                    <p><a href="manage/index.php" target="_blank">Login to the administration part of your weblog</a></p>

                    <p><font color="red"><b>You need to remove the install.php and install.tpl files for security reasons!</b></font></p>

                {/if}

                <p class="subline">
                    &nbsp;<br/>
                    Powered by <a href="{$YD_FW_HOMEPAGE}" class="subline" target="_blank">{$YD_FW_NAME}</a>.
                    <br/>
                    {$YD_FW_COPYRIGHT}
                </p>

            </td></tr></table>

        </td>
    </tr>
    </table>

</body>

</html>

