<html>

<head>

    <title>{$YD_FW_NAMEVERS}</title>

</head>

<body>

    {if $file}
        <h3>source code: {$file}</h3>
        {$source}
        <p>[ <a href="{$YD_SELF_SCRIPT}">go back</a> ]</p>
    {else}
    
        <h3>{$YD_FW_NAMEVERS}</h3>

        <p>Welcome to the {$YD_FW_NAMEVERS}!</p>

        <p>&nbsp;<br><b>Samples</b></p>

        <table border="1" width="650" cellpadding="3"  cellspacing="0">
        <tr>
            <td><b>Sample</b></td>
            <td width="40%"><b>Source</b></td>
        </tr>
        <tr>
            <td valign="top">
                <a href="phpinfo.php">Showing the PHP information</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=phpinfo.php">phpinfo.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="browserinfo.php">Showing the browser information</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=browserinfo.php">browserinfo.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=browserinfo.tpl">browserinfo.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="htmlmenu1.php">Creating a HTML menu</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=htmlmenu1.php">htmlmenu1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="arrayutil1.php">Array utilities</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=arrayutil1.php">arrayutil1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="fsfile1.php">YDFSFile object</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsfile1.php">fsfile1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="fsdirectory1.php">YDFSDirectory object</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsdirectory1.php">fsdirectory1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="fsimage1.php">YDFSImage object</a>
                <br>
                <a href="fsimage1.php?do=thumbnail1">YDFSImage object - thumbnail 1</a>
                <br>
                <a href="fsimage1.php?do=thumbnail2">YDFSImage object - thumbnail 2</a>
                <br>
                <a href="fsimage1.php?do=thumbnail3">YDFSImage object - thumbnail 3</a>
                <br>
                <a href="fsimage1.php?do=thumbnail4">YDFSImage object - thumbnail 4 (no caching)</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=fsimage1.php">fsimage1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="extending1.php">Extending the YDRequest base class</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=extending1.php">extending1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="database1.php">Database connectivity</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=config.php">config.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database1.php">database1.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=database1.tpl">database1.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="form1.php">Form handling and validation</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form1.php">form1.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=form1.tpl">form1.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="sample1.php">Defining and using action requests</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=sample1.php">sample1.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=sample1.tpl">sample1.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="login/index.php">Using authentication</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/includes/MyLoginRequest.php">login/includes/MyLoginRequest.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/index.php">login/index.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/index.tpl">login/index.tpl</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/login.tpl">login/login.tpl</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/userinfo.php">login/userinfo.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=login/userinfo.tpl">login/userinfo.tpl</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="ydurl1.php">YDUrl object</a>
                <br>
                <a href="ydurl1.php?do=image1">YDUrl object - Image 1</a>
                <br>
                <a href="ydurl1.php?do=image2">YDUrl object - Image 2</a>
                <br>
                <a href="ydurl1.php?do=image3">YDUrl object - Image 3</a>
                <br>
                <a href="ydurl1.php?do=headers">YDUrl object - Headers</a>
                <br>
                <a href="ydurl1.php?do=status">YDUrl object - Status</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=ydurl1.php">ydurl1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="feedcreator1.php">YDFeedCreator object</a>
                <br>
                <a href="feedcreator1.php?do=rss091">YDFeedCreator object - RSS 0.91 output</a>
                <br>
                <a href="feedcreator1.php?do=rss10">YDFeedCreator object - RSS 1.0 output</a>
                <br>
                <a href="feedcreator1.php?do=rss20">YDFeedCreator object - RSS 2.0 output</a>
                <br>
                <a href="feedcreator1.php?do=atom">YDFeedCreator object - Atom output</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=feedcreator1.php">feedcreator1.php</a>
            </td>
        </tr>
        <tr>
            <td valign="top">
                <a href="auth_ipcheck.php">Authentication based on IP numbers</a>
            </td>
            <td valign="top">
                <a href="{$YD_SELF_SCRIPT}?do=source&id=auth_ipcheck.php">auth_ipcheck.php</a>
                <br>
                <a href="{$YD_SELF_SCRIPT}?do=source&id=auth_ipcheck.tpl">auth_ipcheck.tpl</a>
            </td>
        </tr>
        </table>

        <p>&nbsp;<br><b>Documentation</b></p>

        <p>The documentation for the {$YD_FW_NAME} can be found in the
        <a href="../YDFramework2/doc/index.html">YDFramework2/docs</a> directory
        of the download.</p>
    
    {/if}

</body>

</html>
