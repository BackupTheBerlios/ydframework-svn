<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

</head>

<body>

    <h3>YDFramework2 constants</h3>

    <table width="100%" border="1">
    <tr>
        <td width="30%"><b>Name</b></td>
        <td width="70%"><b>Value</b></td>
    </tr>
    <tr>
        <td valign="top">YD_FW_NAME</td>
        <td><?= $YD_FW_NAME ?></td>
    </tr>
    <tr>
        <td valign="top">YD_FW_VERSION</td>
        <td><?= $YD_FW_VERSION ?></td>
    </tr>
    <tr>
        <td valign="top">YD_FW_NAMEVERS</td>
        <td><?= $YD_FW_NAMEVERS ?></td>
    </tr>
    <tr>
        <td valign="top">YD_FW_HOMEPAGE</td>
        <td><?= $YD_FW_HOMEPAGE ?></td>
    </tr>
    <tr>
        <td valign="top">YD_SELF_SCRIPT</td>
        <td><?= $YD_SELF_SCRIPT ?></td>
    </tr>
    <tr>
        <td valign="top">YD_SELF_URI</td>
        <td><?= $YD_SELF_URI ?></td>
    </tr>
    <tr>
        <td valign="top">YD_ACTION_PARAM</td>
        <td><?= $YD_ACTION_PARAM ?></td>
    </tr>
    <tr>
        <td valign="top">YD_ENV</td>
        <td><pre><? var_dump( $YD_ENV ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_COOKIE</td>
        <td><pre><? var_dump( $YD_COOKIE ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_GET</td>
        <td><pre><? var_dump( $YD_GET ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_POST</td>
        <td><pre><? var_dump( $YD_POST ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_FILES</td>
        <td><pre><? var_dump( $YD_FILES ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_REQUEST</td>
        <td><pre><? var_dump( $YD_REQUEST ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_SESSION</td>
        <td><pre><? var_dump( $YD_SESSION ) ?></pre></td>
    </tr>
    <tr>
        <td valign="top">YD_GLOBALS</td>
        <td><pre><? var_dump( $YD_GLOBALS ) ?></pre></td>
    </tr>
    </table>

    <p>[ <a href="index.php">other samples</a> ]</p>

</body>

</html>
